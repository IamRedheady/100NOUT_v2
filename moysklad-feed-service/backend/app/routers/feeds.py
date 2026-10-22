import time

from fastapi import APIRouter, BackgroundTasks, Depends, HTTPException, Query
from sqlalchemy.orm import Session

from app.config import settings
from app.database import get_db
from app.deps import get_ms_client
from app.feed_jobs import rebuild_feed
from app.models import Feed, FeedBuildLog
from app.services.feed_build_log import (
    append_feed_build_log_from_feed,
    start_manual_async_log,
)
from app.rebuild_job_store import create_job, get_job as get_stored_job
from app.schemas import (
    FeedBuildLogOut,
    FeedCreate,
    FeedOut,
    FeedUpdate,
    RebuildAsyncOut,
    RebuildJobStatusOut,
    RebuildResult,
)
from app.services.moysklad import MoySkladError
from app.services.rebuild_runner import run_rebuild_background

router = APIRouter(prefix="/feeds", tags=["feeds"])


def _hint(slug: str) -> str:
    return f"{settings.public_base_url.rstrip('/')}/feeds/{slug}.yml"


def _to_out(f: Feed) -> FeedOut:
    return FeedOut(
        id=f.id,
        name=f.name,
        slug=f.slug,
        store_uuids=list(f.store_uuids or []),
        productfolder_uuids=list(f.productfolder_uuids or []),
        batch_limit=f.batch_limit,
        schedule_cron=f.schedule_cron,
        price_type_id=f.price_type_id,
        old_price_type_id=f.old_price_type_id,
        last_built_at=f.last_built_at,
        last_error=f.last_error,
        public_url_hint=_hint(f.slug),
    )


@router.get("/rebuild-jobs/{job_id}", response_model=RebuildJobStatusOut)
def rebuild_job_status(job_id: str):
    data = get_stored_job(job_id)
    if not data:
        raise HTTPException(status_code=404, detail="Задача не найдена")
    return RebuildJobStatusOut(
        status=data["status"],
        phase=data.get("phase") or "",
        current=int(data.get("current") or 0),
        total=data.get("total"),
        message=data.get("message") or "",
        offers=int(data.get("offers") or 0),
        public_url=data.get("public_url"),
        error=data.get("error"),
        feed_id=data.get("feed_id"),
    )


@router.get("/build-log", response_model=list[FeedBuildLogOut])
def list_build_log(
    db: Session = Depends(get_db),
    limit: int = Query(100, ge=1, le=500),
    offset: int = Query(0, ge=0),
    feed_id: int | None = Query(None, ge=1),
):
    q = db.query(FeedBuildLog).order_by(FeedBuildLog.created_at.desc())
    if feed_id is not None:
        q = q.filter(FeedBuildLog.feed_id == feed_id)
    rows = q.offset(offset).limit(limit).all()
    return [
        FeedBuildLogOut(
            id=r.id,
            created_at=r.created_at,
            feed_id=r.feed_id,
            feed_name=r.feed_name,
            feed_slug=r.feed_slug,
            trigger=r.trigger,
            status=r.status,
            offers_count=r.offers_count,
            error_message=r.error_message,
            duration_ms=r.duration_ms,
            job_id=r.job_id,
        )
        for r in rows
    ]


@router.get("", response_model=list[FeedOut])
def list_feeds(db: Session = Depends(get_db)):
    rows = db.query(Feed).order_by(Feed.id.asc()).all()
    return [_to_out(f) for f in rows]


@router.post("", response_model=FeedOut)
def create_feed(body: FeedCreate, db: Session = Depends(get_db)):
    if db.query(Feed).filter(Feed.slug == body.slug).first():
        raise HTTPException(status_code=409, detail="Такой slug уже занят")
    f = Feed(
        name=body.name,
        slug=body.slug,
        store_uuids=body.store_uuids,
        productfolder_uuids=body.productfolder_uuids,
        batch_limit=body.batch_limit,
        schedule_cron=body.schedule_cron.strip() if body.schedule_cron else None,
        price_type_id=body.price_type_id or None,
        old_price_type_id=body.old_price_type_id or None,
    )
    db.add(f)
    db.commit()
    db.refresh(f)
    return _to_out(f)


