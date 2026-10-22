"""
Управление изображениями товаров.
Изображения хранятся локально: {media_dir}/{product_uuid}/{filename}
и отдаются через /media/{product_uuid}/{filename}.
"""

from __future__ import annotations

import uuid as _uuid
from pathlib import Path
from typing import Any

from fastapi import APIRouter, Depends, File, HTTPException, Query, UploadFile
from sqlalchemy import func, or_
from sqlalchemy.orm import Session

from app.config import settings
from app.database import get_db
from app.deps import get_ms_client
from app.logging_setup import get_logger
from app.models import ProductImages
from app.schemas import ProductStatsOut
from app.services.moysklad import MoySkladError
from app.services.products_sync import (
    ORPHAN_THRESHOLD_DAYS,
    cleanup_orphan_images,
)
from app.services.yml_patch import apply_image_change_to_feeds

log = get_logger("app.products")

router = APIRouter(prefix="/products", tags=["products"])

ALLOWED_MIME = {"image/jpeg", "image/png", "image/webp", "image/gif"}
MAX_FILE_SIZE = 10 * 1024 * 1024  # 10 MB


# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

def _media_dir(product_uuid: str) -> Path:
    return Path(settings.media_dir) / product_uuid


def _public_url(product_uuid: str, filename: str) -> str:
    base = settings.public_base_url.rstrip("/")
    return f"{base}/media/{product_uuid}/{filename}"


def _row_to_dict(
    row: ProductImages,
    yml_patch_result: dict | None = None,
) -> dict[str, Any]:
    images = list(row.images or [])
    out: dict[str, Any] = {
        "product_uuid": row.product_uuid,
        "product_name": row.product_name or "",
        "product_code": row.product_code or "",
        "product_article": row.product_article or "",
        "folder_uuid": row.folder_uuid or "",
        "images": images,
        "image_urls": [_public_url(row.product_uuid, fn) for fn in images],
    }
    if yml_patch_result is not None:
        out["yml_patch"] = yml_patch_result
    return out


def _get_or_create(db: Session, product_uuid: str) -> ProductImages:
    row = db.query(ProductImages).filter(ProductImages.product_uuid == product_uuid).first()
    if not row:
        row = ProductImages(product_uuid=product_uuid, images=[])
        db.add(row)
        db.flush()
    return row


# ---------------------------------------------------------------------------
# Endpoints — product listing / search
# ---------------------------------------------------------------------------

DEFAULT_PAGE_SIZE = 200
MAX_PAGE_SIZE = 1000


@router.get("")
def list_products(
    q: str = Query(default="", description="Поиск по коду/артикулу/названию (локально)"),
    folder_uuid: str = Query(default="", description="Фильтр по папке (UUID)"),
    has_images: bool | None = Query(default=None, description="true — только с фото; false — без фото; не задано — все"),
    limit: int = Query(default=DEFAULT_PAGE_SIZE, ge=1, le=MAX_PAGE_SIZE),
    offset: int = Query(default=0, ge=0),
    db: Session = Depends(get_db),
):
    """
    Список товаров — только из локальной БД.
    База пополняется автоматически при сборке YML-выгрузок (товары, что попали
    под условия складов/категорий/наличия). Поиск МС здесь не выполняется.
    """
    dbq = db.query(ProductImages)

    if has_images is True:
        dbq = dbq.filter(func.jsonb_array_length(ProductImages.images) > 0)
    elif has_images is False:
        dbq = dbq.filter(func.jsonb_array_length(ProductImages.images) == 0)

    if folder_uuid.strip():
        dbq = dbq.filter(ProductImages.folder_uuid == folder_uuid.strip())

    qs = q.strip()
    if qs:
        like = f"%{qs}%"
        dbq = dbq.filter(
            or_(
                ProductImages.product_code.ilike(like),
                ProductImages.product_article.ilike(like),
                ProductImages.product_name.ilike(like),
                ProductImages.product_uuid.ilike(like),
            )
        )

    dbq = dbq.order_by(
        (func.jsonb_array_length(ProductImages.images) > 0).desc(),
        ProductImages.updated_at.desc(),
    )

    rows = dbq.offset(offset).limit(limit).all()
    return [_row_to_dict(r) for r in rows]


@router.post("/cleanup")
def cleanup_products(
    threshold_days: int = Query(default=ORPHAN_THRESHOLD_DAYS, ge=1, le=365),
    dry_run: bool = Query(default=False),
    db: Session = Depends(get_db),
):
    """Удаляет файлы у товаров, которые не появлялись в МС > threshold_days дней."""
    return cleanup_orphan_images(db, threshold_days=threshold_days, dry_run=dry_run)


@router.get("/stats", response_model=ProductStatsOut)
def products_stats(db: Session = Depends(get_db)):
    """
    Счётчики на основе локального кеша:
    - with_images: товары с загруженными фото
    - catalog_products_total: всего записей в кеше (после ресинхрона ≈ каталог МС)
    - without_images: записи без фото
    """
    with_n = (
        db.query(func.count())
        .select_from(ProductImages)
        .filter(func.jsonb_array_length(ProductImages.images) > 0)
        .scalar()
    )
    total_n = db.query(func.count()).select_from(ProductImages).scalar()
    with_images = int(with_n or 0)
    catalog_total = int(total_n or 0)
    without = max(0, catalog_total - with_images)

    return ProductStatsOut(
        with_images=with_images,
        catalog_products_total=catalog_total,
        without_images=without,
    )


