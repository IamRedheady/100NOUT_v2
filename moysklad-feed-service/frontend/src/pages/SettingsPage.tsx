import { useCallback, useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { api, type Credentials } from "../api";
import { AlertBanner } from "../components/AlertBanner";
import { CredentialsSection } from "../sections/CredentialsSection";

export function SettingsPage() {
  const [msg, setMsg] = useState<string | null>(null);
  const [err, setErr] = useState<string | null>(null);
  const [login, setLogin] = useState("");
  const [password, setPassword] = useState("");
  const [creds, setCreds] = useState<Credentials | null>(null);

  const refreshCreds = useCallback(async () => {
    const c = await api<Credentials>("/api/credentials");
    setCreds(c);
    if (c.login) setLogin(c.login);
  }, []);

  useEffect(() => {
    refreshCreds().catch((e) => setErr(String(e)));
  }, [refreshCreds]);

  async function saveCredentials(e: React.FormEvent) {
    e.preventDefault();
    setErr(null);
    setMsg(null);
    try {
      await api<Credentials>("/api/credentials", {
        method: "PUT",
        body: JSON.stringify({ login, password }),
      });
      setPassword("");
      setMsg("Сохранено");
      await refreshCreds();
    } catch (e) {
      setErr(String(e));
    }
  }

  return (
    <>
      <AlertBanner error={err} message={msg} />
      <div className="mx-auto max-w-5xl space-y-8 px-4 py-8 sm:px-6">
        <div>
          <h2 className="text-xl font-bold text-slate-900">Общие настройки</h2>
          <p className="mt-1 text-sm text-slate-500">
            Учётные данные API — один раз для всего сервиса. Выгрузки настраиваются
            отдельно в разделе «Выгрузки».{" "}
            <Link to="/journal" className="font-medium text-accent hover:underline">
              Журнал сборок
            </Link>{" "}
            — история ручных и автоматических пересборок YML.
          </p>
        </div>
        <CredentialsSection
          login={login}
          password={password}
          creds={creds}
          onLoginChange={setLogin}
          onPasswordChange={setPassword}
          onSubmit={saveCredentials}
        />
      </div>
    </>
  );
}
