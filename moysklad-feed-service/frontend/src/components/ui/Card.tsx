import type { ReactNode } from "react";

type Props = {
  step?: number;
  title: string;
  description?: string;
  children: ReactNode;
  className?: string;
};

export function Card({ step, title, description, children, className = "" }: Props) {
  return (
    <section
      className={`rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm shadow-slate-900/[0.04] sm:p-7 ${className}`}
    >
      <header className="mb-6 flex gap-4">
        {step != null && (
          <span
            className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-sm font-bold text-accent"
            aria-hidden
          >
            {step}
          </span>
        )}
        <div className="min-w-0 flex-1">
          <h2 className="text-lg font-semibold tracking-tight text-slate-900">
            {title}
          </h2>
          {description ? (
            <p className="mt-1 text-sm leading-relaxed text-slate-500">
              {description}
            </p>
          ) : null}
        </div>
      </header>
      {children}
    </section>
  );
}
