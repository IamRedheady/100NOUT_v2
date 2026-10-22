import type { ReactNode } from "react";
import { NavLink } from "react-router-dom";

const navCls = ({ isActive }: { isActive: boolean }) =>
  `shrink-0 rounded-lg px-2.5 py-2 text-sm font-medium transition-colors sm:px-3 ${
    isActive
      ? "bg-accent text-white shadow-sm shadow-accent/25"
      : "text-slate-600 hover:bg-slate-100"
  }`;

export function AppShell({ children }: { children: ReactNode }) {
  return (
    <div className="min-h-screen">
      <header className="border-b border-slate-200/80 bg-white/90 backdrop-blur-sm">
        <div className="mx-auto flex max-w-5xl flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:gap-4 sm:px-6">
          <div className="flex min-w-0 items-center gap-3">
            <div
              className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent text-sm font-bold text-white shadow-md shadow-accent/30"
              aria-hidden
            >
              Y
            </div>
            <div>
              <p className="text-xs font-semibold uppercase tracking-widest text-accent">
                MoySklad
              </p>
              <h1 className="text-lg font-bold tracking-tight text-slate-900 sm:text-xl">
                YML фиды
              </h1>
            </div>
          </div>
          <nav
            className="-mx-1 flex max-w-full flex-nowrap items-center gap-1 overflow-x-auto overscroll-x-contain px-1 pb-0.5 [-ms-overflow-style:none] [scrollbar-width:none] sm:mx-0 sm:w-auto sm:flex-wrap sm:gap-2 sm:overflow-visible sm:px-0 sm:pb-0 [&::-webkit-scrollbar]:hidden"
            aria-label="Разделы"
          >
            <NavLink to="/settings" className={navCls} end>
              Настройки
            </NavLink>
            <NavLink to="/feeds" className={navCls} end>
              Выгрузки
            </NavLink>
            <NavLink to="/journal" className={navCls} end>
              Журнал
            </NavLink>
            <NavLink to="/products" className={navCls} end>
              Товары
            </NavLink>
          </nav>
        </div>
      </header>
      <main>{children}</main>
      <footer className="mt-12 border-t border-slate-200/80 py-8 text-center text-xs text-slate-400">
        Локальная панель · API и воркер на сервере
      </footer>
    </div>
  );
}
