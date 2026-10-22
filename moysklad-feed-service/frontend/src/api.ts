const BASE = (import.meta.env.VITE_API_BASE as string | undefined) || "";

async function parseErr(r: Response): Promise<string> {
  const t = await r.text();
  try {
    const j = JSON.parse(t);
    return j.detail ?? t;
  } catch {
    return t || r.statusText;
  }
}

export async function api<T>(path: string, init?: RequestInit): Promise<T> {
  const r = await fetch(`${BASE}${path}`, {
    ...init,
    headers: {
      "Content-Type": "application/json",
      ...(init?.headers as object),
    },
  });
  if (!r.ok) throw new Error(await parseErr(r));
  if (r.status === 204) return undefined as T;
  return r.json() as Promise<T>;
}

export type Credentials = {
  login: string;
  has_password: boolean;
  updated_at: string | null;
};

export type StoreRow = { id: string; name: string; archived: boolean };

export type PriceTypeRow = { id: string; name: string; external_code: string };

export type ProductFolderRow = {
  id: string;
  name: string;
  path_name: string;
  parent_id: string | null;
  archived: boolean;
};

export type Feed = {
  id: number;
  name: string;
  slug: string;
  store_uuids: string[];
  productfolder_uuids: string[];
  batch_limit: number;
  schedule_cron: string | null;
  price_type_id: string | null;
  old_price_type_id: string | null;
  last_built_at: string | null;
  last_error: string | null;
  public_url_hint: string;
};

export type RebuildJobStatus = {
  status: string;
  phase: string;
  current: number;
  total: number | null;
  message: string;
  offers: number;
  public_url: string | null;
  error: string | null;
  feed_id: number | null;
};

export type RebuildAsyncStart = { job_id: string };

export type FeedBuildLogEntry = {
  id: number;
  created_at: string;
  feed_id: number;
  feed_name: string;
  feed_slug: string;
  trigger: string;
  /** running | success | error */
  status: string;
  offers_count: number;
  error_message: string | null;
  duration_ms: number | null;
  job_id: string | null;
};

export function fetchBuildLog(params?: {
  limit?: number;
  offset?: number;
  feed_id?: number;
}): Promise<FeedBuildLogEntry[]> {
  const sp = new URLSearchParams();
  if (params?.limit !== undefined) sp.set("limit", String(params.limit));
  if (params?.offset !== undefined) sp.set("offset", String(params.offset));
  if (params?.feed_id !== undefined) sp.set("feed_id", String(params.feed_id));
  const qs = sp.toString();
  return api<FeedBuildLogEntry[]>(`/api/feeds/build-log${qs ? `?${qs}` : ""}`);
}

// ── Товары / изображения ────────────────────────────────────────────────────

export type YmlPatchResult = {
  patched_feeds: number[];
  removed_from_feeds: number[];
  missing_in_feeds: number[];
};

export type ProductRow = {
  product_uuid: string;
  product_name: string;
  product_code: string;
  product_article: string;
  folder_uuid: string;
  images: string[];       // имена файлов
  image_urls: string[];   // полные URL
  yml_patch?: YmlPatchResult;
};

export type ProductStats = {
  with_images: number;
  catalog_products_total: number | null;
  without_images: number | null;
};

export function fetchProductStats(): Promise<ProductStats> {
  return api<ProductStats>("/api/products/stats");
}

/** Поиск/список товаров (локальная БД) */
export async function fetchProducts(params: {
  q?: string;
  folder_uuid?: string;
  has_images?: boolean | null;
  limit?: number;
  offset?: number;
}): Promise<ProductRow[]> {
  const sp = new URLSearchParams();
  if (params.q !== undefined && params.q !== "") sp.set("q", params.q);
  if (params.folder_uuid) sp.set("folder_uuid", params.folder_uuid);
  if (params.has_images !== undefined && params.has_images !== null)
    sp.set("has_images", String(params.has_images));
  if (params.limit !== undefined) sp.set("limit", String(params.limit));
  if (params.offset !== undefined) sp.set("offset", String(params.offset));
  const qs = sp.toString();
  return api<ProductRow[]>(`/api/products${qs ? `?${qs}` : ""}`);
}

/** Загрузить изображения для товара */
export async function uploadProductImages(
  productUuid: string,
  files: File[],
  meta: { product_name?: string; product_code?: string; product_article?: string; folder_uuid?: string },
): Promise<ProductRow> {
  const BASE = (import.meta.env.VITE_API_BASE as string | undefined) || "";
  const sp = new URLSearchParams();
  if (meta.product_name) sp.set("product_name", meta.product_name);
  if (meta.product_code) sp.set("product_code", meta.product_code);
  if (meta.product_article) sp.set("product_article", meta.product_article);
  if (meta.folder_uuid) sp.set("folder_uuid", meta.folder_uuid);

  const fd = new FormData();
  files.forEach((f) => fd.append("files", f));

  const r = await fetch(
    `${BASE}/api/products/${productUuid}/images?${sp.toString()}`,
    { method: "POST", body: fd },
  );
  if (!r.ok) {
    const t = await r.text();
    let msg = t;
    try { msg = JSON.parse(t).detail ?? t; } catch { /* ignore */ }
    throw new Error(msg);
  }
  return r.json() as Promise<ProductRow>;
}

/** Удалить изображение */
export async function deleteProductImage(
  productUuid: string,
  filename: string,
): Promise<ProductRow> {
  return api<ProductRow>(`/api/products/${productUuid}/images/${filename}`, {
    method: "DELETE",
  });
}

/** Изменить порядок изображений */
export async function reorderProductImages(
  productUuid: string,
  images: string[],
): Promise<ProductRow> {
  return api<ProductRow>(`/api/products/${productUuid}/images`, {
    method: "PUT",
    body: JSON.stringify({ images }),
  });
}
