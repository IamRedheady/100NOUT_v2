# Graph Report - .  (2026-09-22)

## Corpus Check
- 280 files · ~3,393,207 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1559 nodes · 2014 edges · 184 communities detected
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 102 edges (avg confidence: 0.5)
- Token cost: 0 input · 0 output

## God Nodes (most connected - your core abstractions)
1. `WmsOrderApi` - 32 edges
2. `DB` - 31 edges
3. `ProductImages` - 27 edges
4. `MoySkladError` - 27 edges
5. `WmsAssortment` - 27 edges
6. `MoySkladClient` - 26 edges
7. `WmsAssortmentController` - 26 edges
8. `Feed` - 24 edges
9. `ClassLoader` - 24 edges
10. `MsPs_Sync` - 21 edges

## Surprising Connections (you probably didn't know these)
- `Локальный патч YML-фидов: применяет изменение изображений товара ко всем сохранё` --uses--> `Feed`  [INFERRED]
  moysklad-feed-service/backend/app/services/yml_patch.py → moysklad-feed-service/backend/app/models.py
- `Удаляет все <picture> и вставляет новые в правильную позицию.` --uses--> `Feed`  [INFERRED]
  moysklad-feed-service/backend/app/services/yml_patch.py → moysklad-feed-service/backend/app/models.py
- `Прокидывает текущий список фото `filenames` товара `product_uuid` во все     сох` --uses--> `Feed`  [INFERRED]
  moysklad-feed-service/backend/app/services/yml_patch.py → moysklad-feed-service/backend/app/models.py
- `Список feed.id, в YML которых уже встречается оффер с этим UUID.` --uses--> `Feed`  [INFERRED]
  moysklad-feed-service/backend/app/services/yml_patch.py → moysklad-feed-service/backend/app/models.py
- `История сборок YML: running (async стартовал), success | error (итог).` --uses--> `Base`  [INFERRED]
  moysklad-feed-service/backend/app/models.py → moysklad-feed-service/backend/app/database.py

## Communities

### Community 0 - "Community 0"
Cohesion: 0.04
Nodes (93): Base, Base, DeclarativeBase, append_feed_build_log(), append_feed_build_log_from_feed(), finalize_feed_build_log_by_job_id(), Журнал сборок YML (ручных и по расписанию)., Удобная обёртка для объекта Feed. (+85 more)

### Community 1 - "Community 1"
Cohesion: 0.02
Nodes (18): Actions, BaseSettings, Config, Settings, Exception, FillingTablesWithData, FillingTablesWithDataController, FillingTablesWithDataMonitorController (+10 more)

### Community 2 - "Community 2"
Cohesion: 0.03
Nodes (22): api(), deleteProductImage(), fetchBuildLog(), fetchProducts(), fetchProductStats(), parseErr(), reorderProductImages(), handleSave() (+14 more)

### Community 3 - "Community 3"
Cohesion: 0.05
Nodes (6): add_offers_to_product_category_schema(), delete_product_and_images(), get_all_child_categories(), nout_product_availability(), nout_product_meta_qty(), remove_old_products()

### Community 4 - "Community 4"
Cohesion: 0.05
Nodes (3): ClassLoader, includeFile(), InstalledVersions

### Community 5 - "Community 5"
Cohesion: 0.12
Nodes (28): ae(), At(), de(), Dt(), Gt(), Ht(), i(), ie() (+20 more)

### Community 6 - "Community 6"
Cohesion: 0.07
Nodes (9): DataStore, __call(), __get(), getAllowedProps(), getProps(), __set(), setProps(), Schema (+1 more)

### Community 7 - "Community 7"
Cohesion: 0.13
Nodes (2): Images, WmsAssortment

### Community 8 - "Community 8"
Cohesion: 0.12
Nodes (1): WmsOrderApi

### Community 9 - "Community 9"
Cohesion: 0.14
Nodes (1): DB

### Community 10 - "Community 10"
Cohesion: 0.12
Nodes (1): WmsAssortmentController

### Community 11 - "Community 11"
Cohesion: 0.09
Nodes (1): MsPs_Sync

