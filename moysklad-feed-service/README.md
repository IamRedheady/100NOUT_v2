# MoySklad → YML (FastAPI + React + Docker)

Сервис для выгрузки товаров МойСклад в YML с выбором складов, шагом пагинации и расписанием (cron). Публичная ссылка: `/feeds/{slug}.yml`.

## Быстрый старт

1. Сгенерируй ключ шифрования секретов:

```bash
python3 -c "from cryptography.fernet import Fernet; print(Fernet.generate_key().decode())"
```

2. В корне `moysklad-feed-service` создай файл `.env` (Compose подхватит его для подстановки переменных):

```bash
cd moysklad-feed-service
echo "FERNET_KEY=<вставь_сгенерированный_ключ>" > .env
# опционально: PUBLIC_BASE_URL, CORS_ORIGINS
```

Можно взять за основу `.env.example`.

3. Запуск:

```bash
docker compose up --build
```

- API: [http://localhost:8000](http://localhost:8000) (`/health`, `/api/...`)
- Админка: [http://localhost:5173](http://localhost:5173)
- Пример фида (после «Пересобрать»): `http://localhost:8000/feeds/{slug}.yml`

В UI: сохранить логин/пароль МС → загрузить склады → создать выгрузку → пересобрать. Поле **Cron** (UTC) обрабатывает воркер `arq` раз в минуту (например `*/30 * * * *`).

## Локальная разработка (без Docker)

**Backend**

```bash
cd backend
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
export DATABASE_URL=postgresql://...
export REDIS_URL=redis://localhost:6379/0
export FERNET_KEY=...
export PUBLIC_BASE_URL=http://127.0.0.1:8000
uvicorn app.main:app --reload
```

**Worker**

```bash
arq app.worker.WorkerSettings
```

**Frontend**

```bash
cd frontend
npm install
VITE_API_BASE=http://127.0.0.1:8000 npm run dev
```

## API (кратко)

| Метод | Путь | Описание |
|--------|------|----------|
| GET/PUT | `/api/credentials` | Логин/пароль МС (пароль шифруется) |
| GET | `/api/stores` | Склады из МС |
| GET | `/api/product-folders` | Папки товаров (категории) из МС |
| GET/POST | `/api/feeds` | Список / создать выгрузку |
| GET/PATCH/DELETE | `/api/feeds/{id}` | Чтение / правка / удаление |
| POST | `/api/feeds/{id}/rebuild` | Собрать YML синхронно |
| POST | `/api/feeds/{id}/rebuild/async` | Старт сборки в фоне → `{ job_id }` |
| GET | `/api/feeds/rebuild-jobs/{job_id}` | Статус и прогресс (`phase`, `current`/`total`, `public_url`) |
| GET | `/feeds/{slug}.yml` | Публичный фид |

Пустой список складов у выгрузки означает отчёт без фильтра по `store` (все склады). Пустой список категорий (`productfolder_uuids`) — все товары; иначе при сборке YML каждый выбранный UUID папки **разворачивается на все дочерние папки** по дереву `productFolder`, затем товары объединяются без дублей по id.

## Заметки

- В фид попадают **только позиции с остатком больше нуля** по выбранным для выгрузки складам (если склады не выбраны — по всем в отчёте остатков). Используется `report/stock/all?filter=stockMode=positiveOnly;store=…;productFolder=…` — отчёт сразу отфильтрован на стороне МойСклад, без отдельного обхода `/entity/product`.
- Формат YML упрощённый (MVP): `shop`, `offers`, поля `price`, `currencyId`, `count`, `vendorCode`, `description`.
- Для продакшена задайте `PUBLIC_BASE_URL` внешним URL API и проксируйте HTTPS.
