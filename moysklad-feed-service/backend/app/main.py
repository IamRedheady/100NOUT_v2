import os
from contextlib import asynccontextmanager

from fastapi import Depends, FastAPI, HTTPException, Response
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles
from sqlalchemy.orm import Session

from app.config import settings
from app.database import get_db, init_db
from app.logging_setup import get_logger, setup_logging
from app.models import Feed
from app.routers import credentials, feeds, folders, prices, products, stores

setup_logging()
log = get_logger("app.main")

# Создаём папку для медиафайлов до монтирования StaticFiles
os.makedirs(settings.media_dir, exist_ok=True)


@asynccontextmanager
async def lifespan(_: FastAPI):
    init_db()
    log.info("media_dir=%s", settings.media_dir)
    yield


app = FastAPI(title="MoySklad YML Feed Service", lifespan=lifespan)

app.add_middleware(
    CORSMiddleware,
    allow_origins=[o.strip() for o in settings.cors_origins.split(",") if o.strip()],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Статические файлы изображений товаров → /media/{uuid}/{filename}
app.mount("/media", StaticFiles(directory=settings.media_dir), name="media")

app.include_router(credentials.router, prefix=settings.api_prefix)
app.include_router(stores.router, prefix=settings.api_prefix)
app.include_router(folders.router, prefix=settings.api_prefix)
app.include_router(prices.router, prefix=settings.api_prefix)
app.include_router(products.router, prefix=settings.api_prefix)
app.include_router(feeds.router, prefix=settings.api_prefix)


@app.get("/health")
def health():
    return {"status": "ok"}


@app.get("/feeds/{slug}.yml")
def public_yml(slug: str, db: Session = Depends(get_db)):
    f = db.query(Feed).filter(Feed.slug == slug).first()
    if not f or not f.yml_body:
        raise HTTPException(status_code=404, detail="Фид не найден или ещё не собран")
    return Response(
        content=f.yml_body,
        media_type="application/xml; charset=utf-8",
    )
