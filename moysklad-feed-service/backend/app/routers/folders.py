from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session

from app.database import get_db
from app.deps import get_ms_client
from app.folder_tree import folder_id_from_api_row, parent_id_from_folder_row
from app.schemas import ProductFolderRow
from app.services.moysklad import MoySkladError

router = APIRouter(prefix="/product-folders", tags=["product-folders"])


@router.get("", response_model=list[ProductFolderRow])
def list_folders(db: Session = Depends(get_db)):
    try:
        client = get_ms_client(db)
    except ValueError:
        raise HTTPException(status_code=400, detail="Сначала сохраните логин и пароль МойСклад")
    try:
        raw = client.list_product_folders()
    except MoySkladError as e:
        raise HTTPException(status_code=502, detail=f"МойСклад: {e} {e.body or ''}")
    seen: set[str] = set()
    out: list[ProductFolderRow] = []
    for r in raw:
        fid = folder_id_from_api_row(r)
        if not fid or fid in seen:
            continue
        seen.add(fid)
        out.append(
            ProductFolderRow(
                id=fid,
                name=r.get("name") or fid,
                path_name=(r.get("pathName") or "") if isinstance(r.get("pathName"), str) else "",
                parent_id=parent_id_from_folder_row(r),
                archived=bool(r.get("archived", False)),
            )
        )
    return sorted(out, key=lambda x: (x.path_name.lower(), x.name.lower()))
