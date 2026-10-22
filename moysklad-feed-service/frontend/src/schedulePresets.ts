/**
 * Пресеты автозапуска сборки YML.
 * Все выражения — стандартный cron в UTC (как воркер и croniter на бэкенде).
 */

export type SchedulePreset = {
  id: string;
  label: string;
  /** Пустая строка = только ручной запуск */
  cron: string;
};

export const SCHEDULE_OFF = "off";
export const SCHEDULE_CUSTOM = "custom";

export const schedulePresets: SchedulePreset[] = [
  { id: "m5", label: "Каждые 5 минут", cron: "*/5 * * * *" },
  { id: "m10", label: "Каждые 10 минут", cron: "*/10 * * * *" },
  { id: "m15", label: "Каждые 15 минут", cron: "*/15 * * * *" },
  { id: "m20", label: "Каждые 20 минут", cron: "*/20 * * * *" },
  { id: "m30", label: "Каждые 30 минут", cron: "*/30 * * * *" },
  { id: "m45", label: "Каждые 45 минут", cron: "*/45 * * * *" },
  { id: "h1", label: "Каждый час", cron: "0 * * * *" },
  { id: "h2", label: "Каждые 2 часа", cron: "0 */2 * * *" },
  { id: "h3", label: "Каждые 3 часа", cron: "0 */3 * * *" },
  { id: "h6", label: "Каждые 6 часов", cron: "0 */6 * * *" },
  { id: "h12", label: "Каждые 12 часов", cron: "0 */12 * * *" },
  { id: "d1", label: "Раз в сутки (00:00 UTC)", cron: "0 0 * * *" },
  { id: "w1", label: "Раз в неделю (воскресенье 00:00 UTC)", cron: "0 0 * * 0" },
];

export function presetIdFromCron(cronStr: string | null | undefined): string {
  const c = (cronStr || "").trim();
  if (!c) return SCHEDULE_OFF;
  const hit = schedulePresets.find((p) => p.cron === c);
  return hit?.id ?? SCHEDULE_CUSTOM;
}

export function cronForPresetId(id: string): string {
  if (id === SCHEDULE_OFF) return "";
  const p = schedulePresets.find((x) => x.id === id);
  return p?.cron ?? "";
}
