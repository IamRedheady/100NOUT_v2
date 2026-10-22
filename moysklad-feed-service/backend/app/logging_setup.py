"""Единая настройка логирования (FastAPI + arq worker + фоновые задачи)."""

from __future__ import annotations

import logging
import logging.config
import os
import sys

_CONFIGURED = False


def setup_logging() -> None:
    """Идемпотентно настраивает корневые логгеры. Вызывается из API и воркера."""
    global _CONFIGURED
    if _CONFIGURED:
        return
    _CONFIGURED = True

    level = os.getenv("LOG_LEVEL", "INFO").upper()

    fmt = "%(asctime)s | %(levelname)-7s | %(name)-26s | %(message)s"

    logging.config.dictConfig(
        {
            "version": 1,
            "disable_existing_loggers": False,
            "formatters": {
                "default": {
                    "format": fmt,
                    "datefmt": "%Y-%m-%d %H:%M:%S",
                }
            },
            "handlers": {
                "stdout": {
                    "class": "logging.StreamHandler",
                    "stream": sys.stdout,
                    "formatter": "default",
                    "level": level,
                }
            },
            "root": {"level": level, "handlers": ["stdout"]},
            "loggers": {
                "uvicorn": {"level": level, "handlers": ["stdout"], "propagate": False},
                "uvicorn.error": {"level": level, "handlers": ["stdout"], "propagate": False},
                "uvicorn.access": {"level": "WARNING", "handlers": ["stdout"], "propagate": False},
                "arq": {"level": level, "handlers": ["stdout"], "propagate": False},
                "arq.worker": {"level": level, "handlers": ["stdout"], "propagate": False},
                "httpx": {"level": "WARNING", "handlers": ["stdout"], "propagate": False},
                "httpcore": {"level": "WARNING", "handlers": ["stdout"], "propagate": False},
                "sqlalchemy.engine": {"level": "WARNING", "handlers": ["stdout"], "propagate": False},
            },
        }
    )

    logging.getLogger("app").info("Logging configured (level=%s)", level)


def get_logger(name: str) -> logging.Logger:
    if not _CONFIGURED:
        setup_logging()
    return logging.getLogger(name)
