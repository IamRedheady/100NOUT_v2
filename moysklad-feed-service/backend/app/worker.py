"""
ARQ worker: периодический запуск фидов по cron.
Запуск: arq app.worker.WorkerSettings
"""

import time

from arq import cron
from arq.connections import RedisSettings

from app.config import settings
from app.database import SessionLocal, init_db
from app.feed_jobs import run_due_feeds
from app.logging_setup import get_logger, setup_logging
from app.services.products_sync import (
    ORPHAN_THRESHOLD_DAYS,
    cleanup_orphan_images,
)

setup_logging()
log = get_logger("app.worker")


async def on_startup(_ctx):
    log.info("ARQ worker startup: redis=%s db=%s", settings.redis_url, settings.database_url)
    init_db()
    log.info("init_db: ok")


async def on_shutdown(_ctx):
    log.info("ARQ worker shutdown")


async def daily_cleanup_orphan_images(_ctx):
    """Раз в сутки очищает файлы у товаров, не виденных в МС > N дней."""
    log.info("cleanup tick: starting orphan-images cleanup")
    db = SessionLocal()
    try:
        result = cleanup_orphan_images(db, threshold_days=ORPHAN_THRESHOLD_DAYS)
        log.info(
            "cleanup tick: orphans=%d removed_files=%d freed=%d bytes",
            result["orphans"],
            result["removed_files"],
            result["freed_bytes"],
        )
    except Exception:
        log.exception("cleanup tick: unhandled error")
    finally:
        db.close()


async def tick_scheduled_feeds(_ctx):
    log.info("cron tick: scanning feeds for due cron schedules")
    started = time.monotonic()
    db = SessionLocal()
    try:
        results = run_due_feeds(db)
        if not results:
            log.info("cron tick: nothing due (took %.2fs)", time.monotonic() - started)
            return
        for fid, ok, msg in results:
            if ok:
                log.info("cron tick: feed_id=%s OK · %s", fid, msg)
            else:
                log.error("cron tick: feed_id=%s FAILED · %s", fid, msg)
        log.info("cron tick: done in %.2fs", time.monotonic() - started)
    except Exception:
        log.exception("cron tick: unhandled error")
    finally:
        db.close()


class WorkerSettings:
    on_startup = on_startup
    on_shutdown = on_shutdown
    redis_settings = RedisSettings.from_dsn(settings.redis_url)
    cron_jobs = [
        cron(tick_scheduled_feeds, minute=set(range(60)), second=0),
        # Очистка осиротевших картинок раз в сутки в 03:15
        cron(daily_cleanup_orphan_images, hour={3}, minute={15}, second=0),
    ]
