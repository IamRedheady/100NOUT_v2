import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import {
  api,
  deleteProductImage,
  fetchProductStats,
  fetchProducts,
  reorderProductImages,
  uploadProductImages,
} from "../api";
import type { ProductFolderRow, ProductRow, ProductStats } from "../api";

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

function useDebounce<T>(value: T, ms = 400): T {
  const [dv, setDv] = useState(value);
  useEffect(() => {
    const t = setTimeout(() => setDv(value), ms);
    return () => clearTimeout(t);
  }, [value, ms]);
  return dv;
}

function ImageThumb({
  url,
  onRemove,
  isMain,
}: {
  url: string;
  onRemove?: () => void;
  isMain?: boolean;
}) {
  return (
    <div className="relative group">
      <img
        src={url}
        alt=""
        className="h-20 w-20 rounded-xl object-cover border border-slate-200"
      />
      {isMain && (
        <span className="absolute top-1 left-1 rounded bg-accent px-1 py-0.5 text-[10px] font-semibold text-white leading-none">
          гл.
        </span>
      )}
      {onRemove && (
        <button
          onClick={onRemove}
          className="absolute top-1 right-1 hidden group-hover:flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-white text-xs hover:bg-red-700"
          title="Удалить"
        >
          ×
        </button>
      )}
    </div>
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Product list item
// ─────────────────────────────────────────────────────────────────────────────

function ProductListItem({
  product,
  isSelected,
  onClick,
}: {
  product: ProductRow;
  isSelected: boolean;
  onClick: () => void;
}) {
  const hasImages = product.images.length > 0;
  return (
    <button
      onClick={onClick}
      className={`w-full text-left flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors ${
        isSelected
          ? "bg-accent/10 ring-1 ring-accent/30"
          : "hover:bg-slate-50"
      }`}
    >
      {/* thumbnail or placeholder */}
      {product.image_urls[0] ? (
        <img
          src={product.image_urls[0]}
          alt=""
          className="h-12 w-12 shrink-0 rounded-lg object-cover border border-slate-200"
        />
      ) : (
        <div className="h-12 w-12 shrink-0 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300 text-2xl border border-slate-200">
          ☐
        </div>
      )}
      <div className="min-w-0 flex-1">
        <p className="truncate text-sm font-medium text-slate-800">
          {product.product_name || "(без названия)"}
        </p>
        <p className="text-xs text-slate-400">
          {[product.product_code && `Код: ${product.product_code}`, product.product_article && `Арт: ${product.product_article}`]
            .filter(Boolean)
            .join(" · ") || "—"}
        </p>
      </div>
      {hasImages && (
        <span className="shrink-0 rounded-full bg-accent/10 px-2 py-0.5 text-xs font-medium text-accent">
          {product.images.length} фото
        </span>
      )}
    </button>
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Image management panel
// ─────────────────────────────────────────────────────────────────────────────

function ImagePanel({
  product,
  onUpdate,
  onClose,
}: {
  product: ProductRow;
  onUpdate: (updated: ProductRow) => void;
  onClose: () => void;
}) {
  const [localProduct, setLocalProduct] = useState<ProductRow>(product);
  const [uploading, setUploading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [patchInfo, setPatchInfo] = useState<NonNullable<ProductRow["yml_patch"]> | null>(null);
  const fileRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    setLocalProduct(product);
    setPatchInfo(null);
  }, [product]);

  const consumePatch = (updated: ProductRow) => {
    if (updated.yml_patch) setPatchInfo(updated.yml_patch);
  };

  const handleUpload = async (files: FileList | null) => {
    if (!files || files.length === 0) return;
    setUploading(true);
    setError(null);
    try {
      const updated = await uploadProductImages(
        localProduct.product_uuid,
        Array.from(files),
        {
          product_name: localProduct.product_name,
          product_code: localProduct.product_code,
          product_article: localProduct.product_article,
          folder_uuid: localProduct.folder_uuid,
        },
      );
      setLocalProduct(updated);
      onUpdate(updated);
      consumePatch(updated);
    } catch (e) {
      setError(String(e));
    } finally {
      setUploading(false);
      if (fileRef.current) fileRef.current.value = "";
    }
  };

  const handleDelete = async (filename: string) => {
    setError(null);
    try {
      const updated = await deleteProductImage(localProduct.product_uuid, filename);
      setLocalProduct(updated);
      onUpdate(updated);
      consumePatch(updated);
    } catch (e) {
      setError(String(e));
    }
  };

  const handleMoveToFirst = async (filename: string) => {
    const newOrder = [
      filename,
      ...localProduct.images.filter((fn) => fn !== filename),
    ];
    setSaving(true);
    setError(null);
    try {
      const updated = await reorderProductImages(localProduct.product_uuid, newOrder);
      setLocalProduct(updated);
      onUpdate(updated);
      consumePatch(updated);
    } catch (e) {
      setError(String(e));
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="flex flex-col gap-4">
      {/* Header */}
      <div className="flex items-start justify-between gap-2">
        <div>
          <h3 className="font-semibold text-slate-900">
            {localProduct.product_name || "(без названия)"}
          </h3>
          <p className="text-xs text-slate-400 mt-0.5">
            {[
              localProduct.product_code && `Код: ${localProduct.product_code}`,
              localProduct.product_article && `Арт: ${localProduct.product_article}`,
            ]
              .filter(Boolean)
              .join(" · ") || localProduct.product_uuid}
          </p>
        </div>
        <button
          onClick={onClose}
          className="shrink-0 text-slate-400 hover:text-slate-600 text-xl leading-none mt-0.5"
          title="Закрыть"
        >
          ×
        </button>
      </div>

      {error && (
        <div className="rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-700">
          {error}
        </div>
      )}

      {patchInfo && (patchInfo.patched_feeds.length > 0 ||
        patchInfo.removed_from_feeds.length > 0 ||
        patchInfo.missing_in_feeds.length > 0) ? (
        <div className="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-800">
          {patchInfo.patched_feeds.length > 0 && (
            <p>
              YML обновлён в фидах:{" "}
              <strong>{patchInfo.patched_feeds.join(", ")}</strong>
            </p>
          )}
          {patchInfo.removed_from_feeds.length > 0 && (
            <p>
              Оффер удалён из фидов:{" "}
              <strong>{patchInfo.removed_from_feeds.join(", ")}</strong>
              {" "}— фото не осталось
            </p>
          )}
          {patchInfo.missing_in_feeds.length > 0 && (
            <p className="mt-1 text-amber-700">
              Товара нет в фидах: <strong>{patchInfo.missing_in_feeds.join(", ")}</strong>.
              Запустите пересборку, чтобы он там появился.
            </p>
          )}
        </div>
      ) : null}

      {/* Images grid */}
      {localProduct.image_urls.length > 0 ? (
        <div>
          <p className="mb-2 text-xs font-medium text-slate-500 uppercase tracking-wide">
            Изображения (первое — главное)
          </p>
          <div className="flex flex-wrap gap-2">
            {localProduct.image_urls.map((url, i) => (
              <div key={localProduct.images[i]} className="flex flex-col items-center gap-1">
                <ImageThumb
                  url={url}
                  isMain={i === 0}
                  onRemove={() => handleDelete(localProduct.images[i])}
                />
                {i !== 0 && (
                  <button
                    onClick={() => handleMoveToFirst(localProduct.images[i])}
                    disabled={saving}
                    className="text-[10px] text-accent hover:underline disabled:opacity-50"
                  >
                    Сделать главным
                  </button>
                )}
              </div>
            ))}
          </div>
        </div>
      ) : (
        <div className="rounded-xl border-2 border-dashed border-slate-200 py-8 text-center text-sm text-slate-400">
          Нет изображений. Загрузите ниже.
        </div>
      )}

      {/* Upload area */}
      <div className="space-y-2">
        <input
          ref={fileRef}
          type="file"
          accept="image/jpeg,image/png,image/webp,image/gif"
          multiple
          className="hidden"
          id={`upload-${localProduct.product_uuid}`}
          onChange={(e) => handleUpload(e.target.files)}
        />
        <label
          htmlFor={`upload-${localProduct.product_uuid}`}
          className={`flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 border-dashed px-4 py-3 text-sm font-medium transition-colors ${
            uploading
              ? "border-accent/30 bg-accent/5 text-accent/60 cursor-not-allowed pointer-events-none"
              : "border-slate-200 text-slate-500 hover:border-accent hover:text-accent"
          }`}
        >
          {uploading ? (
            <>
              <span className="inline-block h-4 w-4 animate-spin rounded-full border-2 border-accent border-t-transparent" />
              Загружаем…
            </>
          ) : (
            <>
              <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Добавить фото (jpeg, png, webp)
            </>
          )}
        </label>
        <p className="text-xs text-slate-400">
          Первое фото — главное. Перетащите для смены порядка через «Сделать главным».
        </p>
      </div>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Main page
// ─────────────────────────────────────────────────────────────────────────────

type HasImagesFilter = "all" | "with" | "without";

export function ProductsPage() {
  const [folders, setFolders] = useState<ProductFolderRow[]>([]);
  const [products, setProducts] = useState<ProductRow[]>([]);
  const [selected, setSelected] = useState<ProductRow | null>(null);

  const [query, setQuery] = useState("");
  const [folderFilter, setFolderFilter] = useState("");
  const [hasImagesFilter, setHasImagesFilter] = useState<HasImagesFilter>("with");

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [stats, setStats] = useState<ProductStats | null>(null);

  const debouncedQuery = useDebounce(query, 400);

  const loadStats = useCallback(() => {
    fetchProductStats()
      .then(setStats)
      .catch(() => setStats(null));
  }, []);

  // Загрузка папок
  useEffect(() => {
    api<ProductFolderRow[]>("/api/product-folders")
      .then(setFolders)
      .catch(() => {});
  }, []);

  useEffect(() => {
    loadStats();
  }, [loadStats]);

  // Загрузка товаров при изменении фильтров
  const loadProducts = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const hasImg: boolean | null =
        hasImagesFilter === "with" ? true : hasImagesFilter === "without" ? false : null;
      const rows = await fetchProducts({
        q: debouncedQuery,
        folder_uuid: folderFilter,
        has_images: hasImg,
      });
      setProducts(rows);
    } catch (e) {
      setError(String(e));
    } finally {
      setLoading(false);
      loadStats();
    }
  }, [debouncedQuery, folderFilter, hasImagesFilter, loadStats]);

  useEffect(() => {
    loadProducts();
  }, [loadProducts]);

  const handleProductUpdate = (updated: ProductRow) => {
    setProducts((prev) =>
      prev.map((p) => (p.product_uuid === updated.product_uuid ? updated : p)),
    );
    setSelected(updated);
    loadStats();
  };

  const handleSelect = (p: ProductRow) => {
    setSelected((prev) => (prev?.product_uuid === p.product_uuid ? null : p));
  };

  /** Уникальные папки по id; при одинаковой подписи в списке — суффикс с id */
  const folderSelectOptions = useMemo(() => {
    const byId = new Map<string, ProductFolderRow>();
    for (const f of folders) {
      if (f.id) byId.set(f.id, f);
    }
    const list = [...byId.values()].sort((a, b) => {
      const pa = (a.path_name || a.name || "").toLowerCase();
      const pb = (b.path_name || b.name || "").toLowerCase();
      return pa.localeCompare(pb, "ru");
    });
    const labelKey = (f: ProductFolderRow) =>
      (f.path_name || f.name || f.id).trim().toLowerCase();
    const counts = new Map<string, number>();
    for (const f of list) {
      const k = labelKey(f);
      counts.set(k, (counts.get(k) || 0) + 1);
    }
    return list.map((f) => {
      const base = (f.path_name || f.name || f.id).trim() || f.id;
      const dup = (counts.get(labelKey(f)) || 0) > 1;
      const label = dup ? `${base} (${f.id.slice(0, 8)}…)` : base;
      return { id: f.id, label };
    });
  }, [folders]);

  return (
    <div className="mx-auto max-w-5xl px-4 py-8 sm:px-6">
      <div className="mb-6">
        <h2 className="text-2xl font-bold tracking-tight text-slate-900">Товары</h2>
        <p className="mt-1 text-sm text-slate-500">
          База пополняется при сборке YML-выгрузок: попадают товары, прошедшие фильтр складов, категорий и наличия.
          В YML включаются <strong>только те, у кого загружено фото</strong>.
        </p>

        <div className="mt-4 flex flex-wrap gap-3">
          <div className="min-w-32 rounded-xl border border-accent/25 bg-accent/5 px-4 py-3">
            <p className="text-xs font-medium text-slate-600">С фото</p>
            <p className="text-2xl font-bold tabular-nums text-accent">
              {stats !== null ? stats.with_images : "…"}
            </p>
            <p className="text-[11px] text-slate-400">загружено в сервисе</p>
          </div>
          <div className="min-w-32 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            <p className="text-xs font-medium text-slate-600">Без фото</p>
            <p className="text-2xl font-bold tabular-nums text-slate-800">
              {stats?.without_images !== null && stats?.without_images !== undefined
                ? stats.without_images
                : "—"}
            </p>
            <p className="text-[11px] text-slate-400">
              не попадут в YML
            </p>
          </div>
          <div className="min-w-32 rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3">
            <p className="text-xs font-medium text-slate-600">Всего в базе</p>
            <p className="text-2xl font-bold tabular-nums text-slate-700">
              {stats?.catalog_products_total !== null &&
              stats?.catalog_products_total !== undefined
                ? stats.catalog_products_total
                : "—"}
            </p>
            <p className="text-[11px] text-slate-400">из YML-выгрузок</p>
          </div>
        </div>
      </div>

      {/* Filters */}
      <div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        {/* Search */}
        <div className="relative flex-1">
          <svg
            className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            type="text"
            placeholder="Поиск по коду, артикулу или названию…"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            className="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20"
          />
        </div>

        {/* Category filter */}
        <select
          value={folderFilter}
          onChange={(e) => setFolderFilter(e.target.value)}
          className="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20 sm:w-52"
        >
          <option value="">Все категории</option>
          {folderSelectOptions.map((o) => (
            <option key={o.id} value={o.id}>
              {o.label}
            </option>
          ))}
        </select>

        {/* Has images filter */}
        <div className="flex items-center gap-1 rounded-xl border border-slate-200 bg-white p-1">
          {(
            [
              ["with", "С фото"],
              ["without", "Без фото"],
              ["all", "Все"],
            ] as [HasImagesFilter, string][]
          ).map(([val, label]) => (
            <button
              key={val}
              onClick={() => setHasImagesFilter(val)}
              className={`rounded-lg px-3 py-1.5 text-xs font-medium transition-colors ${
                hasImagesFilter === val
                  ? "bg-accent text-white shadow-sm"
                  : "text-slate-600 hover:bg-slate-100"
              }`}
            >
              {label}
            </button>
          ))}
        </div>
      </div>

      {/* База пуста — нужно собрать хотя бы одну выгрузку */}
      {stats?.catalog_products_total === 0 && (
        <div className="mb-3 rounded-lg bg-amber-50 border border-amber-200 px-3 py-2 text-sm text-amber-700">
          База товаров пуста. Запустите сборку любой YML-выгрузки — товары, прошедшие фильтры, появятся здесь.
        </div>
      )}

      {error && (
        <div className="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
          {error}
        </div>
      )}

      {/* Main layout: list + panel */}
      <div className={`grid gap-4 ${selected ? "sm:grid-cols-[1fr_380px]" : ""}`}>
        {/* Product list */}
        <div className="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
          {loading ? (
            <div className="flex items-center justify-center py-16 text-sm text-slate-400">
              <span className="mr-2 inline-block h-4 w-4 animate-spin rounded-full border-2 border-accent border-t-transparent" />
              Загружаем…
            </div>
          ) : products.length === 0 ? (
            <div className="flex flex-col items-center justify-center py-16 text-center">
              <div className="mb-3 text-4xl text-slate-200">📦</div>
              {query ? (
                <p className="text-sm text-slate-500">
                  Ничего не найдено по запросу «{query}».
                </p>
              ) : hasImagesFilter === "with" ? (
                <p className="text-sm text-slate-500">
                  Пока нет товаров с изображениями.
                  <br />
                  Переключитесь на «Без фото» и загрузите снимки.
                </p>
              ) : stats?.catalog_products_total === 0 ? (
                <p className="text-sm text-slate-500">
                  База пуста. Сначала запустите сборку любой YML-выгрузки.
                </p>
              ) : (
                <p className="text-sm text-slate-500">Нет товаров по фильтру.</p>
              )}
            </div>
          ) : (
            <div className="divide-y divide-slate-100">
              <div className="px-3 py-2 text-xs font-medium text-slate-400 uppercase tracking-wide border-b border-slate-100">
                {products.length} товар{products.length === 1 ? "" : products.length < 5 ? "а" : "ов"}
              </div>
              {products.map((p) => (
                <ProductListItem
                  key={p.product_uuid}
                  product={p}
                  isSelected={selected?.product_uuid === p.product_uuid}
                  onClick={() => handleSelect(p)}
                />
              ))}
            </div>
          )}
        </div>

        {/* Image management panel */}
        {selected && (
          <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm self-start sticky top-4">
            <ImagePanel
              product={selected}
              onUpdate={handleProductUpdate}
              onClose={() => setSelected(null)}
            />
          </div>
        )}
      </div>
    </div>
  );
}