### Community 12 - "Community 12"
Cohesion: 0.11
Nodes (3): StocksStore, StoreException, Stores

### Community 13 - "Community 13"
Cohesion: 0.11
Nodes (6): CheckingForImageUpdatesQueues, ImageQueuesSchema, Init, InitProvider, MoySkladEntitySchema, TableUpdate

### Community 14 - "Community 14"
Cohesion: 0.1
Nodes (1): WmsHelper

### Community 15 - "Community 15"
Cohesion: 0.14
Nodes (3): OrderMetaBoxController, OrderUtil, WmsOrderController

### Community 16 - "Community 16"
Cohesion: 0.11
Nodes (2): WmsWalker, WmsWalkerFactory

### Community 17 - "Community 17"
Cohesion: 0.17
Nodes (1): Wms

### Community 18 - "Community 18"
Cohesion: 0.19
Nodes (1): WmsCounterpartyApi

### Community 19 - "Community 19"
Cohesion: 0.21
Nodes (15): BaseModel, Config, CredentialsIn, CredentialsOut, FeedBase, FeedBuildLogOut, FeedCreate, FeedOut (+7 more)

### Community 20 - "Community 20"
Cohesion: 0.18
Nodes (2): ProductFolder, WmsGroopApi

### Community 21 - "Community 21"
Cohesion: 0.25
Nodes (1): WmsStockController

### Community 22 - "Community 22"
Cohesion: 0.19
Nodes (1): WmsConnectApi

### Community 23 - "Community 23"
Cohesion: 0.12
Nodes (1): MsPs_Jobs

### Community 24 - "Community 24"
Cohesion: 0.21
Nodes (1): MsPs_Api

### Community 25 - "Community 25"
Cohesion: 0.13
Nodes (1): Importers

### Community 26 - "Community 26"
Cohesion: 0.16
Nodes (1): Options

### Community 27 - "Community 27"
Cohesion: 0.18
Nodes (2): CustomerOrderStates, WmsOrderStatusApi

### Community 28 - "Community 28"
Cohesion: 0.16
Nodes (1): Wdc_Options

### Community 29 - "Community 29"
Cohesion: 0.13
Nodes (1): MsPs_Admin

### Community 30 - "Community 30"
Cohesion: 0.16
Nodes (4): get_wms_product_id(), msw_get_assortment_search_fields(), wms_get_product_id(), wms_var_sync()

### Community 31 - "Community 31"
Cohesion: 0.23
Nodes (1): WmsStockApi

### Community 32 - "Community 32"
Cohesion: 0.14
Nodes (1): WmsPrice

### Community 33 - "Community 33"
Cohesion: 0.27
Nodes (1): YFYM_Get_Unit_Offer_Simple

### Community 34 - "Community 34"
Cohesion: 0.14
Nodes (1): MsPs_Order

### Community 35 - "Community 35"
Cohesion: 0.21
Nodes (8): checkAddress(), checkEmail(), checkName(), checkTel(), clearResults(), loadAnswers(), smScroll(), submitOrder()

### Community 36 - "Community 36"
Cohesion: 0.19
Nodes (2): ImagesController, ImageUpdatesQueuesDataStore

### Community 37 - "Community 37"
Cohesion: 0.22
Nodes (1): WmsCounterpartyController

### Community 38 - "Community 38"
Cohesion: 0.15
Nodes (1): WmsData

### Community 39 - "Community 39"
Cohesion: 0.18
Nodes (1): Wdc_Settings_Page

### Community 40 - "Community 40"
Cohesion: 0.19
Nodes (4): wms_load_button_sync(), wms_start_assortment(), wms_start_counterparty(), wms_start_stock()

### Community 41 - "Community 41"
Cohesion: 0.23
Nodes (10): _ensure_feeds_price_type_columns(), _ensure_feeds_productfolder_column(), _ensure_product_images_last_seen_column(), _ensure_product_images_table(), init_db(), Создаёт таблицы, если их ещё нет. Безопасно вызывать из API и из воркера., Добавляет колонку для существующих БД (create_all не меняет схему)., Добавляет колонки price_type_id и old_price_type_id для существующих БД. (+2 more)

