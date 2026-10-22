from sqlalchemy import create_engine, text
from sqlalchemy.orm import sessionmaker, DeclarativeBase

from app.config import settings


class Base(DeclarativeBase):
    pass


engine = create_engine(settings.database_url, pool_pre_ping=True)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)


def init_db() -> None:
    """Создаёт таблицы, если их ещё нет. Безопасно вызывать из API и из воркера."""
    import app.models  # noqa: F401 — регистрация моделей в Base.metadata

    Base.metadata.create_all(bind=engine)
    _ensure_feeds_productfolder_column()
    _ensure_feeds_price_type_columns()
    _ensure_product_images_table()
    _ensure_product_images_last_seen_column()


def _ensure_feeds_productfolder_column() -> None:
    """Добавляет колонку для существующих БД (create_all не меняет схему)."""
    with engine.begin() as conn:
        r = conn.execute(
            text(
                "SELECT 1 FROM information_schema.columns "
                "WHERE table_schema = 'public' AND table_name = 'feeds' "
                "AND column_name = 'productfolder_uuids'"
            )
        )
        if r.fetchone() is None:
            conn.execute(
                text(
                    "ALTER TABLE feeds ADD COLUMN productfolder_uuids JSONB "
                    "NOT NULL DEFAULT '[]'::jsonb"
                )
            )


def _ensure_feeds_price_type_columns() -> None:
    """Добавляет колонки price_type_id и old_price_type_id для существующих БД."""
    with engine.begin() as conn:
        for col in ("price_type_id", "old_price_type_id"):
            r = conn.execute(
                text(
                    "SELECT 1 FROM information_schema.columns "
                    "WHERE table_schema = 'public' AND table_name = 'feeds' "
                    f"AND column_name = '{col}'"
                )
            )
            if r.fetchone() is None:
                conn.execute(
                    text(f"ALTER TABLE feeds ADD COLUMN {col} VARCHAR(64) NULL")
                )


def _ensure_product_images_table() -> None:
    """Создаёт таблицу product_images если её ещё нет (idempotent)."""
    with engine.begin() as conn:
        r = conn.execute(
            text(
                "SELECT 1 FROM information_schema.tables "
                "WHERE table_schema = 'public' AND table_name = 'product_images'"
            )
        )
        if r.fetchone() is None:
            conn.execute(
                text(
                    """
                    CREATE TABLE product_images (
                        id SERIAL PRIMARY KEY,
                        product_uuid VARCHAR(64) NOT NULL UNIQUE,
                        product_name TEXT,
                        product_code VARCHAR(256),
                        product_article VARCHAR(256),
                        folder_uuid VARCHAR(64),
                        images JSONB NOT NULL DEFAULT '[]'::jsonb,
                        created_at TIMESTAMPTZ DEFAULT now(),
                        updated_at TIMESTAMPTZ DEFAULT now()
                    )
                    """
                )
            )
            conn.execute(
                text("CREATE INDEX ix_product_images_product_code ON product_images(product_code)")
            )
            conn.execute(
                text("CREATE INDEX ix_product_images_folder_uuid ON product_images(folder_uuid)")
            )


def _ensure_product_images_last_seen_column() -> None:
    """Добавляет колонку last_seen_in_ms_at для существующих БД."""
    with engine.begin() as conn:
        r = conn.execute(
            text(
                "SELECT 1 FROM information_schema.columns "
                "WHERE table_schema = 'public' AND table_name = 'product_images' "
                "AND column_name = 'last_seen_in_ms_at'"
            )
        )
        if r.fetchone() is None:
            conn.execute(
                text(
                    "ALTER TABLE product_images "
                    "ADD COLUMN last_seen_in_ms_at TIMESTAMPTZ NULL"
                )
            )
            conn.execute(
                text(
                    "CREATE INDEX IF NOT EXISTS ix_product_images_last_seen "
                    "ON product_images(last_seen_in_ms_at)"
                )
            )


def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
