import { useCallback, useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { api, type Feed, type RebuildJobStatus } from "../api";
import { RebuildProgressBlock } from "../components/RebuildProgressBlock";
import { AlertBanner } from "../components/AlertBanner";
import { Button } from "../components/ui/Button";
import { Card } from "../components/ui/Card";
import {
  clearRebuildJobForFeed,
  readRebuildJobsMap,
  setRebuildJobForFeed,
} from "../rebuildJobStorage";

export function FeedsListPage() {
  const navigate = useNavigate();
  const [feeds, setFeeds] = useState<Feed[]>([]);
  const [err, setErr] = useState<string | null>(null);
  const [msg, setMsg] = useState<string | null>(null);
  const [statusByFeed, setStatusByFeed] = useState<
    Record<number, RebuildJobStatus | null>
  >({});
  const [startingId, setStartingId] = useState<number | null>(null);

  const load = useCallback(async () => {
    const list = await api<Feed[]>("/api/feeds");
    setFeeds(list);
  }, []);

  useEffect(() => {
    load().catch((e) => setErr(String(e)));
  }, [load]);

  useEffect(() => {
    let cancelled = false;
    let timeoutId: ReturnType<typeof setTimeout>;

    const tick = async () => {
      if (cancelled) return;
      const map = readRebuildJobsMap();
      const entries = Object.entries(map);
      if (entries.length === 0) {
        setStatusByFeed({});
        timeoutId = setTimeout(tick, 800);
        return;
      }
      const next: Record<number, RebuildJobStatus> = {};
      let finished = false;
      for (const [fid, jid] of entries) {
        const feedId = Number(fid);
        try {
          const s = await api<RebuildJobStatus>(`/api/feeds/rebuild-jobs/${jid}`);
          next[feedId] = s;
          if (s.status === "done" || s.status === "error") {
            clearRebuildJobForFeed(feedId);
            finished = true;
          }
        } catch {
          clearRebuildJobForFeed(feedId);
        }
      }
      if (!cancelled) {
        setStatusByFeed(next);
        if (finished) load().catch(() => {});
        timeoutId = setTimeout(tick, 450);
      }
    };

    tick();
    return () => {
      cancelled = true;
      clearTimeout(timeoutId);
    };
  }, [load]);

  async function remove(id: number) {
    if (!confirm("Удалить выгрузку?")) return;
    setErr(null);
    setMsg(null);
    try {
      await api(`/api/feeds/${id}`, { method: "DELETE" });
      clearRebuildJobForFeed(id);
      setMsg("Удалено");
      await load();
    } catch (e) {
      setErr(String(e));
    }
  }

  async function startRebuild(feedId: number) {
    const st = statusByFeed[feedId];
    if (
      st &&
      (st.status === "pending" || st.status === "running")
    ) {
      return;
    }
    setErr(null);
    setMsg(null);
    setStartingId(feedId);
    try {
      const r = await api<{ job_id: string }>(
        `/api/feeds/${feedId}/rebuild/async`,
        { method: "POST" },
      );
      setRebuildJobForFeed(feedId, r.job_id);
      setStatusByFeed((prev) => ({
        ...prev,
        [feedId]: {
          status: "pending",
          phase: "",
          current: 0,
          total: null,
          message: "",
          offers: 0,
          public_url: null,
          error: null,
          feed_id: feedId,
        },
      }));
    } catch (e) {
      setErr(String(e));
    } finally {
      setStartingId(null);
    }
  }

  return (
    <>
      <AlertBanner error={err} message={msg} />
      <div className="mx-auto max-w-5xl space-y-8 px-4 py-8 sm:px-6">
        <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <h2 className="text-xl font-bold text-slate-900">Выгрузки</h2>
            <p className="mt-1 text-sm text-slate-500">
              Каждая выгрузка — свой YML, склады и категории. Откройте настройки,
              чтобы изменить параметры и сохранить.{" "}
              <Link to="/journal" className="font-medium text-accent hover:underline">
                Журнал сборок
              </Link>
            </p>
          </div>
          <Button
            type="button"
            variant="primary"
            className="w-full sm:w-auto"
            onClick={() => navigate("/feeds/new")}
          >
            Новая выгрузка
          </Button>
        </div>

        <Card title="Список" description="Ссылка на фид активна после пересборки.">
          {feeds.length === 0 ? (
            <p className="py-6 text-center text-sm text-slate-400">
              Пока нет выгрузок. Создайте первую.
            </p>
          ) : (
            <ul className="divide-y divide-slate-100">
              {feeds.map((f) => {
                const live = statusByFeed[f.id];
                const building =
                  live &&
                  (live.status === "pending" || live.status === "running");
                const starting = startingId === f.id;
                return (
                  <li
                    key={f.id}
                    className="flex flex-col gap-3 py-4 first:pt-0 sm:flex-row sm:items-start sm:justify-between"
                  >
                    <div className="min-w-0 flex-1">
                      <p className="font-semibold text-slate-900">{f.name}</p>
                      <p className="font-mono text-xs text-slate-500">/{f.slug}.yml</p>
                      <a
                        href={f.public_url_hint}
                        target="_blank"
                        rel="noreferrer"
                        className="mt-1 inline-block break-all text-xs font-medium text-accent hover:underline"
                      >
                        {f.public_url_hint}
                      </a>
                      <p className="mt-1 text-xs text-slate-400">
                        Сборка: {f.last_built_at ?? "—"}
                        {f.last_error ? (
                          <span className="ml-2 text-red-600">{f.last_error}</span>
                        ) : null}
                      </p>
                      {building && live ? (
                        <div className="mt-2 rounded-lg border border-accent/20 bg-accent-soft/40 px-3 py-2">
                          <p className="text-[11px] font-medium uppercase tracking-wide text-accent">
                            Сборка идёт
                          </p>
                          <RebuildProgressBlock jobStatus={live} compact />
                        </div>
                      ) : null}
                    </div>
                    <div className="flex shrink-0 flex-wrap gap-2 sm:flex-col sm:items-stretch">
                      <Button
                        type="button"
                        variant="primary"
                        disabled={!!building || starting}
                        onClick={() => startRebuild(f.id)}
                      >
                        {starting
                          ? "Запуск…"
                          : building
                            ? "Сборка…"
                            : "Собрать YML"}
                      </Button>
                      <Button
                        type="button"
                        variant="ghost"
                        onClick={() => navigate(`/feeds/${f.id}`)}
                      >
                        Настройки
                      </Button>
                      <Button type="button" variant="ghost" onClick={() => remove(f.id)}>
                        Удалить
                      </Button>
                    </div>
                  </li>
                );
              })}
            </ul>
          )}
        </Card>
      </div>
    </>
  );
}
