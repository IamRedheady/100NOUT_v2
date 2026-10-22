"""Журнал сборок YML (ручных и по расписанию)."""

from __future__ import annotations

from sqlalchemy.orm import Session

from app.logging_setup import get_logger
from app.models import Feed, FeedBuildLog

log = get_logger("app.build_log")

_ERR_MAX = 4000


def start_manual_async_log(db: Session, feed: Feed, job_id: str) -> None:
    """Строка «идёт ручная сборка» при POST .../rebuild/async (до завершения воркера)."""
    try:
        row = FeedBuildLog(
            feed_id=int(feed.id),
            feed_name=(feed.name or "")[:255],
            feed_slug=(feed.slug or "")[:128],
            trigger="manual",
            status="running",
            offers_count=0,
            error_message=None,
            duration_ms=None,
            job_id=job_id[:64],
        )
        db.add(row)
        db.commit()
    except Exception:
        log.exception("start_manual_async_log failed feed_id=%s", feed.id)
        db.rollback()


def finalize_feed_build_log_by_job_id(
    db: Session,
    job_id: str,
    *,
    ok: bool,
    message: str,
    offers: int,
    duration_ms: int | None,
) -> bool:
    """
    Завершает строку со статусом running с данным job_id.
    False — строки не найдено (например, не удалось записать старт).
    """
    try:
        row = (
            db.query(FeedBuildLog)
            .filter(
                FeedBuildLog.job_id == job_id[:64],
                FeedBuildLog.status == "running",
            )
            .first()
        )
        if not row:
            return False
        row.status = "success" if ok else "error"
        row.offers_count = max(0, int(offers))
        row.error_message = None if ok else (message or "ошибка")[:_ERR_MAX]
        row.duration_ms = duration_ms
        db.commit()
        return True
    except Exception:
        log.exception("finalize_feed_build_log_by_job_id job_id=%s", job_id)
        db.rollback()
        return False


def append_feed_build_log(
    db: Session,
    *,
    feed_id: int,
    feed_name: str,
    feed_slug: str,
    trigger: str,
    ok: bool,
    message: str = "",
    offers: int = 0,
    duration_ms: int | None = None,
    job_id: str | None = None,
) -> None:
    err = None if ok else (message or "ошибка")[:_ERR_MAX]
    try:
        row = FeedBuildLog(
            feed_id=feed_id,
            feed_name=(feed_name or "")[:255],
            feed_slug=(feed_slug or "")[:128],
            trigger=trigger[:32],
            status="success" if ok else "error",
            offers_count=max(0, int(offers)),
            error_message=err,
            duration_ms=duration_ms,
            job_id=(job_id[:64] if job_id else None),
        )
        db.add(row)
        db.commit()
    except Exception:
        log.exception("append_feed_build_log failed feed_id=%s", feed_id)
        db.rollback()


def append_feed_build_log_from_feed(
    db: Session,
    feed: Feed,
    *,
    trigger: str,
    ok: bool,
    message: str = "",
    offers: int = 0,
    duration_ms: int | None = None,
    job_id: str | None = None,
) -> None:
    """Удобная обёртка для объекта Feed."""
    append_feed_build_log(
        db,
        feed_id=int(feed.id),
        feed_name=str(getattr(feed, "name", "") or ""),
        feed_slug=str(getattr(feed, "slug", "") or ""),
        trigger=trigger,
        ok=ok,
        message=message,
        offers=offers,
        duration_ms=duration_ms,
        job_id=job_id,
    )
