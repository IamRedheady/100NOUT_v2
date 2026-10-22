from __future__ import annotations

import html
import re
import xml.etree.ElementTree as ET
from collections.abc import Callable, Iterable, Iterator
from datetime import datetime, timezone
from typing import Any

from sqlalchemy import func
from sqlalchemy.dialects.postgresql import insert as pg_insert
from sqlalchemy.orm import Session

from app.config import settings
from app.folder_tree import expand_folder_selection, parent_id_from_folder_row
from app.logging_setup import get_logger
from app.models import Feed, ProductImages
from app.services.moysklad import MoySkladClient, MoySkladError

log = get_logger("app.yml")

# Прогресс пересборки: callback получает словарь полей,
# которые применяются к статусу задачи (status/phase/current/total/message/offers/...).
ProgressCb = Callable[[dict[str, Any]], None]

# Сколько productFolder=... засовывать в один URL отчёта.
# nginx у МС обычно ограничивает request URI ~8 KB.
FOLDERS_PER_REQUEST = 25

_UUID_RE = re.compile(r"entity/[^/]+/([0-9a-fA-F-]{36})")


# ---------------------------------------------------------------------------
# XML helpers
# ---------------------------------------------------------------------------

def _text_el(parent: ET.Element, tag: str, text: str | None) -> None:
    el = ET.SubElement(parent, tag)
    el.text = text or ""


def _stock_row_folder_uuid(row: dict[str, Any]) -> str | None:
    """Достаёт UUID папки из stock-row (поле folder)."""
    folder = row.get("folder") or {}
    fuid = folder.get("id") or ""
    if not fuid:
        href = (folder.get("meta") or {}).get("href") or ""
        if href:
            fuid = href.rstrip("/").split("/")[-1]
    return fuid or None


def _upsert_products_from_stock(
    db: Session, stock_rows: list[dict[str, Any]]
) -> int:
    """
    Bulk upsert найденных в остатках товаров в нашу базу `product_images`.
    Поле `images` НЕ перезаписываем — оно хранится отдельно (загруженные фото).
    Возвращает количество обработанных строк.
    """
    if not stock_rows:
        return 0
    now = datetime.now(timezone.utc)

    # Дедуп по UUID — берём последнее значение полей
    by_uuid: dict[str, dict[str, Any]] = {}
    for row in stock_rows:
        uid = _extract_assortment_id(row)
        if not uid:
            continue
        by_uuid[uid] = {
            "product_uuid": uid,
            "product_name": (row.get("name") or "")[:1000] or None,
            "product_code": (row.get("code") or "")[:256] or None,
            "product_article": (row.get("article") or "")[:256] or None,
            "folder_uuid": _stock_row_folder_uuid(row),
            "images": [],  # дефолт для INSERT, при ON CONFLICT не перезаписывается
            "last_seen_in_ms_at": now,
        }
    if not by_uuid:
        return 0

    values = list(by_uuid.values())
    # Чанками — на случай очень большой выгрузки (PostgreSQL bind-параметры)
    CHUNK = 1000
    for i in range(0, len(values), CHUNK):
        chunk = values[i : i + CHUNK]
        stmt = pg_insert(ProductImages.__table__).values(chunk)
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
    log.info("upsert_products_from_stock: rows=%d", len(values))
    return len(values)


# ---------------------------------------------------------------------------
# Stock helpers
# ---------------------------------------------------------------------------

def _row_quantity(row: dict[str, Any]) -> float:
    """
    Физический остаток на складе. В report/stock/all поле `stock` — это «Складской
    остаток», `quantity` — `stock - reserve` (доступно к продаже). Берём именно
    `stock`, потому что задача — «товары, которые есть на складе».
    """
    s = row.get("stock")
    if isinstance(s, (int, float)):
        return float(s)
    q = row.get("quantity")
    if isinstance(q, (int, float)):
        return float(q)
    try:
        return float(row.get("stock", 0) or 0) - float(row.get("reserve", 0) or 0)
    except (TypeError, ValueError):
        return 0.0


