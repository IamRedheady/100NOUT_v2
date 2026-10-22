from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session

from app.database import get_db
from app.models import MsCredentials
from app.schemas import CredentialsIn, CredentialsOut
from app.security import decrypt_secret, encrypt_secret

router = APIRouter(prefix="/credentials", tags=["credentials"])


@router.get("", response_model=CredentialsOut)
def get_credentials(db: Session = Depends(get_db)):
    row = db.query(MsCredentials).order_by(MsCredentials.id.asc()).first()
    if not row:
        return CredentialsOut(login="", has_password=False, updated_at=None)
    try:
        login = decrypt_secret(row.login_encrypted)
    except ValueError:
        raise HTTPException(status_code=500, detail="Не удалось расшифровать логин (FERNET_KEY?)")
    return CredentialsOut(login=login, has_password=True, updated_at=row.updated_at)


@router.put("", response_model=CredentialsOut)
def put_credentials(body: CredentialsIn, db: Session = Depends(get_db)):
    row = db.query(MsCredentials).order_by(MsCredentials.id.asc()).first()
    login_enc = encrypt_secret(body.login)
    pass_enc = encrypt_secret(body.password)
    if row:
        row.login_encrypted = login_enc
        row.password_encrypted = pass_enc
    else:
        row = MsCredentials(login_encrypted=login_enc, password_encrypted=pass_enc)
        db.add(row)
    db.commit()
    db.refresh(row)
    return CredentialsOut(login=body.login, has_password=True, updated_at=row.updated_at)
