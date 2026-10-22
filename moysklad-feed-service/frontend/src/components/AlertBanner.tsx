type Props = {
  error: string | null;
  message: string | null;
};

export function AlertBanner({ error, message }: Props) {
  if (!error && !message) return null;
  return (
    <div className="mx-auto max-w-5xl space-y-3 px-4 pt-6 sm:px-6">
      {error ? (
        <div
          role="alert"
          className="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
        >
          {error}
        </div>
      ) : null}
      {message ? (
        <div className="rounded-xl border border-accent/30 bg-accent-soft px-4 py-3 text-sm font-medium text-accent-dark">
          {message}
        </div>
      ) : null}
    </div>
  );
}