def _extract_assortment_id(row: dict[str, Any]) -> str:
    """Достаёт UUID товара/модификации из строки отчёта (несколько вариантов)."""
    aref = ((row.get("assortment") or {}).get("meta") or {}).get("href") or ""
    if aref:
        return aref.rstrip("/").split("/")[-1]
    rmeta_href = (row.get("meta") or {}).get("href") or ""
    m = _UUID_RE.search(rmeta_href)
    if m:
        return m.group(1)
    return ""


# ---------------------------------------------------------------------------
# Attribute / characteristic value extraction (как в WmsAttributeApi плагина)
# ---------------------------------------------------------------------------

def _attr_value(attr: dict[str, Any]) -> str:
    """
    Извлекает строковое значение атрибута МойСклад.
    Типы: string, text, long, double, boolean, time, customentity, file, link.
    """
    val = attr.get("value")
    if val is None:
        return ""
    if isinstance(val, dict):
        # customentity: {"name": "Nike", "meta": {...}}
        return val.get("name") or ""
    if isinstance(val, bool):
        return "Да" if val else "Нет"
    return str(val).strip()


# ---------------------------------------------------------------------------
# Entity detail map  (product UUID → {folder_uuid, attributes})
# ---------------------------------------------------------------------------

# Сколько id=... засовывать в один filter-запрос детализации.
# UUID = 36 символов → "id=uuid;" = 40 символов × 50 = 2000 символов — безопасно.
IDS_PER_DETAIL_REQUEST = 50


def _href_to_uuid(href: str) -> str:
    """Извлекает UUID из любого href МойСклад."""
    m = _UUID_RE.search(href)
    return m.group(1) if m else href.rstrip("/").split("/")[-1]


def _parse_product_row(row: dict[str, Any]) -> tuple[str, dict[str, Any]]:
    """Парсит строку /entity/product → (uuid, {folder_uuid, attributes, sale_prices})."""
    uid = row.get("id") or ""
    folder = row.get("productFolder") or {}
    folder_uuid = folder.get("id") or ""
    if not folder_uuid:
        folder_href = (folder.get("meta") or {}).get("href") or ""
        if folder_href:
            folder_uuid = _href_to_uuid(folder_href)
    attrs: list[dict[str, str]] = []
    for a in (row.get("attributes") or []):
        v = _attr_value(a)
        name = (a.get("name") or "").strip()
        if name and v:
            attrs.append({"name": name, "value": v})
    # salePrices: {price_type_uuid → value_in_rubles}
    sale_prices: dict[str, float] = {}
    for sp in (row.get("salePrices") or []):
        ptype = sp.get("priceType") or {}
        tid = ptype.get("id") or ""
        val = sp.get("value")
        if tid and isinstance(val, (int, float)) and float(val) > 0:
            sale_prices[tid] = float(val) / 100.0
    return uid, {"folder_uuid": folder_uuid, "attributes": attrs, "sale_prices": sale_prices}


