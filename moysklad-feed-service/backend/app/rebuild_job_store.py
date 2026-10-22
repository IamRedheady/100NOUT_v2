"""In-memory статусы асинхронной пересборки YML (один процесс API)."""

from __future__ import annotations

import copy
import threading
import uuid
from typing import Any

_lock = threading.Lock()
_jobs: dict[str, dict[str, Any]] = {}


def create_job(feed_id: int) -> str:
    jid = str(uuid.uuid4())
    with _lock:
        _jobs[jid] = {
            "status": "pending",
            "phase": "",
            "current": 0,
            "total": None,
            "message": "",
            "offers": 0,
            "public_url": None,
            "error": None,
            "feed_id": feed_id,
        }
    return jid


def update_job(job_id: str, **kwargs: Any) -> None:
    with _lock:
        if job_id in _jobs:
            _jobs[job_id].update(kwargs)


def get_job(job_id: str) -> dict[str, Any] | None:
    with _lock:
        j = _jobs.get(job_id)
        return copy.deepcopy(j) if j else None
