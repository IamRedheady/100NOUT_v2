import { useCallback, useEffect, useMemo, useState } from "react";
import { Link, useMatch, useNavigate, useParams } from "react-router-dom";
import {
  api,
  type Feed,
  type PriceTypeRow,
  type ProductFolderRow,
  type RebuildJobStatus,
  type StoreRow,
} from "../api";
import { RebuildProgressBlock } from "../components/RebuildProgressBlock";
import { AlertBanner } from "../components/AlertBanner";
import { Button } from "../components/ui/Button";
import { Card } from "../components/ui/Card";
import {
  clearRebuildJobForFeed,
  readRebuildJobsMap,
  setRebuildJobForFeed,
} from "../rebuildJobStorage";
import { Field } from "../components/ui/Field";
import {
  buildChildrenMap,
  folderSearchMatch,
  toggleFolderWithSubtree,
} from "../folderUtils";
import {
  SCHEDULE_CUSTOM,
  SCHEDULE_OFF,
  cronForPresetId,
  presetIdFromCron,
  schedulePresets,
} from "../schedulePresets";

export function FeedFormPage() {
  const isNew = !!useMatch({ path: "/feeds/new", end: true });
  const { id: idParam } = useParams<{ id: string }>();
  const navigate = useNavigate();

  const [err, setErr] = useState<string | null>(null);
  const [msg, setMsg] = useState<string | null>(null);
  const [loadingFeed, setLoadingFeed] = useState(!isNew);

  const [stores, setStores] = useState<StoreRow[]>([]);
  const [folders, setFolders] = useState<ProductFolderRow[]>([]);
  const [priceTypes, setPriceTypes] = useState<PriceTypeRow[]>([]);
  const [loadingStores, setLoadingStores] = useState(true);
  const [loadingFolders, setLoadingFolders] = useState(true);
  const [loadingPriceTypes, setLoadingPriceTypes] = useState(true);

  const [name, setName] = useState(isNew ? "Новая выгрузка" : "");
  const [slug, setSlug] = useState("");
  const [batch, setBatch] = useState(100);
  const [cron, setCron] = useState("");
  const [schedulePresetId, setSchedulePresetId] = useState<string>(SCHEDULE_OFF);
  const [priceTypeId, setPriceTypeId] = useState<string>("");
  const [oldPriceTypeId, setOldPriceTypeId] = useState<string>("");
  const [storesSel, setStoresSel] = useState<Record<string, boolean>>({});
  const [foldersSel, setFoldersSel] = useState<Record<string, boolean>>({});

  const [folderQuery, setFolderQuery] = useState("");
  const [includeSubtree, setIncludeSubtree] = useState(true);

  const [jobId, setJobId] = useState<string | null>(null);
  const [jobStatus, setJobStatus] = useState<RebuildJobStatus | null>(null);
  const [saving, setSaving] = useState(false);
  const [starting, setStarting] = useState(false);

  const isBuilding =
    starting ||
    (jobStatus?.status === "pending" || jobStatus?.status === "running");

  const feedId = !isNew && idParam ? Number(idParam) : NaN;

  const childrenMap = useMemo(() => buildChildrenMap(folders), [folders]);
  const filteredFolders = useMemo(
    () => folders.filter((f) => folderSearchMatch(f, folderQuery)),
    [folders, folderQuery],
  );

  const [storesErr, setStoresErr] = useState<string | null>(null);
  const [foldersErr, setFoldersErr] = useState<string | null>(null);
  const [priceTypesErr, setPriceTypesErr] = useState<string | null>(null);

  // Справочники грузим независимо: один не блокирует другой и форму
  useEffect(() => {
    setLoadingStores(true);
    api<StoreRow[]>("/api/stores")
      .then((s) => {
        setStores(s);
        setStoresErr(null);
      })
      .catch((e) => setStoresErr(String(e)))
      .finally(() => setLoadingStores(false));
  }, []);

  useEffect(() => {
    setLoadingFolders(true);
    api<ProductFolderRow[]>("/api/product-folders")
      .then((f) => {
        setFolders(f);
        setFoldersErr(null);
      })
      .catch((e) => setFoldersErr(String(e)))
      .finally(() => setLoadingFolders(false));
  }, []);

  useEffect(() => {
    setLoadingPriceTypes(true);
    api<PriceTypeRow[]>("/api/price-types")
      .then((pt) => {
        setPriceTypes(pt);
        setPriceTypesErr(null);
      })
      .catch((e) => setPriceTypesErr(String(e)))
      .finally(() => setLoadingPriceTypes(false));
  }, []);

  useEffect(() => {
    if (isNew) {
      setLoadingFeed(false);
      return;
    }
    if (Number.isNaN(feedId) || feedId < 1) {
      setErr("Некорректный ID");
      setLoadingFeed(false);
      return;
    }
    let cancelled = false;
    (async () => {
      try {
        const f = await api<Feed>(`/api/feeds/${feedId}`);
        if (cancelled) return;
        setName(f.name);
        setSlug(f.slug);
        setBatch(f.batch_limit);
        setCron(f.schedule_cron || "");
        setSchedulePresetId(presetIdFromCron(f.schedule_cron));
        const ss: Record<string, boolean> = {};
        for (const u of f.store_uuids) ss[u] = true;
        setStoresSel(ss);
        const fs: Record<string, boolean> = {};
        for (const u of f.productfolder_uuids) fs[u] = true;
        setFoldersSel(fs);
        setPriceTypeId(f.price_type_id ?? "");
        setOldPriceTypeId(f.old_price_type_id ?? "");
      } catch (e) {
        if (!cancelled) setErr(String(e));
      } finally {
        if (!cancelled) setLoadingFeed(false);
      }
    })();
    return () => {
      cancelled = true;
    };
  }, [isNew, feedId]);

  useEffect(() => {
    if (isNew || !feedId || Number.isNaN(feedId)) return;
    const jid = readRebuildJobsMap()[feedId];
    if (jid) setJobId(jid);
  }, [isNew, feedId]);

  useEffect(() => {
    if (!jobId) return;
    let cancelled = false;
    let timeoutId: ReturnType<typeof setTimeout>;

    const poll = async () => {
      if (cancelled) return;
      try {
        const s = await api<RebuildJobStatus>(`/api/feeds/rebuild-jobs/${jobId}`);
        if (cancelled) return;
        setJobStatus(s);
        if (s.status === "pending" || s.status === "running") {
          timeoutId = setTimeout(poll, 450);
        } else {
          if (!isNew && !Number.isNaN(feedId)) clearRebuildJobForFeed(feedId);
          if (s.status === "done") {
            setMsg("YML успешно собран");
            setErr(null);
          }
          if (s.status === "error") {
            setErr(s.error || "Ошибка сборки");
          }
        }
      } catch (e) {
        if (!cancelled) {
          const t = String(e);
          if (/\b404\b|не найден/i.test(t) && !isNew && !Number.isNaN(feedId)) {
            clearRebuildJobForFeed(feedId);
            setJobId(null);
            setJobStatus(null);
            setErr(
              "Задача сборки недоступна (например, после перезапуска API). Запустите сборку снова.",
            );
          } else {
            setErr(t);
          }
        }
      }
    };

    poll();
    return () => {
      cancelled = true;
      clearTimeout(timeoutId);
    };
  }, [jobId, isNew, feedId]);

  function toggleStore(id: string, checked: boolean) {
    setStoresSel((prev) => ({ ...prev, [id]: checked }));
  }

  function toggleFolder(id: string, checked: boolean) {
    setFoldersSel((prev) =>
      toggleFolderWithSubtree(prev, id, checked, childrenMap, includeSubtree),
    );
  }

  function selectedKeys(m: Record<string, boolean>): string[] {
    return Object.entries(m)
      .filter(([, v]) => v)
      .map(([k]) => k);
  }

  async function handleSave(e: React.FormEvent) {
    e.preventDefault();
    setErr(null);
    setMsg(null);
    if (saving) return;
    const schedule_cron =
      schedulePresetId === SCHEDULE_OFF
        ? null
        : schedulePresetId === SCHEDULE_CUSTOM
          ? cron.trim() || null
          : cronForPresetId(schedulePresetId) || null;

    const body = {
      name,
      slug,
      store_uuids: selectedKeys(storesSel),
      productfolder_uuids: selectedKeys(foldersSel),
      batch_limit: batch,
      schedule_cron,
      price_type_id: priceTypeId || null,
      old_price_type_id: oldPriceTypeId || null,
    };
    setSaving(true);
    try {
      if (isNew) {
        if (!slug.trim()) {
          setErr("Укажите slug");
          return;
        }
        const created = await api<Feed>("/api/feeds", {
          method: "POST",
          body: JSON.stringify(body),
        });
        setMsg("Выгрузка создана");
        navigate(`/feeds/${created.id}`, { replace: true });
        return;
      }
      await api(`/api/feeds/${feedId}`, {
        method: "PATCH",
        body: JSON.stringify(body),
      });
      setMsg("Настройки сохранены");
    } catch (e) {
      setErr(String(e));
    } finally {
      setSaving(false);
    }
  }

  async function startRebuildLive() {
    if (isNew || isBuilding) return;
    setErr(null);
    setMsg(null);
    setJobStatus(null);
    setStarting(true);
    try {
      const r = await api<{ job_id: string }>(
        `/api/feeds/${feedId}/rebuild/async`,
        { method: "POST" },
      );
      setJobId(r.job_id);
      setRebuildJobForFeed(feedId, r.job_id);
    } catch (e) {
      setErr(String(e));
    } finally {
      setStarting(false);
    }
  }

  if (!isNew && (Number.isNaN(feedId) || feedId < 1)) {
    return (
      <div className="px-4 py-8 text-center text-red-600">
        Некорректная выгрузка
      </div>
    );
  }

  if (loadingFeed) {
    return (
      <div className="px-4 py-16 text-center text-slate-500">Загрузка…</div>
    );
  }

  // Хелперы для отображения «выбрано N» когда справочник ещё не подгружен
  const selStoresCount = Object.values(storesSel).filter(Boolean).length;
  const selFoldersCount = Object.values(foldersSel).filter(Boolean).length;
  const priceTypeName = (id: string): string =>
    priceTypes.find((p) => p.id === id)?.name ?? `${id.slice(0, 8)}…`;

  const title = isNew ? "Новая выгрузка" : "Настройки выгрузки";

  return (
    <>
      <AlertBanner error={err} message={msg} />
      <div className="mx-auto max-w-5xl space-y-6 px-4 py-8 sm:px-6">
        <div className="flex flex-wrap items-center gap-3">
          <Link
            to="/feeds"
            className="text-sm font-medium text-slate-500 hover:text-accent"
          >
            ← К списку
          </Link>
        </div>

        <div>
          <h2 className="text-xl font-bold text-slate-900">{title}</h2>
          <p className="mt-1 text-sm text-slate-500">
            Склады и категории (папки). Пустой выбор — «все». При включённой
            опции ниже отметка родительской категории добавляет все подпапки.
          </p>
        </div>

        <form onSubmit={handleSave} className="space-y-6">
          <Card title="Основное">
            <div className="grid gap-4 sm:grid-cols-2">
              <Field label="Название" value={name} onChange={(e) => setName(e.target.value)} />
              <Field
                label="Slug (в URL)"
                value={slug}
                onChange={(e) => setSlug(e.target.value)}
                className="font-mono text-xs"
                hint="Только a-z, 0-9, дефис"
              />
              <Field
                label="Шаг загрузки (limit)"
                type="number"
                min={5}
                max={1000}
                value={batch}
                onChange={(e) => setBatch(Number(e.target.value))}
              />
              <div className="sm:col-span-2 space-y-2">
                <label className="mb-1 block text-sm font-medium text-slate-700">
                  Автозапуск сборки YML
                </label>
                <select
                  value={schedulePresetId}
                  onChange={(e) => {
                    const v = e.target.value;
                    setSchedulePresetId(v);
                    if (v === SCHEDULE_OFF) setCron("");
                    else if (v !== SCHEDULE_CUSTOM) setCron(cronForPresetId(v));
                  }}
                  className="w-full max-w-xl rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20"
                >
                  <option value={SCHEDULE_OFF}>Только вручную</option>
                  {schedulePresets.map((p) => (
                    <option key={p.id} value={p.id}>
                      {p.label}
                    </option>
                  ))}
                  <option value={SCHEDULE_CUSTOM}>
                    Свой cron (UTC)…
                  </option>
                </select>
                {schedulePresetId === SCHEDULE_CUSTOM ? (
                  <Field
                    label="Cron (5 полей, UTC)"
                    value={cron}
                    onChange={(e) => setCron(e.target.value)}
                    className="font-mono text-xs max-w-xl"
                    placeholder="*/30 * * * *"
                    hint="Минута час день месяц день_недели"
                  />
                ) : schedulePresetId !== SCHEDULE_OFF ? (
                  <p className="font-mono text-xs text-slate-400">
                    {cronForPresetId(schedulePresetId)}
                  </p>
                ) : null}
                <p className="text-xs text-slate-500">
                  Воркер проверяет расписание каждую минуту. Время — в UTC.
                </p>
              </div>
            </div>
          </Card>

          <Card
            title="Цены"
            description="Какой тип цены МойСклад попадает в каждое поле YML. Не выбрано — берётся первая ненулевая цена."
          >
            <div className="grid gap-4 sm:grid-cols-2">
              <div>
                <label className="mb-1.5 block text-sm font-medium text-slate-700">
                  Основная цена{" "}
                  <code className="rounded bg-slate-100 px-1 text-xs text-slate-500">
                    &lt;price&gt;
                  </code>
                </label>
                <select
                  value={priceTypeId}
                  onChange={(e) => setPriceTypeId(e.target.value)}
                  disabled={loadingPriceTypes && priceTypes.length === 0}
                  className="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20 disabled:opacity-60"
                >
                  <option value="">— первая ненулевая —</option>
                  {priceTypeId &&
                    !priceTypes.find((p) => p.id === priceTypeId) && (
                      <option value={priceTypeId}>
                        Сохранено: {priceTypeName(priceTypeId)}
                      </option>
                    )}
                  {priceTypes.map((pt) => (
                    <option key={pt.id} value={pt.id}>
                      {pt.name}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label className="mb-1.5 block text-sm font-medium text-slate-700">
                  Зачёркнутая цена{" "}
                  <code className="rounded bg-slate-100 px-1 text-xs text-slate-500">
                    &lt;oldprice&gt;
                  </code>
                </label>
                <select
                  value={oldPriceTypeId}
                  onChange={(e) => setOldPriceTypeId(e.target.value)}
                  disabled={loadingPriceTypes && priceTypes.length === 0}
                  className="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20 disabled:opacity-60"
                >
                  <option value="">— не выводить —</option>
                  {oldPriceTypeId &&
                    !priceTypes.find((p) => p.id === oldPriceTypeId) && (
                      <option value={oldPriceTypeId}>
                        Сохранено: {priceTypeName(oldPriceTypeId)}
                      </option>
                    )}
                  {priceTypes.map((pt) => (
                    <option key={pt.id} value={pt.id}>
                      {pt.name}
                    </option>
                  ))}
                </select>
                <p className="mt-1.5 text-xs text-slate-400">
                  Выводится только если выше основной цены
                </p>
              </div>
            </div>
            {loadingPriceTypes && (
              <p className="mt-3 inline-flex items-center gap-2 text-xs text-slate-400">
                <span className="inline-block h-3 w-3 animate-spin rounded-full border-2 border-accent border-t-transparent" />
                Загружаем типы цен из МойСклад…
              </p>
            )}
            {priceTypesErr && (
              <p className="mt-3 text-sm text-red-600">
                Ошибка загрузки: {priceTypesErr}
              </p>
            )}
          </Card>

          <Card
            title="Склады"
            description="Остатки суммируются по выбранным складам; в YML попадают только товары с остатком больше нуля. Не отмечено — учитываются все склады."
          >
            {loadingStores && stores.length === 0 ? (
              <div className="space-y-3">
                {selStoresCount > 0 && (
                  <p className="text-xs text-slate-500">
                    Сохранено выбрано: <strong>{selStoresCount}</strong> · загружаем список…
                  </p>
                )}
                <ul className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                  {[0, 1, 2, 3, 4, 5].map((i) => (
                    <li
                      key={i}
                      className="h-10 animate-pulse rounded-xl bg-slate-100"
                    />
                  ))}
                </ul>
              </div>
            ) : storesErr ? (
              <p className="text-sm text-red-600">
                Ошибка загрузки складов: {storesErr}
              </p>
            ) : stores.length === 0 ? (
              <p className="text-sm text-slate-400">
                Нет данных. Сохраните учётные данные в «Настройках» и обновите страницу.
              </p>
            ) : (
              <ul className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                {stores.map((s) => (
                  <li key={s.id}>
                    <label className="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-3 py-2.5 hover:border-accent/25">
                      <input
                        type="checkbox"
                        checked={!!storesSel[s.id]}
                        onChange={(e) => toggleStore(s.id, e.target.checked)}
                        className="mt-0.5 rounded border-slate-300 text-accent focus:ring-accent/30"
                      />
                      <span className="text-sm text-slate-800">
                        {s.name}
                        {s.archived ? (
                          <span className="text-slate-400"> (архив)</span>
                        ) : null}
                      </span>
                    </label>
                  </li>
                ))}
              </ul>
            )}
          </Card>

          <Card
            title="Категории"
            description="Папки товаров в МойСклад. Не отмечено — все товары."
          >
            {loadingFolders && folders.length === 0 ? (
              <div className="space-y-3">
                {selFoldersCount > 0 && (
                  <p className="text-xs text-slate-500">
                    Сохранено выбрано: <strong>{selFoldersCount}</strong> · загружаем дерево…
                  </p>
                )}
                <ul className="space-y-1">
                  {[0, 1, 2, 3, 4, 5, 6, 7].map((i) => (
                    <li
                      key={i}
                      className="h-7 animate-pulse rounded-lg bg-slate-100"
                      style={{ width: `${60 + ((i * 7) % 35)}%` }}
                    />
                  ))}
                </ul>
              </div>
            ) : foldersErr ? (
              <p className="text-sm text-red-600">
                Ошибка загрузки категорий: {foldersErr}
              </p>
            ) : folders.length === 0 ? (
              <p className="text-sm text-slate-400">
                Папки не загрузились. Проверьте доступ API в «Настройках».
              </p>
            ) : (
              <div className="space-y-4">
                <Field
                  label="Поиск по названию или пути"
                  value={folderQuery}
                  onChange={(e) => setFolderQuery(e.target.value)}
                  placeholder="Начните вводить…"
                  wrapperClassName="max-w-md"
                />
                <label className="flex cursor-pointer items-center gap-3 rounded-xl border border-accent/20 bg-accent-soft/50 px-4 py-3">
                  <input
                    type="checkbox"
                    checked={includeSubtree}
                    onChange={(e) => setIncludeSubtree(e.target.checked)}
                    className="rounded border-slate-300 text-accent focus:ring-accent/30"
                  />
                  <span className="text-sm font-medium text-slate-800">
                    При выборе категории включать все подпапки
                  </span>
                </label>
                <p className="text-xs text-slate-500">
                  Снимите галочку, чтобы отмечать только отдельные папки без
                  потомков.
                </p>
                <ul className="max-h-96 space-y-1 overflow-y-auto rounded-xl border border-slate-100 p-2">
                  {filteredFolders.length === 0 ? (
                    <li className="px-2 py-4 text-center text-sm text-slate-400">
                      Ничего не найдено
                    </li>
                  ) : (
                    filteredFolders.map((f) => (
                      <li key={f.id}>
                        <label className="flex cursor-pointer items-start gap-3 rounded-lg px-2 py-1.5 hover:bg-slate-50">
                          <input
                            type="checkbox"
                            checked={!!foldersSel[f.id]}
                            onChange={(e) =>
                              toggleFolder(f.id, e.target.checked)
                            }
                            className="mt-0.5 rounded border-slate-300 text-accent focus:ring-accent/30"
                          />
                          <span className="text-sm text-slate-800">
                            {f.path_name ? (
                              <span className="text-slate-500">
                                {f.path_name}
                                {" / "}
                              </span>
                            ) : null}
                            {f.name}
                            {f.archived ? (
                              <span className="text-slate-400"> (архив)</span>
                            ) : null}
                          </span>
                        </label>
                      </li>
                    ))
                  )}
                </ul>
              </div>
            )}
          </Card>

          <div className="flex flex-wrap items-center gap-3">
            <Button type="submit" variant="primary" disabled={saving}>
              {saving ? "Сохранение…" : "Сохранить"}
            </Button>
            {saving ? (
              <span className="text-xs text-slate-400">Применяем изменения…</span>
            ) : null}
          </div>
        </form>

        {!isNew ? (
          <Card
            title="Сборка YML"
            description="Запуск в фоне: прогресс по остаткам и папкам. По готовности появится ссылка."
          >
            <div className="space-y-4">
              <div className="flex flex-wrap items-center gap-3">
                <Button
                  type="button"
                  variant="primary"
                  onClick={startRebuildLive}
                  disabled={isBuilding}
                >
                  {starting
                    ? "Запуск…"
                    : isBuilding
                      ? "Сборка…"
                      : "Запустить сборку"}
                </Button>
                {isBuilding ? (
                  <span className="inline-flex items-center gap-2 text-xs text-slate-500">
                    <span className="inline-block h-2 w-2 animate-pulse rounded-full bg-accent" />
                    Идёт обработка
                  </span>
                ) : null}
              </div>

              {jobStatus &&
              (jobStatus.status === "pending" ||
                jobStatus.status === "running") ? (
                <div className="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                  <RebuildProgressBlock jobStatus={jobStatus} />
                </div>
              ) : null}

              {jobStatus?.status === "done" && jobStatus.public_url ? (
                <div className="rounded-xl border border-accent/30 bg-accent-soft/60 px-4 py-3">
                  <p className="text-sm font-medium text-slate-800">
                    Фид готов
                  </p>
                  <a
                    href={jobStatus.public_url}
                    target="_blank"
                    rel="noreferrer"
                    className="mt-1 inline-block break-all font-mono text-sm font-semibold text-accent hover:underline"
                  >
                    {jobStatus.public_url}
                  </a>
                  {jobStatus.offers > 0 ? (
                    <p className="mt-2 text-xs text-slate-500">
                      Офферов: {jobStatus.offers}
                    </p>
                  ) : null}
                </div>
              ) : null}

              {jobStatus?.status === "error" ? (
                <p className="text-sm text-red-700">{jobStatus.error}</p>
              ) : null}
            </div>
          </Card>
        ) : null}
      </div>
    </>
  );
}