def _build_entity_map(
    client: MoySkladClient,
    stock_rows: list[dict[str, Any]],
    on_progress: ProgressCb | None,
) -> dict[str, dict[str, Any]]:
    """
    Возвращает словарь  uuid → {folder_uuid, attributes: [{name, value}]}
    только для товаров/модификаций, которые реально есть в остатках.

    Алгоритм:
    1. Разбиваем UUID из stock_rows на product/variant
    2. Пакетно подгружаем детали только нужных ID
    3. Для вариантов — подгружаем родительские товары (если не попали в шаг 2)
    """
    # ── Шаг 1: разделить UUID по типу сущности ─────────────────────────────
    product_uuids: set[str] = set()
    variant_uuids: set[str] = set()
    for row in stock_rows:
        uid = _extract_assortment_id(row)
        if not uid:
            continue
        atype = ((row.get("assortment") or {}).get("meta") or {}).get("type") or ""
        if atype == "variant":
            variant_uuids.add(uid)
        else:
            product_uuids.add(uid)

    log.info(
        "entity_map: stock has %d products and %d variants to detail",
        len(product_uuids), len(variant_uuids),
    )
    _emit(
        on_progress,
        phase="enrich",
        message=(
            f"Загружаем детали: {len(product_uuids)} товаров"
            + (f" + {len(variant_uuids)} модификаций" if variant_uuids else "")
        ),
    )

    # ── Шаг 2: пакетно загружаем продукты ──────────────────────────────────
    product_map: dict[str, dict[str, Any]] = {}
    product_chunks = list(_chunked(list(product_uuids), IDS_PER_DETAIL_REQUEST))
    for ci, chunk in enumerate(product_chunks, start=1):
        log.info(
            "entity_map products: batch %d/%d (%d ids)",
            ci, len(product_chunks), len(chunk),
        )
        data = client.list_entities_by_ids("product", chunk, "productFolder,attributes")
        for row in (data.get("rows") or []):
            uid, info = _parse_product_row(row)
            if uid:
                product_map[uid] = info

    log.info("entity_map: %d products loaded", len(product_map))

    # ── Шаг 3: пакетно загружаем варианты ──────────────────────────────────
    variant_raw: dict[str, dict[str, Any]] = {}  # uuid → {parent_uuid, chars}
    missing_parent_uuids: set[str] = set()

    variant_chunks = list(_chunked(list(variant_uuids), IDS_PER_DETAIL_REQUEST))
    for ci, chunk in enumerate(variant_chunks, start=1):
        log.info(
            "entity_map variants: batch %d/%d (%d ids)",
            ci, len(variant_chunks), len(chunk),
        )
        data = client.list_entities_by_ids("variant", chunk, "characteristics")
        for row in (data.get("rows") or []):
            uid = row.get("id") or ""
            if not uid:
                continue
            parent_href = ((row.get("product") or {}).get("meta") or {}).get("href") or ""
            parent_uuid = _href_to_uuid(parent_href) if parent_href else ""
            chars: list[dict[str, str]] = []
            for c in (row.get("characteristics") or []):
                v = str(c.get("value") or "").strip()
                name = (c.get("name") or "").strip()
                if name and v:
                    chars.append({"name": name, "value": v})
            variant_raw[uid] = {"parent_uuid": parent_uuid, "chars": chars}
            if parent_uuid and parent_uuid not in product_map:
                missing_parent_uuids.add(parent_uuid)

    # ── Шаг 4: догружаем родительские товары вариантов (если не были в stock) ──
    if missing_parent_uuids:
        log.info("entity_map: fetching %d missing parent products for variants", len(missing_parent_uuids))
        parent_chunks = list(_chunked(list(missing_parent_uuids), IDS_PER_DETAIL_REQUEST))
        for ci, chunk in enumerate(parent_chunks, start=1):
            data = client.list_entities_by_ids("product", chunk, "productFolder,attributes")
            for row in (data.get("rows") or []):
                uid, info = _parse_product_row(row)
                if uid:
                    product_map[uid] = info

    # ── Шаг 5: собираем итоговую карту ─────────────────────────────────────
    entity_map: dict[str, dict[str, Any]] = dict(product_map)
    for uid, vdata in variant_raw.items():
        parent_info = product_map.get(vdata["parent_uuid"], {})
        entity_map[uid] = {
            "folder_uuid": parent_info.get("folder_uuid", ""),
            "attributes": vdata["chars"] + parent_info.get("attributes", []),
        }

    log.info(
        "entity_map: done — products=%d variants=%d total=%d",
        len(product_map), len(variant_raw), len(entity_map),
    )
    return entity_map


# ---------------------------------------------------------------------------
# Categories XML section
# ---------------------------------------------------------------------------

def _build_folder_index(folders_raw: list[dict[str, Any]]) -> dict[str, dict[str, Any]]:
    """
    Строит словарь uuid → {name, parent_uuid} из сырых папок МойСклад.
    """
    index: dict[str, dict[str, Any]] = {}
    for row in folders_raw:
        uid = (row.get("id") or "").strip()
        if not uid:
            # Попробуем из meta.href
            meta_href = (row.get("meta") or {}).get("href") or ""
            uid = _href_to_uuid(meta_href) if meta_href else ""
        if not uid:
            continue
        parent_uuid = parent_id_from_folder_row(row) or ""
        index[uid] = {
            "name": (row.get("name") or "").strip() or uid,
            "parent_uuid": parent_uuid,
        }
    return index