### Community 42 - "Community 42"
Cohesion: 0.24
Nodes (6): create_feed(), get_feed(), _hint(), list_feeds(), patch_feed(), _to_out()

### Community 43 - "Community 43"
Cohesion: 0.23
Nodes (2): PriceType, WmsPriceApi

### Community 44 - "Community 44"
Cohesion: 0.32
Nodes (1): WmsUpdate

### Community 45 - "Community 45"
Cohesion: 0.17
Nodes (1): WmsLogs

### Community 46 - "Community 46"
Cohesion: 0.22
Nodes (2): Store, WmsStoreApi

### Community 47 - "Community 47"
Cohesion: 0.18
Nodes (1): WmsWebhook

### Community 48 - "Community 48"
Cohesion: 0.31
Nodes (9): build_children_map(), descendants_including_self(), expand_folder_selection(), folder_id_from_api_row(), _folder_id_from_row(), parent_id_from_folder_row(), Дерево папок товаров МойСклад: развёртка выбранных узлов на все потомки., UUID папки: в ответе МойСклад есть поле id; иначе — последний сегмент meta.href. (+1 more)

### Community 49 - "Community 49"
Cohesion: 0.29
Nodes (1): AssortmentController

### Community 50 - "Community 50"
Cohesion: 0.24
Nodes (2): Organization, WmsOrg

### Community 51 - "Community 51"
Cohesion: 0.24
Nodes (1): WmsCache

### Community 52 - "Community 52"
Cohesion: 0.2
Nodes (1): OrderController

### Community 53 - "Community 53"
Cohesion: 0.24
Nodes (3): i(), o(), t()

### Community 54 - "Community 54"
Cohesion: 0.22
Nodes (1): WmsBundlePublic

### Community 55 - "Community 55"
Cohesion: 0.33
Nodes (1): RestRoute

### Community 56 - "Community 56"
Cohesion: 0.22
Nodes (3): Controller, ControllerException, SyncController

### Community 57 - "Community 57"
Cohesion: 0.42
Nodes (1): WmsImageApi

### Community 58 - "Community 58"
Cohesion: 0.33
Nodes (1): WmsImageController

### Community 59 - "Community 59"
Cohesion: 0.25
Nodes (4): daily_cleanup_orphan_images(), ARQ worker: периодический запуск фидов по cron. Запуск: arq app.worker.WorkerSet, Раз в сутки очищает файлы у товаров, не виденных в МС > N дней., WorkerSettings

### Community 60 - "Community 60"
Cohesion: 0.25
Nodes (0): 

### Community 61 - "Community 61"
Cohesion: 0.25
Nodes (2): Attribute, Translit

### Community 62 - "Community 62"
Cohesion: 0.32
Nodes (1): Entity

### Community 63 - "Community 63"
Cohesion: 0.39
Nodes (1): WmsAttributeApi

### Community 64 - "Community 64"
Cohesion: 0.36
Nodes (5): initZoom(), isPartiallyVisible(), scrolling(), throttleScroll(), zoomOut()

### Community 65 - "Community 65"
Cohesion: 0.29
Nodes (1): WmsMonitor

### Community 66 - "Community 66"
Cohesion: 0.33
Nodes (1): WmsProductVariantApi

### Community 67 - "Community 67"
Cohesion: 0.29
Nodes (1): MsPs_Log

### Community 68 - "Community 68"
Cohesion: 0.33
Nodes (1): Facade

### Community 69 - "Community 69"
Cohesion: 0.53
Nodes (1): WebHook

### Community 70 - "Community 70"
Cohesion: 0.6
Nodes (1): Container

### Community 71 - "Community 71"
Cohesion: 0.4
Nodes (1): Wms_Routes

### Community 72 - "Community 72"
Cohesion: 0.33
Nodes (0): 

### Community 73 - "Community 73"
Cohesion: 0.7
Nodes (4): feed_is_due(), ms_client_from_db(), rebuild_feed(), run_due_feeds()

