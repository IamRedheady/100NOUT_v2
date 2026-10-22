from datetime import datetime

from sqlalchemy import DateTime, Index, Integer, String, Text, func, text
from sqlalchemy.dialects.postgresql import JSONB
from sqlalchemy.orm import Mapped, mapped_column

from app.database import Base


class MsCredentials(Base):
    __tablename__ = "ms_credentials"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    login_encrypted: Mapped[str] = mapped_column(Text, nullable=False)
    password_encrypted: Mapped[str] = mapped_column(Text, nullable=False)
    updated_at: Mapped[datetime] = mapped_column(DateTime(timezone=True), server_default=func.now(), onupdate=func.now())


class Feed(Base):
    __tablename__ = "feeds"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    slug: Mapped[str] = mapped_column(String(128), unique=True, nullable=False, index=True)
    # UUID строк складов МойСклад (пустой список = все склады при сборке остатков)
    store_uuids: Mapped[list] = mapped_column(JSONB, nullable=False, server_default=text("'[]'::jsonb"))
    # UUID папок товаров (productFolder); пустой список = все категории
    productfolder_uuids: Mapped[list] = mapped_column(
        JSONB, nullable=False, server_default=text("'[]'::jsonb")
    )
    batch_limit: Mapped[int] = mapped_column(Integer, nullable=False, default=100)
    # cron: "*/15 * * * *" или пусто — только ручной пересчёт
    schedule_cron: Mapped[str | None] = mapped_column(String(128), nullable=True)
    # UUID типа цены МойСклад для тега <price> в YML (null = первая/дефолтная)
    price_type_id: Mapped[str | None] = mapped_column(String(64), nullable=True)
    # UUID типа цены для тега <oldprice> (зачёркнутая цена, null = не выводить)
    old_price_type_id: Mapped[str | None] = mapped_column(String(64), nullable=True)
    yml_body: Mapped[str | None] = mapped_column(Text, nullable=True)
    last_error: Mapped[str | None] = mapped_column(Text, nullable=True)
    last_built_at: Mapped[datetime | None] = mapped_column(DateTime(timezone=True), nullable=True)
    created_at: Mapped[datetime] = mapped_column(DateTime(timezone=True), server_default=func.now())
    updated_at: Mapped[datetime] = mapped_column(DateTime(timezone=True), server_default=func.now(), onupdate=func.now())


class FeedBuildLog(Base):
    """История сборок YML: running (async стартовал), success | error (итог)."""

    __tablename__ = "feed_build_logs"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    feed_id: Mapped[int] = mapped_column(Integer, nullable=False, index=True)
    feed_name: Mapped[str] = mapped_column(String(255), nullable=False, default="")
    feed_slug: Mapped[str] = mapped_column(String(128), nullable=False, default="")
    # manual | scheduled
    trigger: Mapped[str] = mapped_column(String(32), nullable=False)
    # running | success | error
    status: Mapped[str] = mapped_column(String(16), nullable=False)
    offers_count: Mapped[int] = mapped_column(Integer, nullable=False, default=0)
    error_message: Mapped[str | None] = mapped_column(Text, nullable=True)
    duration_ms: Mapped[int | None] = mapped_column(Integer, nullable=True)
    job_id: Mapped[str | None] = mapped_column(String(64), nullable=True)
    created_at: Mapped[datetime] = mapped_column(
        DateTime(timezone=True), server_default=func.now(), index=True
    )


class ProductImages(Base):
    """
    Локальный кеш товаров МойСклад + загруженные вручную изображения.
    Имя «product_images» оставлено для совместимости миграций — фактически это
    «база товаров»: пустой images означает «товар известен, но без фото», такие
    позиции в YML не попадают.
    """

    __tablename__ = "product_images"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    # UUID товара/модификации в МойСклад
    product_uuid: Mapped[str] = mapped_column(String(64), nullable=False, unique=True, index=True)
    # Кешированные данные из МС (для отображения без запросов)
    product_name: Mapped[str | None] = mapped_column(Text, nullable=True)
    product_code: Mapped[str | None] = mapped_column(String(256), nullable=True)
    product_article: Mapped[str | None] = mapped_column(String(256), nullable=True)
    folder_uuid: Mapped[str | None] = mapped_column(String(64), nullable=True)
    # Список имён файлов (ordered: первый = главное изображение)
    images: Mapped[list] = mapped_column(
        JSONB, nullable=False, server_default=text("'[]'::jsonb")
    )
    # Когда товар последний раз был замечен в МС (после ресинхрона)
    last_seen_in_ms_at: Mapped[datetime | None] = mapped_column(
        DateTime(timezone=True), nullable=True
    )
    created_at: Mapped[datetime] = mapped_column(DateTime(timezone=True), server_default=func.now())
    updated_at: Mapped[datetime] = mapped_column(
        DateTime(timezone=True), server_default=func.now(), onupdate=func.now()
    )

    __table_args__ = (
        Index("ix_product_images_product_code", "product_code"),
        Index("ix_product_images_folder_uuid", "folder_uuid"),
        Index("ix_product_images_last_seen", "last_seen_in_ms_at"),
    )
