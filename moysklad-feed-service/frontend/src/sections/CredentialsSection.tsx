import type { Credentials } from "../api";
import { Button } from "../components/ui/Button";
import { Card } from "../components/ui/Card";
import { Field } from "../components/ui/Field";

type Props = {
  login: string;
  password: string;
  creds: Credentials | null;
  onLoginChange: (v: string) => void;
  onPasswordChange: (v: string) => void;
  onSubmit: (e: React.FormEvent) => void;
};

export function CredentialsSection({
  login,
  password,
  creds,
  onLoginChange,
  onPasswordChange,
  onSubmit,
}: Props) {
  return (
    <Card
      title="Подключение к МойСклад"
      description="Логин и пароль API. На сервере пароль шифруется (FERNET_KEY). Эти данные используются для всех выгрузок."
    >
      <form onSubmit={onSubmit} className="space-y-6">
        <div className="grid gap-4 sm:grid-cols-2">
          <Field
            label="Логин"
            value={login}
            onChange={(e) => onLoginChange(e.target.value)}
            autoComplete="username"
          />
          <Field
            label="Пароль"
            type="password"
            value={password}
            onChange={(e) => onPasswordChange(e.target.value)}
            autoComplete="current-password"
            placeholder={creds?.has_password ? "Оставьте пустым или введите новый" : ""}
          />
        </div>
        <div className="flex flex-wrap items-center gap-3">
          <Button type="submit" variant="primary">
            Сохранить
          </Button>
          <p className="text-xs text-slate-400">
            {creds?.updated_at
              ? `Обновлено: ${creds.updated_at}`
              : "Пока не сохранялось"}
          </p>
        </div>
      </form>
    </Card>
  );
}
