from sqlalchemy.orm import Session

from app.models import MsCredentials
from app.security import decrypt_secret
from app.services.moysklad import MoySkladClient


def get_ms_client(db: Session) -> MoySkladClient:
    row = db.query(MsCredentials).order_by(MsCredentials.id.asc()).first()
    if not row:
        raise ValueError("Учётные данные МойСклад не заданы")
    login = decrypt_secret(row.login_encrypted)
    password = decrypt_secret(row.password_encrypted)
    return MoySkladClient(login, password)
