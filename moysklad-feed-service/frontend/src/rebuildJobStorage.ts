const STORAGE_KEY = "ms-yml-active-rebuild-jobs";

export type RebuildJobsMap = Record<number, string>;

function parseMap(raw: string): RebuildJobsMap {
  const o = JSON.parse(raw) as Record<string, string>;
  const out: RebuildJobsMap = {};
  for (const [k, v] of Object.entries(o)) {
    const id = Number(k);
    if (Number.isFinite(id) && typeof v === "string" && v.length > 0) {
      out[id] = v;
    }
  }
  return out;
}

export function readRebuildJobsMap(): RebuildJobsMap {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return {};
    return parseMap(raw);
  } catch {
    return {};
  }
}

function writeRebuildJobsMap(m: RebuildJobsMap): void {
  const o: Record<string, string> = {};
  for (const [k, v] of Object.entries(m)) {
    o[String(k)] = v;
  }
  localStorage.setItem(STORAGE_KEY, JSON.stringify(o));
}

export function setRebuildJobForFeed(feedId: number, jobId: string): void {
  const m = readRebuildJobsMap();
  m[feedId] = jobId;
  writeRebuildJobsMap(m);
}

export function clearRebuildJobForFeed(feedId: number): void {
  const m = readRebuildJobsMap();
  delete m[feedId];
  writeRebuildJobsMap(m);
}