### Community 74 - "Community 74"
Cohesion: 0.4
Nodes (1): In-memory статусы асинхронной пересборки YML (один процесс API).

### Community 75 - "Community 75"
Cohesion: 0.5
Nodes (4): get_logger(), Единая настройка логирования (FastAPI + arq worker + фоновые задачи)., Идемпотентно настраивает корневые логгеры. Вызывается из API и воркера., setup_logging()

### Community 76 - "Community 76"
Cohesion: 0.5
Nodes (1): NotificationsDataStore

### Community 77 - "Community 77"
Cohesion: 0.4
Nodes (1): Wms_Autoload

### Community 78 - "Community 78"
Cohesion: 0.6
Nodes (1): WmsBundleApi

### Community 79 - "Community 79"
Cohesion: 0.4
Nodes (1): WmsGroupMetabox

### Community 80 - "Community 80"
Cohesion: 0.4
Nodes (1): Wdc_Class_Loader

### Community 81 - "Community 81"
Cohesion: 0.6
Nodes (1): Wdc_Admin

### Community 82 - "Community 82"
Cohesion: 0.4
Nodes (1): WmsConnect

### Community 83 - "Community 83"
Cohesion: 0.83
Nodes (3): decrypt_secret(), encrypt_secret(), _fernet()

### Community 84 - "Community 84"
Cohesion: 0.5
Nodes (1): Json

### Community 85 - "Community 85"
Cohesion: 0.67
Nodes (1): Kernel

### Community 86 - "Community 86"
Cohesion: 0.67
Nodes (1): WmsProductApi

### Community 87 - "Community 87"
Cohesion: 0.5
Nodes (0): 

### Community 88 - "Community 88"
Cohesion: 0.5
Nodes (1): ComposerAutoloaderInite7a2b5f628e7c25c84d203dcfb6b9ee3

### Community 89 - "Community 89"
Cohesion: 0.67
Nodes (2): delete_product_and_images(), remove_old_products()

### Community 90 - "Community 90"
Cohesion: 0.67
Nodes (1): Install

### Community 91 - "Community 91"
Cohesion: 0.67
Nodes (1): FileRequireProvider

### Community 92 - "Community 92"
Cohesion: 0.67
Nodes (1): Providers

### Community 93 - "Community 93"
Cohesion: 0.67
Nodes (1): NotificationsSchema

### Community 94 - "Community 94"
Cohesion: 0.67
Nodes (0): 

### Community 95 - "Community 95"
Cohesion: 0.67
Nodes (0): 

### Community 96 - "Community 96"
Cohesion: 0.67
Nodes (0): 

### Community 97 - "Community 97"
Cohesion: 0.67
Nodes (0): 

### Community 98 - "Community 98"
Cohesion: 0.67
Nodes (1): ComposerStaticInite7a2b5f628e7c25c84d203dcfb6b9ee3

### Community 99 - "Community 99"
Cohesion: 1.0
Nodes (0): 

### Community 100 - "Community 100"
Cohesion: 1.0
Nodes (0): 

### Community 101 - "Community 101"
Cohesion: 1.0
Nodes (0): 

### Community 102 - "Community 102"
Cohesion: 1.0
Nodes (1): Command

### Community 103 - "Community 103"
Cohesion: 1.0
Nodes (1): RouteProvider

### Community 104 - "Community 104"
Cohesion: 1.0
Nodes (1): FiltersProvider

### Community 105 - "Community 105"
Cohesion: 1.0
Nodes (1): ActionProvider

### Community 106 - "Community 106"
Cohesion: 1.0
Nodes (1): Url

### Community 107 - "Community 107"
Cohesion: 1.0
Nodes (0): 

### Community 108 - "Community 108"
Cohesion: 1.0
Nodes (0): 

### Community 109 - "Community 109"
Cohesion: 1.0
Nodes (0): 

### Community 110 - "Community 110"
Cohesion: 1.0
Nodes (0): 

### Community 111 - "Community 111"
Cohesion: 1.0
Nodes (0): 