def _ancestors_including_self(uuid: str, folder_index: dict[str, dict[str, Any]]) -> set[str]:
    """Возвращает uuid папки и все её родительские папки."""
    result: set[str] = set()
    current: str | None = uuid
    while current and current not in result:
        result.add(current)
        current = (folder_index.get(current) or {}).get("parent_uuid") or None
    return result


def _categories_xml(
    folders_raw: list[dict[str, Any]],
    used_folder_uuids: set[str],
) -> ET.Element:
    """
    Строит элемент <categories> для YML.
    Включает все используемые категории и их предков (для корректной иерархии).
    """
    folder_index = _build_folder_index(folders_raw)

    # Расширяем до полных путей от корня
    all_uuids: set[str] = set()
    for uid in used_folder_uuids:
        if uid in folder_index:
            all_uuids |= _ancestors_including_self(uid, folder_index)

    categories_el = ET.Element("categories")
    for uid in sorted(all_uuids):
        info = folder_index.get(uid)
        if not info:
            continue
        attrs: dict[str, str] = {"id": uid}
        if info["parent_uuid"] and info["parent_uuid"] in all_uuids:
            attrs["parentId"] = info["parent_uuid"]
        cat_el = ET.SubElement(categories_el, "category", attrs)
        cat_el.text = info["name"]

    log.info(
        "categories_xml: used=%d → with ancestors=%d",
        len(used_folder_uuids), len(all_uuids),
    )
    return categories_el


# ---------------------------------------------------------------------------
# Offer builder
# ---------------------------------------------------------------------------

def _offer_from_stock_row(
    row: dict[str, Any],
    details: dict[str, Any] | None = None,
    price_type_id: str | None = None,
    old_price_type_id: str | None = None,
    product_img_map: dict[str, list[str]] | None = None,
) -> ET.Element | None:
    qty = _row_quantity(row)
    if qty <= 0:
        return None

    ms_id = _extract_assortment_id(row)
    if not ms_id:
        return None

    name = row.get("name") or "Без названия"
    article = (row.get("article") or "").strip()
    code = (row.get("code") or "").strip()

    # ── Цены ────────────────────────────────────────────────────────────────
    sale_prices = (details or {}).get("sale_prices", {})

    # <price> — основная цена (тип из настроек → fallback на stock-поле)
    price_val = ""
    if price_type_id and sale_prices.get(price_type_id):
        price_val = f"{sale_prices[price_type_id]:.2f}"
    else:
        # Fallback: первая ненулевая из salePrices, затем поле из stock-отчёта
        if sale_prices:
            first_v = next(iter(sale_prices.values()))
            price_val = f"{first_v:.2f}"
        else:
            raw = row.get("salePrice") if isinstance(row.get("salePrice"), (int, float)) else row.get("price")
            if isinstance(raw, (int, float)):
                price_val = f"{float(raw) / 100:.2f}"

    # <oldprice> — зачёркнутая цена (только если задан тип и она больше price)
    old_price_val = ""
    if old_price_type_id and sale_prices.get(old_price_type_id):
        candidate = f"{sale_prices[old_price_type_id]:.2f}"
        # Показываем oldprice только если она выше price (иначе YML-валидаторы ругаются)
        if price_val and float(candidate) > float(price_val):
            old_price_val = candidate

    offer = ET.Element("offer", {"id": ms_id, "available": "true"})
    _text_el(offer, "name", str(name))
    # <vendorCode> — артикул производителя; fallback на внутренний код
    vendor_code = article or code
    if vendor_code:
        _text_el(offer, "vendorCode", vendor_code)
    _text_el(offer, "price", price_val)
    if old_price_val:
        _text_el(offer, "oldprice", old_price_val)
    _text_el(offer, "currencyId", "RUR")
    _text_el(offer, "count", str(int(qty)))

    desc = row.get("description")
    if desc:
        _text_el(offer, "description", html.unescape(str(desc))[:8000])

    # Внутренний код (SKU) как param — отдельно от артикула
    if code:
        sku_el = ET.SubElement(offer, "param", {"name": "SKU"})
        sku_el.text = code

    # Изображения из локального хранилища
    img_filenames = (product_img_map or {}).get(ms_id, [])
    base_url = settings.public_base_url.rstrip("/")
    for fn in img_filenames:
        _text_el(offer, "picture", f"{base_url}/media/{ms_id}/{fn}")

    if details:
        # Категория
        folder_uuid = details.get("folder_uuid") or ""
        if folder_uuid:
            _text_el(offer, "categoryId", folder_uuid)

        # Параметры / характеристики
        for attr in (details.get("attributes") or []):
            n = (attr.get("name") or "").strip()
            v = (attr.get("value") or "").strip()
            if n and v:
                param_el = ET.SubElement(offer, "param", {"name": n})
                param_el.text = v

    return offer


