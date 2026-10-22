import type { ProductFolderRow } from "./api";

/** parent_id -> дочерние id (только если родитель есть в списке) */
export function buildChildrenMap(folders: ProductFolderRow[]): Map<string, string[]> {
  const allIds = new Set(folders.map((f) => f.id));
  const children = new Map<string, string[]>();
  for (const f of folders) {
    const p = f.parent_id;
    if (p && allIds.has(p)) {
      if (!children.has(p)) children.set(p, []);
      children.get(p)!.push(f.id);
    }
  }
  return children;
}

/** Узел и все потомки в дереве children */
export function collectDescendants(
  children: Map<string, string[]>,
  root: string,
): Set<string> {
  const out = new Set<string>([root]);
  const stack = [root];
  while (stack.length) {
    const u = stack.pop()!;
    for (const c of children.get(u) || []) {
      if (!out.has(c)) {
        out.add(c);
        stack.push(c);
      }
    }
  }
  return out;
}

export function toggleFolderWithSubtree(
  prev: Record<string, boolean>,
  folderId: string,
  checked: boolean,
  children: Map<string, string[]>,
  includeSubtree: boolean,
): Record<string, boolean> {
  const next = { ...prev };
  const targets = includeSubtree
    ? collectDescendants(children, folderId)
    : new Set([folderId]);
  if (checked) {
    targets.forEach((id) => {
      next[id] = true;
    });
  } else {
    targets.forEach((id) => {
      delete next[id];
    });
  }
  return next;
}

export function folderSearchMatch(f: ProductFolderRow, q: string): boolean {
  if (!q.trim()) return true;
  const s = q.trim().toLowerCase();
  const hay = `${f.path_name} ${f.name}`.toLowerCase();
  return hay.includes(s);
}