@router.post("/{feed_id}/rebuild/async", response_model=RebuildAsyncOut)
def rebuild_feed_async(
    feed_id: int,
    background_tasks: BackgroundTasks,
    db: Session = Depends(get_db),
):
    try:
        get_ms_client(db)
    except ValueError:
        raise HTTPException(
            status_code=400,
            detail="Сначала сохраните учётные данные МойСклад",
        )
    f = db.query(Feed).filter(Feed.id == feed_id).first()
    if not f:
        raise HTTPException(status_code=404)
    job_id = create_job(feed_id)
    start_manual_async_log(db, f, job_id)
    background_tasks.add_task(run_rebuild_background, job_id, feed_id)
    return RebuildAsyncOut(job_id=job_id)


@router.get("/{feed_id}", response_model=FeedOut)
def get_feed(feed_id: int, db: Session = Depends(get_db)):
    f = db.query(Feed).filter(Feed.id == feed_id).first()
    if not f:
        raise HTTPException(status_code=404)
    return _to_out(f)


@router.patch("/{feed_id}", response_model=FeedOut)
def patch_feed(feed_id: int, body: FeedUpdate, db: Session = Depends(get_db)):
    f = db.query(Feed).filter(Feed.id == feed_id).first()
    if not f:
        raise HTTPException(status_code=404)
    if body.name is not None:
        f.name = body.name
    if body.slug is not None:
        if db.query(Feed).filter(Feed.slug == body.slug, Feed.id != feed_id).first():
            raise HTTPException(status_code=409, detail="Такой slug уже занят")
        f.slug = body.slug
    if body.store_uuids is not None:
        f.store_uuids = body.store_uuids
    if body.productfolder_uuids is not None:
        f.productfolder_uuids = body.productfolder_uuids
    if body.batch_limit is not None:
        f.batch_limit = body.batch_limit
    if body.schedule_cron is not None:
        f.schedule_cron = body.schedule_cron.strip() or None
    if body.price_type_id is not None:
        f.price_type_id = body.price_type_id or None
    if body.old_price_type_id is not None:
        f.old_price_type_id = body.old_price_type_id or None
    db.commit()
    db.refresh(f)
    return _to_out(f)


@router.delete("/{feed_id}")
def delete_feed(feed_id: int, db: Session = Depends(get_db)):
    f = db.query(Feed).filter(Feed.id == feed_id).first()
    if not f:
        raise HTTPException(status_code=404)
    db.delete(f)
    db.commit()
    return {"ok": True}


@router.post("/{feed_id}/rebuild", response_model=RebuildResult)
def rebuild_feed_endpoint(feed_id: int, db: Session = Depends(get_db)):
    try:
        client = get_ms_client(db)
    except ValueError:
        raise HTTPException(status_code=400, detail="Сначала сохраните учётные данные МойСклад")
    f = db.query(Feed).filter(Feed.id == feed_id).first()
    if not f:
        raise HTTPException(status_code=404)
    t0 = time.monotonic()
    try:
        ok, msg, n = rebuild_feed(db, client, f)
        elapsed_ms = int((time.monotonic() - t0) * 1000)
        append_feed_build_log_from_feed(
            db,
            f,
            trigger="manual",
            ok=ok,
            message=msg if not ok else "ok",
            offers=n,
            duration_ms=elapsed_ms,
            job_id=None,
        )
    except MoySkladError as e:
        elapsed_ms = int((time.monotonic() - t0) * 1000)
        detail = str(e)
        append_feed_build_log_from_feed(
            db,
            f,
            trigger="manual",
            ok=False,
            message=detail,
            offers=0,
            duration_ms=elapsed_ms,
            job_id=None,
        )
        raise HTTPException(status_code=502, detail=detail)
    return RebuildResult(ok=ok, message=msg, items_processed=n)