### Community 112 - "Community 112"
Cohesion: 1.0
Nodes (0): 

### Community 113 - "Community 113"
Cohesion: 1.0
Nodes (0): 

### Community 114 - "Community 114"
Cohesion: 1.0
Nodes (0): 

### Community 115 - "Community 115"
Cohesion: 1.0
Nodes (0): 

### Community 116 - "Community 116"
Cohesion: 1.0
Nodes (0): 

### Community 117 - "Community 117"
Cohesion: 1.0
Nodes (0): 

### Community 118 - "Community 118"
Cohesion: 1.0
Nodes (0): 

### Community 119 - "Community 119"
Cohesion: 1.0
Nodes (0): 

### Community 120 - "Community 120"
Cohesion: 1.0
Nodes (0): 

### Community 121 - "Community 121"
Cohesion: 1.0
Nodes (0): 

### Community 122 - "Community 122"
Cohesion: 1.0
Nodes (0): 

### Community 123 - "Community 123"
Cohesion: 1.0
Nodes (0): 

### Community 124 - "Community 124"
Cohesion: 1.0
Nodes (0): 

### Community 125 - "Community 125"
Cohesion: 1.0
Nodes (0): 

### Community 126 - "Community 126"
Cohesion: 1.0
Nodes (0): 

### Community 127 - "Community 127"
Cohesion: 1.0
Nodes (0): 

### Community 128 - "Community 128"
Cohesion: 1.0
Nodes (0): 

### Community 129 - "Community 129"
Cohesion: 1.0
Nodes (0): 

### Community 130 - "Community 130"
Cohesion: 1.0
Nodes (0): 

### Community 131 - "Community 131"
Cohesion: 1.0
Nodes (0): 

### Community 132 - "Community 132"
Cohesion: 1.0
Nodes (0): 

### Community 133 - "Community 133"
Cohesion: 1.0
Nodes (0): 

### Community 134 - "Community 134"
Cohesion: 1.0
Nodes (0): 

### Community 135 - "Community 135"
Cohesion: 1.0
Nodes (0): 

### Community 136 - "Community 136"
Cohesion: 1.0
Nodes (0): 

### Community 137 - "Community 137"
Cohesion: 1.0
Nodes (0): 

### Community 138 - "Community 138"
Cohesion: 1.0
Nodes (0): 

### Community 139 - "Community 139"
Cohesion: 1.0
Nodes (0): 

### Community 140 - "Community 140"
Cohesion: 1.0
Nodes (0): 

### Community 141 - "Community 141"
Cohesion: 1.0
Nodes (0): 

### Community 142 - "Community 142"
Cohesion: 1.0
Nodes (0): 

### Community 143 - "Community 143"
Cohesion: 1.0
Nodes (0): 

### Community 144 - "Community 144"
Cohesion: 1.0
Nodes (0): 

### Community 145 - "Community 145"
Cohesion: 1.0
Nodes (0): 

### Community 146 - "Community 146"
Cohesion: 1.0
Nodes (0): 

### Community 147 - "Community 147"
Cohesion: 1.0
Nodes (0): 

### Community 148 - "Community 148"
Cohesion: 1.0
Nodes (0): 

### Community 149 - "Community 149"
Cohesion: 1.0
Nodes (0): 

### Community 150 - "Community 150"
Cohesion: 1.0
Nodes (0): 

### Community 151 - "Community 151"
Cohesion: 1.0
Nodes (0): 

### Community 152 - "Community 152"
Cohesion: 1.0
Nodes (0): 

### Community 153 - "Community 153"
Cohesion: 1.0
Nodes (0): 

### Community 154 - "Community 154"
Cohesion: 1.0
Nodes (0): 

### Community 155 - "Community 155"
Cohesion: 1.0
Nodes (0): 

### Community 156 - "Community 156"
Cohesion: 1.0
Nodes (0): 

### Community 157 - "Community 157"
Cohesion: 1.0
Nodes (0): 

### Community 158 - "Community 158"
Cohesion: 1.0
Nodes (0): 