# ---------------------------------------------------------------------------
# Stock row iteration
# ---------------------------------------------------------------------------

def _chunked(seq: list[str], n: int) -> Iterable[list[str]]:
    for i in range(0, len(seq), n):
        yield seq[i : i + n]


def _emit(on_progress: ProgressCb | None, **payload: Any) -> None:
    if on_progress:
        on_progress(payload)


def _iter_stock_rows(
    client: MoySkladClient,
    lim: int,
    store_hrefs: list[str] | None,
    folder_hrefs: list[str] | None,
    on_progress: ProgressCb | None,
    state: dict[str, Any],
) -> Iterator[dict[str, Any]]:
    """
    state используется, чтобы в сообщения подмешивать актуальное число офферов,
    которое считается уже снаружи (в build_yml_for_feed).
    """
    if not folder_hrefs:
        off = 0
        total: int | None = None
        log.info(
            "stock iter: simple mode (no folders), batch=%d, stores=%d",
            lim,
            len(store_hrefs or []),
        )
        _emit(on_progress, message="Считаем доступные товары…")
        while True:
            log.debug("stock page request: offset=%d limit=%d", off, lim)
            st = client.get_stock_all(off, lim, store_hrefs, None, True)
            if total is None:
                meta_size = (st.get("meta") or {}).get("size")
                total = int(meta_size) if isinstance(meta_size, int) else None
                if total is not None:
                    log.info("stock iter: total available = %d", total)
                    _emit(
                        on_progress,
                        current=0,
                        total=total,
                        message=f"Найдено товаров на складах: {total}",
                    )
            rows = st.get("rows") or []
            for r in rows:
                yield r
            done = off + len(rows)
            shown_total = total if total is not None else None
            log.info(
                "stock page: offset=%d got=%d done=%d/%s offers_so_far=%d",
                off,
                len(rows),
                done,
                shown_total if shown_total is not None else "?",
                int(state.get("offers", 0)),
            )
            _emit(
                on_progress,
                current=done if shown_total is None else min(done, shown_total),
                total=shown_total,
                offers=int(state.get("offers", 0)),
                message=(
                    f"Загружено товаров: {done}"
                    + (f" из {shown_total}" if shown_total is not None else "")
                    + f" · добавлено в YML: {int(state.get('offers', 0))}"
                ),
            )
            if len(rows) < lim:
                break
            off += lim
        return

    chunks = list(_chunked(folder_hrefs, FOLDERS_PER_REQUEST))
    n_chunks = len(chunks)
    log.info(
        "stock iter: folder mode, total_folders=%d, chunks=%d (×%d), batch=%d, stores=%d",
        len(folder_hrefs),
        n_chunks,
        FOLDERS_PER_REQUEST,
        lim,
        len(store_hrefs or []),
    )
    seen: set[str] = set()
    state["seen_total"] = 0
    for ci, chunk in enumerate(chunks, start=1):
        off = 0
        chunk_seen_before = len(seen)
        log.info(
            "stock iter: chunk %d/%d (folders=%d, seen_so_far=%d)",
            ci,
            n_chunks,
            len(chunk),
            len(seen),
        )
        _emit(
            on_progress,
            current=len(seen),
            total=None,
            offers=int(state.get("offers", 0)),
            message=(
                f"Группа категорий {ci} из {n_chunks} · уже найдено товаров: {len(seen)}"
            ),
        )
        while True:
            log.debug(
                "stock page request: chunk=%d/%d offset=%d limit=%d",
                ci, n_chunks, off, lim,
            )
            st = client.get_stock_all(off, lim, store_hrefs, chunk, True)
            rows = st.get("rows") or []
            new_in_page = 0
            for r in rows:
                key = _extract_assortment_id(r) or ""
                if key and key in seen:
                    continue
                if key:
                    seen.add(key)
                    new_in_page += 1
                state["seen_total"] = len(seen)
                yield r
            in_chunk = len(seen) - chunk_seen_before
            log.info(
                "stock page: chunk=%d/%d offset=%d got=%d new=%d seen_total=%d offers_so_far=%d",
                ci, n_chunks, off, len(rows), new_in_page, len(seen),
                int(state.get("offers", 0)),
            )
            _emit(
                on_progress,
                current=len(seen),
                total=None,
                offers=int(state.get("offers", 0)),
                message=(
                    f"Группа категорий {ci} из {n_chunks} · "
                    f"новых товаров в группе: {in_chunk} · "
                    f"всего найдено: {len(seen)} · добавлено в YML: "
                    f"{int(state.get('offers', 0))}"
                ),
            )
            if len(rows) < lim:
                break
            off += lim


