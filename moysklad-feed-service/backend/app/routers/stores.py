from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session

from app.database import get_db
from app.deps import get_ms_client
from app.schemas import StoreRow
from app.services.moysklad import MoySkladError

router = APIRouter(prefix="/stores", tags=["stores"])


@router.get("", response_model=list[StoreRow])
def list_stores(db: Session = Depends(get_db)):
    try:
        client = get_ms_client(db)
    except ValueError:
        raise HTTPException(status_code=400, detail="Сначала сохраните логин и пароль МойСклад")
    try:
        raw = client.list_stores()
    except MoySkladError as e:
        raise HTTPException(status_code=502, detail=f"МойСклад: {e} {e.body or ''}")
    out: list[StoreRow] = []
    for r in raw:
        meta = r.get("meta", {}) or {}
        href = meta.get("href", "")
        sid = href.rstrip("/").split("/")[-1] if href else ""
        if not sid:
            continue
        out.append(
            StoreRow(
                id=sid,
                name=r.get("name") or sid,
                archived=bool(r.get("archived", False)),
            )
        )
    return sorted(out, key=lambda x: x.name.lower())