### Community 159 - "Community 159"
Cohesion: 1.0
Nodes (0): 

### Community 160 - "Community 160"
Cohesion: 1.0
Nodes (0): 

### Community 161 - "Community 161"
Cohesion: 1.0
Nodes (0): 

### Community 162 - "Community 162"
Cohesion: 1.0
Nodes (0): 

### Community 163 - "Community 163"
Cohesion: 1.0
Nodes (0): 

### Community 164 - "Community 164"
Cohesion: 1.0
Nodes (0): 

### Community 165 - "Community 165"
Cohesion: 1.0
Nodes (0): 

### Community 166 - "Community 166"
Cohesion: 1.0
Nodes (0): 

### Community 167 - "Community 167"
Cohesion: 1.0
Nodes (0): 

### Community 168 - "Community 168"
Cohesion: 1.0
Nodes (0): 

### Community 169 - "Community 169"
Cohesion: 1.0
Nodes (0): 

### Community 170 - "Community 170"
Cohesion: 1.0
Nodes (0): 

### Community 171 - "Community 171"
Cohesion: 1.0
Nodes (0): 

### Community 172 - "Community 172"
Cohesion: 1.0
Nodes (0): 

### Community 173 - "Community 173"
Cohesion: 1.0
Nodes (0): 

### Community 174 - "Community 174"
Cohesion: 1.0
Nodes (0): 

### Community 175 - "Community 175"
Cohesion: 1.0
Nodes (0): 

### Community 176 - "Community 176"
Cohesion: 1.0
Nodes (0): 

### Community 177 - "Community 177"
Cohesion: 1.0
Nodes (0): 

### Community 178 - "Community 178"
Cohesion: 1.0
Nodes (0): 

### Community 179 - "Community 179"
Cohesion: 1.0
Nodes (0): 

### Community 180 - "Community 180"
Cohesion: 1.0
Nodes (0): 

### Community 181 - "Community 181"
Cohesion: 1.0
Nodes (0): 

### Community 182 - "Community 182"
Cohesion: 1.0
Nodes (0): 

### Community 183 - "Community 183"
Cohesion: 1.0
Nodes (0): 

