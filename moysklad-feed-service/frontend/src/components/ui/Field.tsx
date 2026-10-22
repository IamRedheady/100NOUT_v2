import type { InputHTMLAttributes, ReactNode } from "react";

type Props = {
  label: string;
  hint?: ReactNode;
  /** Обертка label */
  wrapperClassName?: string;
} & InputHTMLAttributes<HTMLInputElement>;

export function Field({
  label,
  hint,
  wrapperClassName = "",
  className = "",
  id,
  ...input
}: Props) {
  const fid = id ?? label.replace(/\s+/g, "-").toLowerCase();
  return (
    <label
      htmlFor={fid}
      className={`flex flex-col gap-1.5 ${wrapperClassName}`}
    >
      <span className="text-xs font-medium uppercase tracking-wide text-slate-500">
        {label}
      </span>
      <input
        id={fid}
        className={`rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/25 ${className}`}
        {...input}
      />
      {hint ? (
        <span className="text-xs text-slate-400">{hint}</span>
      ) : null}
    </label>
  );
}