# ---------------------------------------------------------------------------
# Main entry point
# ---------------------------------------------------------------------------

def build_yml_for_feed(
    db: Session,
    client: MoySkladClient,
    feed: Feed,
    on_progress: ProgressCb | None = None,
) -> tuple[str, int, str | None]:
    import time as _t
    started = _t.monotonic()
    lim = min(feed.batch_limit, 1000)

    price_type_id: str | None = feed.price_type_id or None
    old_price_type_id: str | None = feed.old_price_type_id or None

    log.info(
        "build_yml: feed_id=%s name=%r slug=%r batch=%d stores=%d folders=%d "
        "price_type=%s old_price_type=%s",
        feed.id,
        feed.name,
        feed.slug,
        lim,
        len(feed.store_uuids or []),
        len(feed.productfolder_uuids or []),
        price_type_id or "default",
        old_price_type_id or "none",
    )

    store_hrefs: list[str] | None = None
    if feed.store_uuids:
        store_hrefs = [MoySkladClient.store_href(u) for u in feed.store_uuids]
        log.debug("build_yml: store_hrefs=%d", len(store_hrefs))

    # ── 1. Дерево категорий (всегда: нужно для categories-секции в YML) ────
    try:
        _emit(on_progress, phase="enrich", message="Получаем дерево категорий…")
        log.info("build_yml: fetching all product folders for categories section…")
        folders_raw = client.list_product_folders()
        log.info("build_yml: got %d product folders", len(folders_raw))
    except MoySkladError as e:
        log.error("build_yml: folder tree failed: %s · %s", e, (e.body or "")[:300])
        return "", 0, f"folders: {e} {e.body or ''}"

    # ── 2. Раскрываем выбранные папки (фильтр для stock-отчёта) ────────────
    folder_hrefs: list[str] | None = None
    if feed.productfolder_uuids:
        expanded = expand_folder_selection(
            folders_raw, list(feed.productfolder_uuids or [])
        )
        folder_hrefs = [MoySkladClient.folder_href(u) for u in expanded]
        log.info(
            "build_yml: selected=%d → expanded with subtree=%d",
            len(feed.productfolder_uuids or []),
            len(folder_hrefs),
        )
        _emit(
            on_progress,
            message=(
                f"Категорий с подпапками: {len(folder_hrefs)}"
                f" · группами по {FOLDERS_PER_REQUEST}"
            ),
        )

    # ── 3. Итерация по остаткам → буферизация строк ────────────────────────
    _emit(on_progress, phase="loading", message="Загружаем остатки…")
    state: dict[str, Any] = {"offers": 0, "rows_seen": 0}
    stock_rows: list[dict[str, Any]] = []
    try:
        for row in _iter_stock_rows(
            client, lim, store_hrefs, folder_hrefs, on_progress, state
        ):
            stock_rows.append(row)
    except MoySkladError as e:
        log.error(
            "build_yml: stock failed after %d rows: %s · %s",
            len(stock_rows), e, (e.body or "")[:300],
        )
        return "", 0, f"stock: {e} {e.body or ''}"
    log.info("build_yml: stock buffered %d rows", len(stock_rows))

    # ── 3.5. Upsert найденных товаров в нашу БД (база товаров для UI «Товары») ─
    _emit(on_progress, message="Обновляем базу товаров…")
    _upsert_products_from_stock(db, stock_rows)

    # ── 4. Загружаем изображения из БД и фильтруем остатки только по «с фото» ─
    _emit(on_progress, message="Подбираем товары с изображениями…")
    img_rows = (
        db.query(ProductImages)
        .filter(func.jsonb_array_length(ProductImages.images) > 0)
        .all()
    )
    product_img_map: dict[str, list[str]] = {
        r.product_uuid: list(r.images or []) for r in img_rows if r.images
    }
    log.info("build_yml: products with images in DB: %d", len(product_img_map))

    pre_filter_count = len(stock_rows)
    stock_rows = [
        row for row in stock_rows
        if _extract_assortment_id(row) in product_img_map
    ]
    skipped_no_image = pre_filter_count - len(stock_rows)
    log.info(
        "build_yml: stock filtered by images: %d/%d kept (%d without images skipped)",
        len(stock_rows), pre_filter_count, skipped_no_image,
    )
    _emit(
        on_progress,
        message=(
            f"Остатков с фото: {len(stock_rows)} из {pre_filter_count}"
        ),
    )

    # ── 5. Карта деталей — только для отфильтрованных товаров ──────────────
    try:
        entity_map = _build_entity_map(client, stock_rows, on_progress)
    except MoySkladError as e:
        log.error("build_yml: entity map failed: %s · %s", e, (e.body or "")[:300])
        return "", 0, f"entity_map: {e} {e.body or ''}"

    # ── 6. Корневая XML-структура + сборка офферов ─────────────────────────
    root = ET.Element(
        "yml_catalog",
        {"date": datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M")},
    )
    shop = ET.SubElement(root, "shop")
    _text_el(shop, "name", feed.name)
    _text_el(shop, "company", feed.name)
    _text_el(shop, "url", "https://example.com")

    # Offers строим отдельным элементом — categories вставим перед ним
    offers_el = ET.Element("offers")
    used_folder_uuids: set[str] = set()
    skipped_no_id = 0
    skipped_zero_qty = 0

    _emit(on_progress, phase="build", message="Формируем YML-офферы…")
    for row in stock_rows:
        state["rows_seen"] += 1
        uid = _extract_assortment_id(row)
        details = entity_map.get(uid)

        if details:
            fuid = details.get("folder_uuid") or ""
            if fuid:
                used_folder_uuids.add(fuid)

        offer_el = _offer_from_stock_row(
            row, details, price_type_id, old_price_type_id, product_img_map
        )
        if offer_el is None:
            qty = _row_quantity(row)
            if qty <= 0:
                skipped_zero_qty += 1
            else:
                skipped_no_id += 1
            continue
        offers_el.append(offer_el)
        state["offers"] += 1

    # ── 7. Категории (после итерации — знаем, какие реально используются) ──
    _emit(on_progress, phase="enrich", message="Формируем секцию категорий…")
    categories_el = _categories_xml(folders_raw, used_folder_uuids)

    # Вставляем categories ПЕРЕД offers (порядок важен для YML-валидаторов)
    shop.append(categories_el)
    shop.append(offers_el)

    elapsed = _t.monotonic() - started
    log.info(
        "build_yml: done feed_id=%s rows_seen=%d offers=%d categories=%d "
        "skipped_no_image=%d skipped_zero_qty=%d skipped_no_id=%d in %.2fs",
        feed.id,
        state["rows_seen"],
        state["offers"],
        len(used_folder_uuids),
        skipped_no_image,
        skipped_zero_qty,
        skipped_no_id,
        elapsed,
    )

    _emit(
        on_progress,
        current=state["offers"],
        total=state["offers"],
        offers=state["offers"],
        message=(
            f"Сформировано офферов: {state['offers']} · "
            f"категорий: {len(used_folder_uuids)} · "
            f"пропущено без фото: {skipped_no_image}"
        ),
    )

    xml_bytes = ET.tostring(root, encoding="utf-8", xml_declaration=True)
    return xml_bytes.decode("utf-8"), int(state["offers"]), None