## Knowledge Gaps
- **27 isolated node(s):** `WorkerSettings`, `ARQ worker: периодический запуск фидов по cron. Запуск: arq app.worker.WorkerSet`, `Раз в сутки очищает файлы у товаров, не виденных в МС > N дней.`, `Создаёт таблицы, если их ещё нет. Безопасно вызывать из API и из воркера.`, `Добавляет колонку для существующих БД (create_all не меняет схему).` (+22 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **Thin community `Community 99`** (2 nodes): `deps.py`, `get_ms_client()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 100`** (2 nodes): `folders.py`, `list_folders()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 101`** (2 nodes): `prices.py`, `list_price_types()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 102`** (2 nodes): `Command.php`, `Command`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 103`** (2 nodes): `RouteProvider.php`, `RouteProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 104`** (2 nodes): `FiltersProvider.php`, `FiltersProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 105`** (2 nodes): `ActionProvider.php`, `ActionProvider`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 106`** (2 nodes): `Url.php`, `Url`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 107`** (2 nodes): `wms-admin-counterparty.php`, `wms_counterparty_button()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 108`** (2 nodes): `wms-script-admin.js`, `wms_send_order()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 109`** (1 nodes): `404.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 110`** (1 nodes): `index.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 111`** (1 nodes): `archive.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 112`** (1 nodes): `ajax.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 113`** (1 nodes): `comments.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 114`** (1 nodes): `webpack.config.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 115`** (1 nodes): `search.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 116`** (1 nodes): `header.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 117`** (1 nodes): `footer.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 118`** (1 nodes): `single.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 119`** (1 nodes): `page.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 120`** (1 nodes): `postcss.config.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 121`** (1 nodes): `sidebar.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 122`** (1 nodes): `vite.config.ts`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 123`** (1 nodes): `vite-env.d.ts`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 124`** (1 nodes): `webpack.dev.config.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 125`** (1 nodes): `webpack.prod.config.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 126`** (1 nodes): `environment.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 127`** (1 nodes): `content-product.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 128`** (1 nodes): `content-product-prs.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 129`** (1 nodes): `content-product-prs-mini-cart.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 130`** (1 nodes): `ti-wishlist.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 131`** (1 nodes): `archive-product.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 132`** (1 nodes): `content-single-product.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 133`** (1 nodes): `content-product-us.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 134`** (1 nodes): `ti-wishlist-empty.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 135`** (1 nodes): `ti-wishlist-product-counter.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 136`** (1 nodes): `ti-wishlist-header.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 137`** (1 nodes): `ti-addtowishlist.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 138`** (1 nodes): `review-order.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 139`** (1 nodes): `form-billing.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 140`** (1 nodes): `form-checkout.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 141`** (1 nodes): `form-shipping.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 142`** (1 nodes): `cart.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 143`** (1 nodes): `cart-empty.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 144`** (1 nodes): `mini-cart.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 145`** (1 nodes): `breadcrumb.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 146`** (1 nodes): `no-products-found.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 147`** (1 nodes): `add-to-cart.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 148`** (1 nodes): `no-product-found.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 149`** (1 nodes): `uninstall.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 150`** (1 nodes): `bootstrap.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 151`** (1 nodes): `allowed-webhook.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 152`** (1 nodes): `classes.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 153`** (1 nodes): `wms-admin.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 154`** (1 nodes): `autoload_namespaces.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 155`** (1 nodes): `autoload_psr4.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 156`** (1 nodes): `autoload_classmap.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 157`** (1 nodes): `installed.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 158`** (1 nodes): `content-none.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 159`** (1 nodes): `content-page.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 160`** (1 nodes): `content.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 161`** (1 nodes): `content-search.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 162`** (1 nodes): `wishlist.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 163`** (1 nodes): `obmen.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 164`** (1 nodes): `ur.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 165`** (1 nodes): `vacancies.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 166`** (1 nodes): `delivery.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 167`** (1 nodes): `blog-post.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 168`** (1 nodes): `home.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 169`** (1 nodes): `dso.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 170`** (1 nodes): `skupka-dev.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 171`** (1 nodes): `trade-in.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 172`** (1 nodes): `payment.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 173`** (1 nodes): `order-test.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 174`** (1 nodes): `blog.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 175`** (1 nodes): `skupka.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 176`** (1 nodes): `thankyou.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 177`** (1 nodes): `reviews.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 178`** (1 nodes): `oferta.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 179`** (1 nodes): `contacts.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 180`** (1 nodes): `about.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 181`** (1 nodes): `compare.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 182`** (1 nodes): `ms-products-sync.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 183`** (1 nodes): `catalogModes.js`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `MoySkladError` connect `Community 0` to `Community 1`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `WmsOrderApi` connect `Community 8` to `Community 1`?**
  _High betweenness centrality (0.016) - this node is a cross-community bridge._
- **Why does `DB` connect `Community 9` to `Community 6`?**
  _High betweenness centrality (0.015) - this node is a cross-community bridge._
- **Are the 24 inferred relationships involving `ProductImages` (e.g. with `Base` and `Управление изображениями товаров. Изображения хранятся локально: {media_dir}/{pr`) actually correct?**
  _`ProductImages` has 24 INFERRED edges - model-reasoned connections that need verification._
- **Are the 23 inferred relationships involving `MoySkladError` (e.g. with `Управление изображениями товаров. Изображения хранятся локально: {media_dir}/{pr` and `Список товаров — только из локальной БД.     База пополняется автоматически при`) actually correct?**
  _`MoySkladError` has 23 INFERRED edges - model-reasoned connections that need verification._
- **What connects `WorkerSettings`, `ARQ worker: периодический запуск фидов по cron. Запуск: arq app.worker.WorkerSet`, `Раз в сутки очищает файлы у товаров, не виденных в МС > N дней.` to the rest of the system?**
  _27 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Community 0` be split into smaller, more focused modules?**
  _Cohesion score 0.04 - nodes in this community are weakly interconnected._