# ---------------------------------------------------------------------------
# Endpoints — single product
# ---------------------------------------------------------------------------

@router.get("/{product_uuid}")
def get_product(product_uuid: str, db: Session = Depends(get_db)):
    row = db.query(ProductImages).filter(ProductImages.product_uuid == product_uuid).first()
    if not row:
        return {
            "product_uuid": product_uuid,
            "product_name": "",
            "product_code": "",
            "product_article": "",
            "folder_uuid": "",
            "images": [],
            "image_urls": [],
        }
    return _row_to_dict(row)


@router.post("/{product_uuid}/images")
async def upload_images(
    product_uuid: str,
    files: list[UploadFile] = File(...),
    product_name: str = Query(default=""),
    product_code: str = Query(default=""),
    product_article: str = Query(default=""),
    folder_uuid: str = Query(default=""),
    db: Session = Depends(get_db),
):
    """Загружает одно или несколько изображений для товара."""
    dest_dir = _media_dir(product_uuid)
    dest_dir.mkdir(parents=True, exist_ok=True)

    row = _get_or_create(db, product_uuid)

    # Обновляем кешированные данные если переданы
    if product_name:
        row.product_name = product_name
    if product_code:
        row.product_code = product_code
    if product_article:
        row.product_article = product_article
    if folder_uuid:
        row.folder_uuid = folder_uuid

    saved: list[str] = []
    current_images: list[str] = list(row.images or [])

    for file in files:
        content_type = file.content_type or ""
        if content_type not in ALLOWED_MIME:
            raise HTTPException(
                status_code=415,
                detail=f"Тип файла не поддерживается: {content_type}. Разрешены: jpeg, png, webp, gif",
            )

        data = await file.read()
        if len(data) > MAX_FILE_SIZE:
            raise HTTPException(status_code=413, detail=f"Файл слишком большой (макс {MAX_FILE_SIZE // 1024 // 1024} MB)")

        ext = Path(file.filename or "img.jpg").suffix.lower() or ".jpg"
        filename = f"{_uuid.uuid4().hex}{ext}"
        file_path = dest_dir / filename
        file_path.write_bytes(data)
        current_images.append(filename)
        saved.append(filename)
        log.info("upload: product=%s file=%s size=%d", product_uuid, filename, len(data))

    row.images = current_images
    db.commit()
    db.refresh(row)

    patch = apply_image_change_to_feeds(db, product_uuid, current_images)
    return _row_to_dict(row, patch)


@router.delete("/{product_uuid}/images/{filename}")
def delete_image(product_uuid: str, filename: str, db: Session = Depends(get_db)):
    """Удаляет конкретное изображение."""
    row = db.query(ProductImages).filter(ProductImages.product_uuid == product_uuid).first()
    if not row:
        raise HTTPException(status_code=404, detail="Товар не найден")

    current: list[str] = list(row.images or [])
    if filename not in current:
        raise HTTPException(status_code=404, detail="Файл не найден")

    file_path = _media_dir(product_uuid) / filename
    try:
        file_path.unlink(missing_ok=True)
    except Exception as e:
        log.warning("delete_image: cannot remove file %s: %s", file_path, e)

    current.remove(filename)
    row.images = current
    db.commit()
    db.refresh(row)
    log.info("delete_image: product=%s file=%s removed", product_uuid, filename)

    patch = apply_image_change_to_feeds(db, product_uuid, current)
    return _row_to_dict(row, patch)


@router.put("/{product_uuid}/images")
def reorder_images(
    product_uuid: str,
    body: dict,
    db: Session = Depends(get_db),
):
    """Обновляет порядок изображений (первое = главное)."""
    new_order: list[str] = body.get("images") or []
    row = db.query(ProductImages).filter(ProductImages.product_uuid == product_uuid).first()
    if not row:
        raise HTTPException(status_code=404, detail="Товар не найден")

    existing = set(row.images or [])
    filtered = [fn for fn in new_order if fn in existing]
    row.images = filtered
    db.commit()
    db.refresh(row)

    patch = apply_image_change_to_feeds(db, product_uuid, filtered)
    return _row_to_dict(row, patch)


@router.patch("/{product_uuid}")
def update_product_meta(
    product_uuid: str,
    body: dict,
    db: Session = Depends(get_db),
):
    """Обновляет кешированные данные о товаре (имя, код, артикул, папка)."""
    row = _get_or_create(db, product_uuid)
    if "product_name" in body:
        row.product_name = body["product_name"]
    if "product_code" in body:
        row.product_code = body["product_code"]
    if "product_article" in body:
        row.product_article = body["product_article"]
    if "folder_uuid" in body:
        row.folder_uuid = body["folder_uuid"]
    db.commit()
    db.refresh(row)
    return _row_to_dict(row)


