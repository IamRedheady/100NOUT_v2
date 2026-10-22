from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session

from app.database import get_db
from app.deps import get_ms_client
from app.logging_setup import get_logger
from app.schemas import PriceTypeRow
from app.services.moysklad import MoySkladError

log = get_logger("app.prices")

router = APIRouter(prefix="/price-types", tags=["price-types"])


@router.get("", response_model=list[PriceTypeRow])
def list_price_types(db: Session = Depends(get_db)):
    try:
        client = get_ms_client(db)
    except ValueError:
        raise HTTPException(status_code=400, detail="Сначала сохраните логин и пароль МойСклад")
    try:
        raw = client.list_price_types()
        log.info("price-types: got %d rows from MS", len(raw))
    except MoySkladError as e:
        log.error("price-types: MoySkladError %s · %s", e, (e.body or "")[:300])
        raise HTTPException(status_code=502, detail=f"МойСклад: {e} {e.body or ''}")
    except Exception as e:
        log.exception("price-types: unexpected error")
        raise HTTPException(status_code=500, detail=f"Ошибка: {e}")
    return [
        PriceTypeRow(
            id=r.get("id") or "",
            name=r.get("name") or "",
            external_code=r.get("externalCode") or "",
        )
        for r in raw
        if r.get("id")
    ]
