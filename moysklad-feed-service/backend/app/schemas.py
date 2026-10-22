from datetime import datetime

from pydantic import BaseModel, Field, field_validator
import re


class CredentialsIn(BaseModel):
    login: str = Field(min_length=1, max_length=512)
    password: str = Field(min_length=1, max_length=512)


class CredentialsOut(BaseModel):
    login: str
    has_password: bool
    updated_at: datetime | None = None


class StoreRow(BaseModel):
    id: str
    name: str
    archived: bool = False


class PriceTypeRow(BaseModel):
    id: str
    name: str
    external_code: str = ""


class ProductFolderRow(BaseModel):
    id: str
    name: str
    path_name: str = ""
    parent_id: str | None = None
    archived: bool = False


class ProductStatsOut(BaseModel):
    """Сводка для страницы «Товары»."""

    with_images: int
    catalog_products_total: int | None = None
    without_images: int | None = None


class RebuildAsyncOut(BaseModel):
    job_id: str


class RebuildJobStatusOut(BaseModel):
    status: str
    phase: str = ""
    current: int = 0
    total: int | None = None
    message: str = ""
    offers: int = 0
    public_url: str | None = None
    error: str | None = None
    feed_id: int | None = None


class FeedBase(BaseModel):
    name: str = Field(min_length=1, max_length=255)
    slug: str = Field(min_length=1, max_length=128)
    store_uuids: list[str] = Field(default_factory=list)
    productfolder_uuids: list[str] = Field(default_factory=list)
    batch_limit: int = Field(default=100, ge=5, le=1000)
    schedule_cron: str | None = None
    price_type_id: str | None = None
    old_price_type_id: str | None = None

    @field_validator("slug")
    @classmethod
    def slug_ok(cls, v: str) -> str:
        if not re.match(r"^[a-z0-9][a-z0-9-]*[a-z0-9]$|^[a-z0-9]$", v):
            raise ValueError("slug: только a-z, 0-9, дефис; не начинать/не заканчивать дефисом")
        return v


class FeedCreate(FeedBase):
    pass


class FeedUpdate(BaseModel):
    name: str | None = None
    slug: str | None = None
    store_uuids: list[str] | None = None
    productfolder_uuids: list[str] | None = None
    batch_limit: int | None = Field(default=None, ge=5, le=1000)
    schedule_cron: str | None = None
    price_type_id: str | None = None
    old_price_type_id: str | None = None


class FeedOut(BaseModel):
    id: int
    name: str
    slug: str
    store_uuids: list[str]
    productfolder_uuids: list[str]
    batch_limit: int
    schedule_cron: str | None
    price_type_id: str | None
    old_price_type_id: str | None
    last_built_at: datetime | None
    last_error: str | None
    public_url_hint: str

    class Config:
        from_attributes = True


class RebuildResult(BaseModel):
    ok: bool
    message: str
    items_processed: int = 0


class FeedBuildLogOut(BaseModel):
    id: int
    created_at: datetime
    feed_id: int
    feed_name: str
    feed_slug: str
    trigger: str
    status: str
    offers_count: int
    error_message: str | None
    duration_ms: int | None
    job_id: str | None

    class Config:
        from_attributes = True
