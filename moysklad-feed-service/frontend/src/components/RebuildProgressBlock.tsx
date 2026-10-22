import type { RebuildJobStatus } from "../api";
import { rebuildPhaseLabel, rebuildProgressPercent } from "../rebuildUi";

type Props = {
  jobStatus: RebuildJobStatus;
  /** компактный вид для карточки в списке фидов */
  compact?: boolean;
};

export function RebuildProgressBlock({ jobStatus, compact }: Props) {
  const progressPct = rebuildProgressPercent(jobStatus);
  const phaseLabel = rebuildPhaseLabel(jobStatus);
  const barH = compact ? "h-1.5" : "h-2";
  const textSm = compact ? "text-xs" : "text-sm";
  const monoSm = compact ? "text-[10px]" : "text-[11px]";

  return (
    <div className={compact ? "mt-2 space-y-1.5" : "space-y-2"}>
      <div className={`${barH} overflow-hidden rounded-full bg-slate-200`}>
        <div
          className={`${barH} rounded-full bg-accent transition-all duration-300 ${
            progressPct != null ? "" : "animate-pulse"
          }`}
          style={{
            width:
              progressPct != null
                ? `${progressPct}%`
                : jobStatus.current > 0
                  ? "55%"
                  : "20%",
          }}
        />
      </div>
      <p className={`${textSm} text-slate-700`}>
        <span className="font-semibold text-accent">{phaseLabel}</span>
        {jobStatus.message ? <> · {jobStatus.message}</> : null}
      </p>
      <p className={`font-mono ${monoSm} text-slate-500`}>
        Загружено товаров:{" "}
        {jobStatus.total != null && jobStatus.total > 0
          ? `${jobStatus.current} из ${jobStatus.total}`
          : `${jobStatus.current}`}
        {" · в YML: "}
        {jobStatus.offers}
      </p>
    </div>
  );
}
