from __future__ import annotations

import time
from datetime import datetime, timezone

from croniter import croniter
from sqlalchemy.orm import Session

from app.logging_setup import get_logger
from app.models import Feed, MsCredentials
from app.security import decrypt_secret
from app.services.feed_build_log import append_feed_build_log_from_feed
from app.services.moysklad import MoySkladClient
from app.services.yml_feed import ProgressCb, build_yml_for_feed

log = get_logger("app.jobs")


def rebuild_feed(
    db: Session,
    client: MoySkladClient,
    feed: Feed,
    on_progress: ProgressCb | None = None,
) -> tuple[bool, str, int]:
    log.info("rebuild_feed: start feed_id=%s slug=%r", feed.id, feed.slug)
    started = time.monotonic()
    yml, n, err = build_yml_for_feed(db, client, feed, on_progress=on_progress)
    elapsed = time.monotonic() - started
    now = datetime.now(timezone.utc)
    if err:
        feed.last_error = err[:8000]
        feed.last_built_at = now
        db.commit()
        log.error(
            "rebuild_feed: feed_id=%s FAILED in %.2fs · %s",
            feed.id, elapsed, err[:200],
        )
        return False, err, 0
    feed.yml_body = yml
    feed.last_error = None
    feed.last_built_at = now
    db.commit()
    log.info(
        "rebuild_feed: feed_id=%s OK in %.2fs · offers=%d · yml_size=%dKB",
        feed.id, elapsed, n, len(yml.encode("utf-8")) // 1024,
    )
    return True, "ok", n


def feed_is_due(feed: Feed, now: datetime | None = None) -> bool:
    if not feed.schedule_cron or not str(feed.schedule_cron).strip():
        return False
    now = now or datetime.now(timezone.utc)
    base = feed.last_built_at or feed.created_at
    if base is None:
        return True
    # croniter стабильнее с naive UTC
    if base.tzinfo:
        base_na = base.astimezone(timezone.utc).replace(tzinfo=None)
    else:
        base_na = base
    now_na = now.astimezone(timezone.utc).replace(tzinfo=None)
    try:
        it = croniter(feed.schedule_cron.strip(), base_na)
        nxt = it.get_next(datetime)
        if isinstance(nxt, float):
            return False
        nxt_na = nxt.replace(tzinfo=None) if nxt.tzinfo else nxt
        return now_na >= nxt_na
    except Exception:
        return False


def ms_client_from_db(db: Session) -> MoySkladClient:
    row = db.query(MsCredentials).order_by(MsCredentials.id.asc()).first()
    if not row:
        raise ValueError("no credentials")
    login = decrypt_secret(row.login_encrypted)
    password = decrypt_secret(row.password_encrypted)
    return MoySkladClient(login, password)


def run_due_feeds(db: Session) -> list[tuple[int, bool, str]]:
    results: list[tuple[int, bool, str]] = []
    try:
        client = ms_client_from_db(db)
    except ValueError:
        log.warning("run_due_feeds: no MS credentials in DB, skip")
        return results
    feeds = db.query(Feed).all()
    now = datetime.now(timezone.utc)
    due = [f for f in feeds if feed_is_due(f, now)]
    log.info(
        "run_due_feeds: scanned=%d due=%d → %s",
        len(feeds),
        len(due),
        [f.id for f in due],
    )
    for f in due:
        log.info(
            "run_due_feeds: feed_id=%s slug=%r cron=%r last_built_at=%s",
            f.id, f.slug, f.schedule_cron, f.last_built_at,
        )
        t0 = time.monotonic()
        ok, msg, n = rebuild_feed(db, client, f)
        elapsed_ms = int((time.monotonic() - t0) * 1000)
        append_feed_build_log_from_feed(
            db,
            f,
            trigger="scheduled",
            ok=ok,
            message=msg if not ok else "ok",
            offers=n,
            duration_ms=elapsed_ms,
            job_id=None,
        )
        results.append((f.id, ok, f"{msg} ({n} offers)"))
    return results
