"""Фоновая пересборка YML (BackgroundTasks)."""

from __future__ import annotations

import time
from typing import Any

from sqlalchemy.orm import Session

from app.config import settings
from app.database import SessionLocal
from app.feed_jobs import rebuild_feed
from app.logging_setup import get_logger
from app.models import Feed, MsCredentials
from app.rebuild_job_store import update_job
from app.security import decrypt_secret
from app.services.feed_build_log import (
    append_feed_build_log,
    append_feed_build_log_from_feed,
    finalize_feed_build_log_by_job_id,
)
from app.services.moysklad import MoySkladClient

log = get_logger("app.rebuild")


def _finalize_journal(
    db: Session,
    job_id: str,
    *,
    ok: bool,
    message: str,
    offers: int,
    duration_ms: int,
    feed_id: int,
    f: Feed | None,
) -> None:
    if finalize_feed_build_log_by_job_id(
        db,
        job_id,
        ok=ok,
        message=message,
        offers=offers,
        duration_ms=duration_ms,
    ):
        return
    if f is not None:
        append_feed_build_log_from_feed(
            db,
            f,
            trigger="manual",
            ok=ok,
            message=message,
            offers=offers,
            duration_ms=duration_ms,
            job_id=job_id,
        )
    else:
        append_feed_build_log(
            db,
            feed_id=feed_id,
            feed_name="",
            feed_slug="",
            trigger="manual",
            ok=ok,
            message=message,
            offers=offers,
            duration_ms=duration_ms,
            job_id=job_id,
        )


def run_rebuild_background(job_id: str, feed_id: int) -> None:
    started = time.monotonic()
    log.info("bg rebuild: job_id=%s feed_id=%s START", job_id, feed_id)
    db = SessionLocal()
    f: Feed | None = None

    def _elapsed_ms() -> int:
        return int((time.monotonic() - started) * 1000)

    try:
        update_job(
            job_id,
            status="running",
            phase="prepare",
            message="Готовим параметры выгрузки…",
        )
        f = db.query(Feed).filter(Feed.id == feed_id).first()
        if not f:
            log.error("bg rebuild: job_id=%s feed_id=%s not found", job_id, feed_id)
            update_job(job_id, status="error", error="Выгрузка не найдена")
            _finalize_journal(
                db,
                job_id,
                ok=False,
                message="Выгрузка не найдена",
                offers=0,
                duration_ms=_elapsed_ms(),
                feed_id=feed_id,
                f=None,
            )
            return
        row = db.query(MsCredentials).order_by(MsCredentials.id.asc()).first()
        if not row:
            log.error("bg rebuild: job_id=%s no MS credentials", job_id)
            update_job(job_id, status="error", error="Нет учётных данных МойСклад")
            _finalize_journal(
                db,
                job_id,
                ok=False,
                message="Нет учётных данных МойСклад",
                offers=0,
                duration_ms=_elapsed_ms(),
                feed_id=feed_id,
                f=f,
            )
            return
        login = decrypt_secret(row.login_encrypted)
        password = decrypt_secret(row.password_encrypted)
        client = MoySkladClient(login, password)
        slug = f.slug
        log.info(
            "bg rebuild: job_id=%s feed=%r slug=%r batch=%d stores=%d folders=%d",
            job_id,
            f.name,
            slug,
            f.batch_limit,
            len(f.store_uuids or []),
            len(f.productfolder_uuids or []),
        )

        last_log_ts = 0.0

        def on_progress(payload: dict[str, Any]) -> None:
            nonlocal last_log_ts
            data = {"phase": "loading", **payload}
            update_job(job_id, **data)
            now = time.monotonic()
            if now - last_log_ts >= 2.0 or "message" in payload:
                last_log_ts = now
                log.info(
                    "bg rebuild: job_id=%s progress · current=%s total=%s offers=%s · %s",
                    job_id,
                    payload.get("current"),
                    payload.get("total"),
                    payload.get("offers"),
                    payload.get("message", ""),
                )

        ok, msg, n = rebuild_feed(db, client, f, on_progress=on_progress)
        elapsed = time.monotonic() - started
        elapsed_ms = int(elapsed * 1000)
        if not ok:
            log.error(
                "bg rebuild: job_id=%s FAILED in %.2fs · %s",
                job_id, elapsed, (msg or "")[:300],
            )
            update_job(
                job_id,
                status="error",
                error=msg,
                offers=n,
                phase="error",
                message=f"Ошибка: {msg}",
            )
            _finalize_journal(
                db,
                job_id,
                ok=False,
                message=msg or "ошибка сборки",
                offers=n,
                duration_ms=elapsed_ms,
                feed_id=feed_id,
                f=f,
            )
            return
        url = f"{settings.public_base_url.rstrip('/')}/feeds/{slug}.yml"
        log.info(
            "bg rebuild: job_id=%s DONE in %.2fs · offers=%d · url=%s",
            job_id, elapsed, n, url,
        )
        update_job(
            job_id,
            status="done",
            message=f"Готово · офферов в YML: {n}",
            offers=n,
            public_url=url,
            phase="done",
            current=n,
            total=n,
        )
        _finalize_journal(
            db,
            job_id,
            ok=True,
            message="ok",
            offers=n,
            duration_ms=elapsed_ms,
            feed_id=feed_id,
            f=f,
        )
    except Exception as e:
        log.exception("bg rebuild: job_id=%s unhandled error", job_id)
        update_job(job_id, status="error", error=str(e), message=f"Ошибка: {e}")
        try:
            _finalize_journal(
                db,
                job_id,
                ok=False,
                message=str(e),
                offers=0,
                duration_ms=_elapsed_ms(),
                feed_id=feed_id,
                f=f,
            )
        except Exception:
            log.exception("bg rebuild: journal finalize failed job_id=%s", job_id)
    finally:
        db.close()
