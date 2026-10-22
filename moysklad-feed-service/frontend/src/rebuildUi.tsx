import type { RebuildJobStatus } from "./api";

export function rebuildProgressPercent(s: RebuildJobStatus): number | null {
  if (s.total != null && s.total > 0) {
    return Math.min(100, Math.round((s.current / s.total) * 100));
  }
  return null;
}

export function rebuildPhaseLabel(s: RebuildJobStatus): string {
  if (s.status === "pending") return "В очереди";
  if (s.status === "error") return "Ошибка";
  if (s.status === "done") return "Готово";
  switch (s.phase) {
    case "prepare":
      return "Подготовка";
    case "loading":
      return "Получаем товары";
    case "done":
      return "Готово";
    case "error":
      return "Ошибка";
    default:
      return "Сборка";
  }
}
