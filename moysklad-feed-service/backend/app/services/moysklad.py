from __future__ import annotations

import time
import urllib.parse
from typing import Any

import httpx

from app.config import settings
from app.logging_setup import get_logger

log = get_logger("app.ms")


class MoySkladError(Exception):
    def __init__(self, message: str, status: int | None = None, body: str | None = None):
        super().__init__(message)
        self.status = status
        self.body = body


def _short_url(url: str, n: int = 240) -> str:
    return url if len(url) <= n else url[: n - 3] + "..."


class MoySkladClient:
    def __init__(self, login: str, password: str):
        self._base = settings.ms_api_base.rstrip("/")
        self._auth = (login, password)
        log.debug("MoySkladClient created (base=%s, login=%s)", self._base, login)

    def _request(self, method: str, path: str, **kw: Any) -> Any:
        url = f"{self._base}{path}" if path.startswith("/") else f"{self._base}/{path}"
        log.debug("MS request → %s %s", method, _short_url(url))
        started = time.monotonic()
        with httpx.Client(
            timeout=120.0,
            auth=self._auth,
            headers={"Accept-Encoding": "gzip"},
        ) as client:
            r = client.request(method, url, **kw)
        elapsed = time.monotonic() - started
        size_kb = len(r.content or b"") / 1024.0
        if r.status_code >= 400:
            log.error(
                "MS response ← %s (%.0fms) %s — %s",
                r.status_code,
                elapsed * 1000,
                _short_url(url),
                (r.text or "")[:500],
            )
            raise MoySkladError(
                f"MS API {r.status_code}",
                status=r.status_code,
                body=r.text[:2000],
            )
        log.info(
            "MS response ← %s (%.0fms, %.1fKB) %s",
            r.status_code,
            elapsed * 1000,
            size_kb,
            _short_url(url),
        )
        if r.headers.get("content-type", "").startswith("application/json"):
            return r.json()
        return r.text

    def list_price_types(self) -> list[dict[str, Any]]:
        """Типы цен из /context/companysettings/pricetype.
        МойСклад отдаёт этот эндпоинт как корневой массив, а не {rows:[...]}.
        """
        data = self._request("GET", "/context/companysettings/pricetype")
        if isinstance(data, list):
            return data
        return data.get("rows") or []

    def list_stores(self) -> list[dict[str, Any]]:
        rows: list[dict[str, Any]] = []
        offset = 0
        limit = 100
        while True:
            data = self._request(
                "GET",
                f"/entity/store?limit={limit}&offset={offset}",
            )
            batch = data.get("rows") or []
            rows.extend(batch)
            if len(batch) < limit:
                break
            offset += limit
        return rows

    def list_products_page(
        self,
        offset: int,
        limit: int,
        product_folder_href: str | None = None,
        expand: str = "productFolder",
    ) -> dict[str, Any]:
        params: dict[str, str | int] = {
            "limit": limit,
            "offset": offset,
        }
        if expand:
            params["expand"] = expand
        if product_folder_href:
            params["filter"] = f"productFolder={product_folder_href}"
        q = urllib.parse.urlencode(params)
        return self._request("GET", f"/entity/product?{q}")

    def list_entities_by_ids(
        self,
        entity_type: str,
        uuids: list[str],
        expand: str = "",
    ) -> dict[str, Any]:
        """
        Fetch a batch of entities filtered by their IDs.
        entity_type: 'product', 'variant', etc.
        expand: comma-separated expand fields, e.g. 'productFolder,attributes'.
        Passes filter=id=uuid1;id=uuid2;... — safe for ~50 IDs per call.
        """
        filter_str = ";".join(f"id={u}" for u in uuids)
        params: dict[str, str] = {"filter": filter_str, "limit": str(len(uuids))}
        if expand:
            params["expand"] = expand
        q = urllib.parse.urlencode(params)
        return self._request("GET", f"/entity/{entity_type}?{q}")

    def search_products(
        self,
        query: str = "",
        folder_href: str | None = None,
        limit: int = 50,
    ) -> list[dict[str, Any]]:
        """Поиск товаров в МС по коду/артикулу/названию. Возвращает rows."""
        parts: list[str] = []
        if folder_href:
            parts.append(f"productFolder={folder_href}")
        filter_param = ";".join(parts) if parts else None

        params: dict[str, str] = {"limit": str(limit)}
        if query.strip():
            params["search"] = query.strip()
        if filter_param:
            params["filter"] = filter_param
        q = urllib.parse.urlencode(params)
        data = self._request("GET", f"/entity/product?{q}&expand=productFolder")
        return data.get("rows") or []

    def list_product_folders(self) -> list[dict[str, Any]]:
        rows: list[dict[str, Any]] = []
        offset = 0
        limit = 100
        while True:
            data = self._request(
                "GET",
                f"/entity/productfolder?limit={limit}&offset={offset}",
            )
            batch = data.get("rows") or []
            rows.extend(batch)
            if len(batch) < limit:
                break
            offset += limit
        return rows

    def get_stock_all(
        self,
        offset: int,
        limit: int,
        store_hrefs: list[str] | None,
        folder_hrefs: list[str] | None = None,
        positive_only: bool = True,
    ) -> dict[str, Any]:
        """
        report/stock/all с фильтрами по складам и папкам.
        positive_only=True — отдаются только позиции с положительным остатком.
        """
        parts: list[str] = []
        parts.append("stockMode=positiveOnly" if positive_only else "stockMode=all")
        if store_hrefs:
            for h in store_hrefs:
                parts.append(f"store={h}")
        if folder_hrefs:
            for h in folder_hrefs:
                parts.append(f"productFolder={h}")
        filter_str = ";".join(parts)
        q = urllib.parse.urlencode(
            {"offset": offset, "limit": limit, "filter": filter_str}
        )
        return self._request("GET", f"/report/stock/all?{q}")

    @staticmethod
    def store_href(uuid: str) -> str:
        base = settings.ms_api_base.rstrip("/")
        return f"{base}/entity/store/{uuid}"

    @staticmethod
    def folder_href(uuid: str) -> str:
        base = settings.ms_api_base.rstrip("/")
        return f"{base}/entity/productfolder/{uuid}"
