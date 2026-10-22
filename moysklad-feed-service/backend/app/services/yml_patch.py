"""
Локальный патч YML-фидов: применяет изменение изображений товара ко всем
сохранённым YML без обращения к МойСклад.

Когда работает:
- Товар уже есть в `<offer>` (значит был в остатках на момент последней сборки)
  → перезаписываем теги `<picture>`, или удаляем оффер если изображений больше нет.

Когда НЕ работает (нужен полный rebuild):
- Товара нет в YML (например, его не было в остатках) → добавить локально мы
  не можем, потому что цена/название/категория могли измениться и нужна сверка.
"""

from __future__ import annotations

import xml.etree.ElementTree as ET
from datetime import datetime, timezone
from typing import Iterable

from sqlalchemy.orm import Session

from app.config import settings
from app.logging_setup import get_logger
from app.models import Feed

log = get_logger("app.yml_patch")

# Где должен жить блок <picture> внутри <offer>: после count/description,
# до тегов <param> и <categoryId>.
_AFTER_TAGS = {
    "name",
    "vendorCode",
    "price",
    "oldprice",
    "currencyId",
    "count",
    "description",
}


def _picture_urls(product_uuid: str, filenames: list[str]) -> list[str]:
    base = settings.public_base_url.rstrip("/")
    return [f"{base}/media/{product_uuid}/{fn}" for fn in filenames]


def _replace_pictures(offer: ET.Element, urls: list[str]) -> None:
    """Удаляет все <picture> и вставляет новые в правильную позицию."""
    for pic in list(offer.findall("picture")):
        offer.remove(pic)

    if not urls:
        return

    # Найти индекс, после которого вставлять
    insert_at = 0
    for i, child in enumerate(list(offer)):
        if child.tag in _AFTER_TAGS:
            insert_at = i + 1
    for j, url in enumerate(urls):
        pic = ET.Element("picture")
        pic.text = url
        offer.insert(insert_at + j, pic)


def apply_image_change_to_feeds(
    db: Session,
    product_uuid: str,
    filenames: list[str],
) -> dict:
    """
    Прокидывает текущий список фото `filenames` товара `product_uuid` во все
    сохранённые YML.

    Returns:
        {
            "patched_feeds": [<feed_id>, ...],   # обновили <picture>
            "removed_from_feeds": [<feed_id>],   # удалили оффер целиком (фото нет)
            "missing_in_feeds": [<feed_id>],     # оффера нет, нужен rebuild
        }
    """
    urls = _picture_urls(product_uuid, filenames)
    feeds = db.query(Feed).filter(Feed.yml_body.isnot(None)).all()

    patched: list[int] = []
    removed: list[int] = []
    missing: list[int] = []

    for feed in feeds:
        try:
            root = ET.fromstring(feed.yml_body or "")
        except ET.ParseError:
            log.warning("yml_patch: feed_id=%s body is not valid XML, skip", feed.id)
            continue

        shop = root.find("shop")
        offers_el = shop.find("offers") if shop is not None else None
        if offers_el is None:
            continue

        offer = offers_el.find(f"./offer[@id='{product_uuid}']")
        if offer is None:
            missing.append(feed.id)
            continue

        if not urls:
            offers_el.remove(offer)
            removed.append(feed.id)
        else:
            _replace_pictures(offer, urls)
            patched.append(feed.id)

        feed.yml_body = ET.tostring(
            root, encoding="utf-8", xml_declaration=True
        ).decode("utf-8")
        feed.last_built_at = datetime.now(timezone.utc)

    if patched or removed or missing:
        db.commit()

    log.info(
        "yml_patch: product=%s files=%d patched=%s removed=%s missing=%s",
        product_uuid,
        len(filenames),
        patched,
        removed,
        missing,
    )
    return {
        "patched_feeds": patched,
        "removed_from_feeds": removed,
        "missing_in_feeds": missing,
    }


def feeds_containing_offer(db: Session, product_uuid: str) -> list[int]:
    """Список feed.id, в YML которых уже встречается оффер с этим UUID."""
    feeds = db.query(Feed).filter(Feed.yml_body.isnot(None)).all()
    out: list[int] = []
    for f in feeds:
        try:
            root = ET.fromstring(f.yml_body or "")
        except ET.ParseError:
            continue
        if root.find(f".//offer[@id='{product_uuid}']") is not None:
            out.append(f.id)
    return out


__all__ = ["apply_image_change_to_feeds", "feeds_containing_offer"]
