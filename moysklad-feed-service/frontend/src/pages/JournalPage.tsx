import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router-dom";
import {
  api,
  fetchBuildLog,
  type Feed,
  type FeedBuildLogEntry,
} from "../api";
import { AlertBanner } from "../components/AlertBanner";
import { Button } from "../components/ui/Button";
import { Card } from "../components/ui/Card";

function formatDuration(ms: number | null): string {
  if (ms === null || ms < 0) return "—";
  if (ms >= 1000) return `${(ms / 1000).toFixed(1)} с`;
  return `${ms} мс`;
}

function formatWhen(iso: string): string {
  try {
    const d = new Date(iso);
    return d.toLocaleString("ru-RU", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
    });
  } catch {
    return iso;
  }
}

export function JournalPage() {
  const [rows, setRows] = useState<FeedBuildLogEntry[]>([]);
  const [feeds, setFeeds] = useState<Feed[]>([]);
  const [filterFeedId, setFilterFeedId] = useState<number | "">("");
  const [err, setErr] = useState<string | null>(null);

  const load = useCallback(async () => {
    const [log, flist] = await Promise.all([
      fetchBuildLog({
        limit: 200,
        feed_id: filterFeedId === "" ? undefined : filterFeedId,
      }),
      api<Feed[]>("/api/feeds"),
    ]);
    setRows(log);
    setFeeds(flist);
  }, [filterFeedId]);

  useEffect(() => {
    load().catch((e) => setErr(String(e)));
  }, [load]);

  const hasRunning = rows.some((r) => r.status === "running");
  useEffect(() => {
    if (!hasRunning) return;
    const id = setInterval(() => {
      load().catch((e) => setErr(String(e)));
    }, 3000);
    return () => clearInterval(id);
  }, [hasRunning, load]);

  return (
    <>
      <AlertBanner error={err} message={null} />
      <div className="mx-auto max-w-5xl space-y-8 px-4 py-8 sm:px-6">
        <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <h2 className="text-xl font-bold text-slate-900">Журнал</h2>
            <p className="mt-1 text-sm text-slate-500">
              Ручные и автоматические сборки YML: статус, число офферов и выгрузка.
            </p>
          </div>
          <div className="flex flex-wrap items-center gap-2">
            <label className="flex items-center gap-2 text-sm text-slate-600">
              <span className="whitespace-nowrap">Выгрузка</span>
              <select
                className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm"
                value={filterFeedId === "" ? "" : String(filterFeedId)}
                onChange={(e) => {
                  const v = e.target.value;
                  setFilterFeedId(v === "" ? "" : Number(v));
                }}
              >
                <option value="">Все</option>
                {feeds.map((f) => (
                  <option key={f.id} value={f.id}>
                    {f.name}
                  </option>
                ))}
              </select>
            </label>
            <Button type="button" variant="ghost" onClick={() => load().catch((e) => setErr(String(e)))}>
              Обновить
            </Button>
          </div>
        </div>

        <Card
          title="Запуски"
          description="Новые записи сверху. Авто — воркер по расписанию cron."
        >
          {rows.length === 0 ? (
            <p className="py-6 text-center text-sm text-slate-400">
              Пока нет записей. Запустите сборку вручную или дождитесь срабатывания расписания.
            </p>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full min-w-[720px] text-left text-sm">
                <thead>
                  <tr className="border-b border-slate-200 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <th className="pb-3 pr-3">Время</th>
                    <th className="pb-3 pr-3">Выгрузка</th>
                    <th className="pb-3 pr-3">Источник</th>
                    <th className="pb-3 pr-3">Статус</th>
                    <th className="pb-3 pr-3 text-right">Офферов</th>
                    <th className="pb-3 pr-3 text-right">Длит.</th>
                    <th className="pb-3">Ошибка</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {rows.map((r) => (
                    <tr key={r.id} className="align-top text-slate-800">
                      <td className="py-3 pr-3 whitespace-nowrap text-slate-600">
                        {formatWhen(r.created_at)}
                      </td>
                      <td className="py-3 pr-3">
                        <Link
                          to={`/feeds/${r.feed_id}`}
                          className="font-medium text-accent hover:underline"
                        >
                          {r.feed_name || `ID ${r.feed_id}`}
                        </Link>
                        <div className="font-mono text-xs text-slate-500">
                          /{r.feed_slug || "—"}.yml
                        </div>
                      </td>
                      <td className="py-3 pr-3">
                        {r.trigger === "scheduled" ? (
                          <span className="rounded-md bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-800">
                            Авто
                          </span>
                        ) : (
                          <span className="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                            Ручной
                          </span>
                        )}
                      </td>
                      <td className="py-3 pr-3">
                        {r.status === "running" ? (
                          <span className="inline-flex items-center gap-1.5 text-amber-800">
                            <span className="inline-block h-2 w-2 animate-pulse rounded-full bg-amber-500" />
                            Сборка
                          </span>
                        ) : r.status === "success" ? (
                          <span className="text-emerald-700">Успех</span>
                        ) : (
                          <span className="text-red-600">Ошибка</span>
                        )}
                      </td>
                      <td className="py-3 pr-3 text-right tabular-nums">
                        {r.status === "running" ? "—" : r.offers_count}
                      </td>
                      <td className="py-3 pr-3 text-right text-slate-600 tabular-nums">
                        {r.status === "running" ? "—" : formatDuration(r.duration_ms)}
                      </td>
                      <td
                        className={`py-3 max-w-[220px] wrap-break-word text-xs ${
                          r.status === "running"
                            ? "text-slate-400"
                            : "text-red-600"
                        }`}
                      >
                        {r.status === "running"
                          ? "Ожидайте завершения…"
                          : r.error_message ?? "—"}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </Card>
      </div>
    </>
  );
}
