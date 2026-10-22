"""
Ресинхрон каталога товаров МойСклад в локальный кеш `product_images`.

Логика:
- Идём постранично по `/entity/product` (с expand=productFolder)
- Делаем upsert по UUID: обновляем имя/код/артикул/папку/last_seen_in_ms_at
- Не трогаем `images` — пользовательские картинки сохраняются.

Очистка осиротевших картинок: товары, не виденные в МС более N дней,
теряют файлы и поле images очищается. Сама запись остаётся (на случай возврата).
"""

from __future__ import annotations

import shutil
from collections.abc import Callable
from datetime import datetime, timedelta, timezone
from pathlib import Path
from typing import Any

from sqlalchemy import func, text
from sqlalchemy.dialects.postgresql import insert
from sqlalchemy.orm import Session

from app.config import settings
from app.logging_setup import get_logger
from app.models import ProductImages
from app.services.moysklad import MoySkladClient, MoySkladError

log = get_logger("app.products_sync")

# Параметры по умолчанию
SYNC_PAGE_LIMIT = 1000  # МС позволяет до 1000 на /entity/product
ORPHAN_THRESHOLD_DAYS = 30


ProgressCb = Callable[[dict[str, Any]], None]


def _emit(cb: ProgressCb | None, **payload: Any) -> None:
    if cb:
        cb(payload)


def _folder_uuid_from_row(r: dict[str, Any]) -> str | None:
    pf = r.get("productFolder") or {}
    fuid = pf.get("id") or ""
    if not fuid:
        href = (pf.get("meta") or {}).get("href") or ""
        if href:
            fuid = href.rstrip("/").split("/")[-1]
    return fuid or None


def resync_products_from_ms(
    db: Session,
    client: MoySkladClient,
    on_progress: ProgressCb | None = None,
    page_limit: int = SYNC_PAGE_LIMIT,
) -> dict[str, int]:
    """
    Прогоняет весь каталог /entity/product, делает UPSERT в product_images.
    Не трогает поле images у существующих записей.

    Возвращает {"scanned", "inserted_or_updated", "elapsed_ms"}.
    """
    import time as _t

    started = _t.monotonic()
    offset = 0
    scanned = 0
    upserted = 0

    log.info("resync_products: start (page_limit=%d)", page_limit)
    _emit(on_progress, phase="sync", message="Синхронизация каталога товаров…")

    while True:
        try:
            data = client.list_products_page(offset, page_limit, None)
        except MoySkladError as e:
            log.error("resync_products: MS error at offset=%d: %s", offset, e)
            raise

        rows = data.get("rows") or []
        if not rows:
            break

        now = datetime.now(timezone.utc)
        values: list[dict[str, Any]] = []
        for r in rows:
            uid = (r.get("id") or "").strip()
            if not uid:
                continue
            values.append(
                {
                    "product_uuid": uid,
                    "product_name": r.get("name") or None,
                    "product_code": (r.get("code") or "")[:256] or None,
                    "product_article": (r.get("article") or "")[:256] or None,
                    "folder_uuid": _folder_uuid_from_row(r),
                    "images": [],
                    "last_seen_in_ms_at": now,
                }
            )

        if values:
            stmt = insert(ProductImages.__table__).values(values)
            stmt = stmt.on_conflict_do_update(
                index_elements=[ProductImages.__table__.c.product_uuid],
                set_={
                    "product_name": stmt.excluded.product_name,
                    "product_code": stmt.excluded.product_code,
                    "product_article": stmt.excluded.product_article,
                    "folder_uuid": stmt.excluded.folder_uuid,
                    "last_seen_in_ms_at": stmt.excluded.last_seen_in_ms_at,
                    "updated_at": now,
                },
            )
            db.execute(stmt)
            db.commit()
            upserted += len(values)

        scanned += len(rows)
        _emit(
            on_progress,
            phase="sync",
            current=scanned,
            message=f"Синхронизировано: {scanned}",
        )

        if len(rows) < page_limit:
            break
        offset += page_limit

    elapsed_ms = int((_t.monotonic() - started) * 1000)
    log.info(
        "resync_products: done scanned=%d upserted=%d in %dms",
        scanned,
        upserted,
        elapsed_ms,
    )
    return {
        "scanned": scanned,
        "inserted_or_updated": upserted,
        "elapsed_ms": elapsed_ms,
    }


def cleanup_orphan_images(
    db: Session,
    threshold_days: int = ORPHAN_THRESHOLD_DAYS,
    dry_run: bool = False,
) -> dict[str, int]:
    """
    Удаляет файлы и очищает images у товаров, которых не было в МС
    более threshold_days дней (по last_seen_in_ms_at).

    Возвращает {"orphans", "removed_files", "freed_bytes"}.
    """
    cutoff = datetime.now(timezone.utc) - timedelta(days=threshold_days)
    rows = (
        db.query(ProductImages)
        .filter(
            ProductImages.last_seen_in_ms_at.isnot(None),
            ProductImages.last_seen_in_ms_at < cutoff,
            func.jsonb_array_length(ProductImages.images) > 0,
        )
        .all()
    )

    removed_files = 0
    freed_bytes = 0
    media_root = Path(settings.media_dir)

    for r in rows:
        prod_dir = media_root / r.product_uuid
        if prod_dir.exists():
            for fn in list(r.images or []):
                fp = prod_dir / fn
                try:
                    if fp.exists():
                        freed_bytes += fp.stat().st_size
                        if not dry_run:
                            fp.unlink(missing_ok=True)
                        removed_files += 1
                except Exception as e:
                    log.warning(
                        "cleanup: cannot remove %s: %s", fp, e
                    )
            if not dry_run:
                try:
                    if not any(prod_dir.iterdir()):
                        shutil.rmtree(prod_dir, ignore_errors=True)
                except Exception:
                    pass

        if not dry_run:
            r.images = []

    if not dry_run:
        db.commit()

    log.info(
        "cleanup_orphan_images: orphans=%d removed_files=%d freed=%d bytes "
        "threshold=%dd dry_run=%s",
        len(rows),
        removed_files,
        freed_bytes,
        threshold_days,
        dry_run,
    )
    return {
        "orphans": len(rows),
        "removed_files": removed_files,
        "freed_bytes": freed_bytes,
    }
