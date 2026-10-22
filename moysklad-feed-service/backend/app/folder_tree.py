"""Дерево папок товаров МойСклад: развёртка выбранных узлов на все потомки."""

from __future__ import annotations

from collections import defaultdict
from typing import Any


def folder_id_from_api_row(row: dict[str, Any]) -> str | None:
    """UUID папки: в ответе МойСклад есть поле id; иначе — последний сегмент meta.href."""
    uid = row.get("id")
    if isinstance(uid, str) and uid.strip():
        return uid.strip()
    meta = row.get("meta", {}) or {}
    href = meta.get("href") or ""
    if not href:
        return None
    part = href.rstrip("/").split("/")[-1]
    return part or None


def _folder_id_from_row(row: dict[str, Any]) -> str | None:
    return folder_id_from_api_row(row)


def parent_id_from_folder_row(row: dict[str, Any]) -> str | None:
    pf = row.get("productFolder")
    if not pf or not isinstance(pf, dict):
        return None
    meta = pf.get("meta") or {}
    href = meta.get("href") or ""
    if not href:
        return None
    return href.rstrip("/").split("/")[-1]


def build_children_map(folders_raw: list[dict[str, Any]]) -> dict[str, list[str]]:
    children: dict[str, list[str]] = defaultdict(list)
    all_ids: set[str] = set()
    for row in folders_raw:
        fid = _folder_id_from_row(row)
        if fid:
            all_ids.add(fid)
    for row in folders_raw:
        fid = _folder_id_from_row(row)
        if not fid:
            continue
        pid = parent_id_from_folder_row(row)
        if pid and pid in all_ids:
            children[pid].append(fid)
    return dict(children)


def descendants_including_self(children: dict[str, list[str]], root: str) -> set[str]:
    out: set[str] = {root}
    stack = [root]
    while stack:
        u = stack.pop()
        for c in children.get(u, []):
            if c not in out:
                out.add(c)
                stack.append(c)
    return out


def expand_folder_selection(folders_raw: list[dict[str, Any]], selected_uuids: list[str]) -> list[str]:
    """
    Для каждого выбранного UUID добавляет сам узел и все дочерние папки
    (по полю productFolder в ответе API).
    """
    if not selected_uuids:
        return []
    children = build_children_map(folders_raw)
    out: set[str] = set()
    for s in selected_uuids:
        out |= descendants_including_self(children, s)
    return list(out)
