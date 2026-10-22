<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 23.01.2018
 * Time: 23:21
 */

//TODO:добавить возможность обновлять товар из карточки
use WCSTORES\WC\MS\Queues\Queues;
use WCSTORES\WC\MS\Wordpress\Rest\RestRoute;

/**
 * Class WmsAssortmentController
 */
class WmsAssortmentController
{
    /**
     * @var mixed|void
     */
    private $limit;
    /**
     * @var null
     */
    private $settings = null;

    /**
     * @var null
     */
    private $offset = null;
    /**
     * @var
     */
    private $walker;

    /**
     * WmsAssortmentController constructor.
     */
    public function __construct()
    {

        $settings = get_option('wms_settings_product');
        $this->set_settings($settings);

        $limit = 25;
        if (isset($this->settings['wms_product_limit']) and $this->settings['wms_product_limit'] > 5) {
            $limit = $this->settings['wms_product_limit'];
        }
        $this->limit = apply_filters('wms_limit_product', $limit);

    }


    /**
     * @version 1.0.4
     * WmsAssortmentController constructor.
     */
    public function init()
    {

        if (wp_doing_ajax()) {
            add_action('wp_ajax_wms-load-start-product-syn', array($this, 'start_product_syn'));
            add_action('wp_ajax_nopriv_wms-load-start-product-syn', array($this, 'start_product_syn'));

            add_action('wp_ajax_wms_product_load', array($this, 'start_product_syn'));
            add_action('wp_ajax_nopriv_wms_product_load', array($this, 'start_product_syn'));

        }


        if (is_admin()) {
            add_action('init', array($this, 'wallker_assortment_daily'));
            add_action('add_meta_boxes', array($this, 'add_meta_box'));
            add_filter('manage_edit-product_columns', array($this, 'wms_manage_edit_assortment_columns'));
            add_action('manage_product_posts_custom_column', array($this, 'wms_manage_assortment_posts_custom_column'));
            add_action('woocommerce_product_after_variable_attributes', array($this, 'wms_manage_assortment_variation_posts_custom_column'), 10, 3);

        }

        add_action('wcstores_moysklad_queues_sync_products_updates', array($this, 'sync'));
        add_action('wcstores_moysklad_queues_checking_for_products_updates', array($this, 'sync'));

        // Walker cron отключён — он создавал третий параллельный источник вызовов sync(),
        // что приводило к скачкам offset и дублированию запросов.
        // add_action('wms_walker_hook_assortment', array($this, 'sync'));
        add_action('wms_assortment_daily', array($this, 'start_product_syn'));


        if (isset($this->settings['wms_load_groops']) and $this->settings['wms_load_groops'] == 'on') {
            WmsGroupMetabox::init();
        }

        //add_filter('cron_schedules', array($this, 'add_assortment_schedule'));
    }

    /**
     *
     */
    public function wallker_assortment_daily()
    {
        if (isset($this->settings['wms_load_auto_product']) and $this->settings['wms_load_auto_product'] == 'on') {

            if (!wp_next_scheduled('wms_assortment_daily')) {
                WmsLogs::set_logs('Товары будут загружаться один раз в день', true);
                wp_schedule_event(time() + 60 * 60 * 12, 'daily', 'wms_assortment_daily');
            }
        } else {
            wp_clear_scheduled_hook('wms_assortment_daily');
        }
    }


    /**
     * @throws Exception
     * Стартуем синхрон товаров
     */
    public function start_product_syn()
    {
        // sleep((float) (rand(0, 60) / 100) + rand(0, 6));

        // Проверяем блокировку синхронизации остатков
        if (!WmsHelper::can_start_products_sync()) {
            WmsLogs::set_logs('Синхронизация товаров отменена: идет синхронизация остатков', true);
            return;
        }

        $sStartTime = WmsWalkerFactory::get_walker('assortment')->get_start_walker();
        if ($sStartTime and is_numeric($sStartTime) and $sStartTime > 0) {
            if ((time() - $sStartTime) < (apply_filters('wms_assortment_start_time_checking_for_hang_ups', 3600 * 6))) {
                WmsLogs::set_logs('Уже идет синхронизация товаров', true);
                return;
            }
        }

        delete_transient('wms_products_sync_stop_requested');
        WmsHelper::set_sync_lock('products');

        // Сбрасываем offset и отменяем ВСЕ цепочки синхронизации товаров (иначе две цепочки работают параллельно и offset прыгает 0 → 1100)
        delete_transient('wms_offset_assortment');
        Queues::unscheduleAllActions('sync_products_updates', [], 'mswoo');
        Queues::unscheduleAllActions('checking_for_products_updates', [], 'mswoo');
        Queues::unscheduleAllActions('sync_products', [], 'queues');
        Queues::unscheduleAllActions('sync_products_check', [], 'queues');

        WmsLogs::set_logs('Стартуем (синхронизация товаров)', true);
        update_option('wms_product_update_start_time', current_time("Y-m-d"));
        update_option('wms_product_update_start', array('load' => 'start', 'message' => 'Начало полной синхронизации...'));

        $actionId = Queues::addAsync('sync_products_updates', [], 'mswoo', false, 1);

        // Запускаем первую задачу сразу, не дожидаясь cron
        if ($actionId && is_numeric($actionId) && class_exists('ActionScheduler')) {
            try {
                \ActionScheduler::runner()->process_action($actionId);
                WmsLogs::set_logs('Первая задача очереди товаров (sync_products_updates) выполнена', true);
            } catch (\Exception $e) {
                WmsLogs::set_logs('Ошибка запуска первой задачи: ' . $e->getMessage(), true);
            }
        }

        wp_die();
    }


    /**
     * Версия start_product_syn() для вызова из ActionScheduler (без wp_die, с ретраем при блокировке).
     * Вызывается из AssortmentController::startingProductSynchronizationAutomatic().
     *
     * @throws Exception
     */
    public function start_product_syn_scheduled()
    {
        WmsLogs::set_logs('start_product_syn_scheduled() вызван из ActionScheduler', true);

        // Если идёт синхронизация остатков — ставим повторную попытку через 30 минут
        if (!WmsHelper::can_start_products_sync()) {
            WmsLogs::set_logs('Синхронизация товаров (расписание): заблокирована остатками, повтор через 30 мин', true);
            Queues::addSingle(time() + 1800, 'sync_products_updates', [], 'mswoo');
            return;
        }

        $sStartTime = WmsWalkerFactory::get_walker('assortment')->get_start_walker();
        if ($sStartTime and is_numeric($sStartTime) and $sStartTime > 0) {
            if ((time() - $sStartTime) < (apply_filters('wms_assortment_start_time_checking_for_hang_ups', 3600 * 6))) {
                WmsLogs::set_logs('Синхронизация товаров (расписание): уже идёт (walker), пропускаем', true);
                return;
            }
        }

        delete_transient('wms_products_sync_stop_requested');
        WmsHelper::set_sync_lock('products');

        delete_transient('wms_offset_assortment');
        Queues::unscheduleAllActions('sync_products_updates', [], 'mswoo');
        Queues::unscheduleAllActions('sync_products', [], 'queues');
        Queues::unscheduleAllActions('sync_products_check', [], 'queues');

        WmsLogs::set_logs('Стартуем (расписание: синхронизация товаров)', true);
        update_option('wms_product_update_start_time', current_time("Y-m-d"));
        update_option('wms_product_update_start', array('load' => 'start', 'message' => 'Начало полной синхронизации (расписание)...'));

        $actionId = Queues::addAsync('sync_products_updates', [], 'mswoo', false, 1);
        WmsLogs::set_logs('Задача sync_products_updates добавлена, id=' . (string) $actionId, true);
    }


    /**
     * @return mixed|void
     */
    public function get_limit()
    {
        return $this->limit;
    }

    /**
     * @return null
     */
    public function get_settings()
    {
        return $this->settings;
    }

    /**
     * @param $offset
     */
    public function set_offset($offset)
    {
        $this->offset = $offset;
    }


    /**
     * @param null $settings
     */
    public function set_settings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param $url
     * @param $action
     * @throws Exception
     */
    public function assortment_webhook($url, $action, $endHook = true)
    {

        $expand = apply_filters('wms_get_assortment_url_expand', 'productFolder,images,country');
        $url = apply_filters('wms_product_assortment_webhook_url', $url . '?expand=' . $expand);

        $assortment = WmsConnectApi::get_instance()->send_request($url);
        $assortment = apply_filters('wms_load_array_products_by_controller', $assortment, $this->settings);

        if ($assortment !== false) {
            $this->add_assortment($assortment, $this->settings);
        }

        update_option('wms_product_update_start', array('load' => 'stop', 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Сработал вебхук'));

        if ($endHook) {
            do_action('wms_assortment_end_sync');
        }

    }

    /**
     * @param string $typeSync
     * @return int|string[]|void
     * @throws Exception
     */
    public function sync($typeSync = 'web')
    {
        WmsLogs::set_logs('WmsAssortmentController::sync() вызван (тип=' . (string) $typeSync . ')', true);

        // Проверяем флаг остановки
        if (get_transient('wms_products_sync_stop_requested')) {
            WmsLogs::set_logs('WmsAssortmentController::sync() — остановка запрошена, выходим', true);
            WmsHelper::remove_sync_lock('products');
            delete_transient('wms_offset_assortment');
            update_option('wms_product_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));
            delete_transient('wms_products_sync_stop_requested');
            return ['stopped'];
        }

        if (!WmsHelper::can_start_products_sync()) {
            WmsLogs::set_logs('Синхронизация товаров отменена: идет синхронизация остатков', true);
            return ['blocked'];
        }

        WmsHelper::set_sync_lock('products');

        // Чтобы не было двух параллельных цепочек (offset 0 и offset 1100): отменяем задачу второй цепочки (Importers)
        Queues::unscheduleAllActions('sync_products', [], 'queues');
        Queues::unscheduleAllActions('sync_products_check', [], 'queues');

        do_action('wms_assortment_start_sync_loop');

        $offset = get_transient('wms_offset_assortment');
        $offset = $offset === false ? 0 : $offset;

        $syncStartTime = microtime(true);
        $products = $this->get_assortment($offset);
        $syncTime = round(microtime(true) - $syncStartTime, 2);

        $count = isset($products['rows']) ? count($products['rows']) : 0;
        $nextOffset = $offset + $this->limit;

        WmsLogs::set_logs("Offset: {$offset}, получено: {$count}, API: {$syncTime} сек", true);

        if ($count < $this->limit or empty($products['rows'])) {

            $count_product = $offset + $count;
            WmsLogs::set_logs('Синхронизация закончилась. ' . $count_product . ' товаров синхронизировано', true);
            update_option('wms_product_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Полная синхронизация'));

            delete_transient('wms_offset_assortment');
            WmsHelper::remove_sync_lock('products');

            do_action('wms_assortment_end_sync');

            return ['ok'];
        }

        // Сохраняем следующий offset ДО обработки, чтобы при ошибке/таймауте не повторять тот же offset
        set_transient('wms_offset_assortment', $nextOffset);

        // ВАЖНО: планируем следующий шаг заранее.
        // Иначе если update_products/create_products упадут по таймауту/фаталу, следующий шаг не будет поставлен и синхронизация "зависнет".
        // Используем unique=true, чтобы не плодить дубликаты.
        $nextQueueId = Queues::addAsync('sync_products_updates', [], 'mswoo', true);
        if (!$nextQueueId) {
            WmsLogs::set_logs('ОШИБКА: не удалось поставить следующий шаг sync_products_updates в очередь', true);
        }

        if ($aProducts = $this->get_products($products)) {

            $aProducts = $this->update_products($aProducts);

            if (!empty($aProducts)) {
                $this->create_products($aProducts);
            }

        }

        update_option('wms_product_update_start', array('size' => $products['meta']['size'], "count" => $offset, "time" => 0, 'load' => 'load'));

        do_action('wms_assortment_end_sync_loop');

        // Проверяем флаг остановки после обработки — отменяем запланированный следующий шаг
        if (get_transient('wms_products_sync_stop_requested')) {
            WmsLogs::set_logs('WmsAssortmentController::sync() — остановка запрошена после обработки, отменяем следующий шаг', true);
            Queues::unscheduleAllActions('sync_products_updates', [], 'mswoo');
            WmsHelper::remove_sync_lock('products');
            delete_transient('wms_offset_assortment');
            update_option('wms_product_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));
            delete_transient('wms_products_sync_stop_requested');
            return ['stopped'];
        }

        // Следующий шаг уже поставлен заранее (см. $nextQueueId)
        return $nextQueueId;

    }


    /**
     * @param $products
     * @return array|null
     */
    public function get_products($products)
    {
        $aProducts = [];

        if (!isset($products['rows'])) {
            return null;
        }


        if (empty($products['rows'])) {
            return null;
        }

        foreach ($products['rows'] as $product) {

            $product = apply_filters('wms_load_array_products_by_controller', $product, $this->settings);

            if ($product !== false) {
                $sSearchFieldName = msw_get_assortment_search_fields($product, $this->settings['wms_product_select_var']);
                $aProducts[$sSearchFieldName] = $product;
            }

        }

        return $aProducts;

    }

    /**
     * @param $aProducts
     * @return mixed
     * @throws Exception
     */
    public function update_products($aProducts)
    {
        $totalIncoming = count($aProducts);
        WmsLogs::set_logs('[update_products] Начало обновления. Товаров на входе: ' . $totalIncoming, true);

        if ($aoProducts = wms_get_products_by_uuid_ms(array_keys($aProducts), $this->limit * 10)) {

            $foundInWC = count($aoProducts);
            WmsLogs::set_logs('[update_products] Найдено в WooCommerce по ключам: ' . $foundInWC, true);

            $aSearchFieldsName = [
                '_id_ms',
                '_externalCode',
                'ms_externalCode',
                'ms_article',
                'ms_code',
            ];

            $updatedCount = 0;

            foreach ($aoProducts as $oProduct) {

                if (!empty($oProduct->get_sku()) and isset($aProducts[$oProduct->get_sku()])) {
                    $msProductName = isset($aProducts[$oProduct->get_sku()]['name']) ? $aProducts[$oProduct->get_sku()]['name'] : '?';
                    WmsLogs::set_logs('[update_products] Обновляем: "' . $msProductName . '" (WC_ID=' . $oProduct->get_id() . ', найдено по SKU=' . $oProduct->get_sku() . ')', true);
                    $this->add_assortment($aProducts[$oProduct->get_sku()], $this->settings, $oProduct);
                    unset($aProducts[$oProduct->get_sku()]);
                    $updatedCount++;
                    continue;
                } else {

                    foreach ($aSearchFieldsName as $sSearchFieldName) {

                        $sWCFieldName = wms_get_meta_by_object($oProduct, $sSearchFieldName);

                        if (!empty($sWCFieldName) and isset($aProducts[$sWCFieldName])) {
                            $msProductName = isset($aProducts[$sWCFieldName]['name']) ? $aProducts[$sWCFieldName]['name'] : '?';
                            WmsLogs::set_logs('[update_products] Обновляем: "' . $msProductName . '" (WC_ID=' . $oProduct->get_id() . ', найдено по ' . $sSearchFieldName . '=' . $sWCFieldName . ')', true);
                            $this->add_assortment($aProducts[$sWCFieldName], $this->settings, $oProduct);
                            unset($aProducts[$sWCFieldName]);
                            $updatedCount++;
                            continue;
                        }
                    }

                }

            }

            WmsLogs::set_logs('[update_products] Обновлено: ' . $updatedCount . ', не найдено в WC (кандидаты на создание): ' . count($aProducts), true);

        } else {
            WmsLogs::set_logs('[update_products] wms_get_products_by_uuid_ms вернул false — ни один товар не найден в WC, все пойдут на создание', true);
        }

        return $aProducts;

    }


    /**
     * @param $aProducts
     * @return mixed
     * @throws Exception
     */
    public function create_products($aProducts)
    {

        if (!empty($aProducts)) {
            $total = count($aProducts);
            $done = 0;
            foreach ($aProducts as $value) {
                $this->add_assortment($value);
                $done++;
                if ($done % 10 === 0 || $done === $total) {
                    WmsLogs::set_logs('create_products: обработано ' . $done . '/' . $total, true);
                }
            }
        }

        return $aProducts;

    }

    /**
     * @param $offset
     *
     * @return bool|mixed
     * @throws Exception
     */
    public function get_assortment($offset)
    {
        $args = [
            'offset' => $offset,
            'limit' => $this->limit,
            'expand' => apply_filters('wms_get_assortment_url_expand', 'images,productFolder,country,components'),
        ];

        // Список складов МойСклад — в ассортимент попадают только товары с остатком > 0 на этих складах
        $stores = [
            'https://api.moysklad.ru/api/remap/1.2/entity/store/6d91a58f-5aa1-11f1-0a80-03be0012a19c',
            'https://api.moysklad.ru/api/remap/1.2/entity/store/6cc303d7-dde0-11ed-0a80-0830000fbf5c',
            'https://api.moysklad.ru/api/remap/1.2/entity/store/476658d3-ab92-11ee-0a80-07a9004572e9',
            'https://api.moysklad.ru/api/remap/1.2/entity/store/fc9022b7-926b-11ed-0a80-071b000f2474',
            'https://api.moysklad.ru/api/remap/1.2/entity/store/3e71ca84-9857-11ef-0a80-0b92001125ef',
            'https://api.moysklad.ru/api/remap/1.2/entity/store/1194b3f3-7b93-11ed-0a80-011100257ff5',
            'https://api.moysklad.ru/api/remap/1.2/entity/store/01de1406-8fa0-11ef-0a80-19b700267bd1',
            'https://api.moysklad.ru/api/remap/1.2/entity/store/365ed422-75f2-11f1-0a80-1c14001db9ce', // Боровая
            'https://api.moysklad.ru/api/remap/1.2/entity/store/fe67cd68-8f2a-11f1-0a80-1afc0031dfd6', // Черняховского
        ];

        $storeFilters = array_map(function ($store) {
            return 'stockStore=' . $store;
        }, $stores);

        if (isset($this->settings['wms_product_variant_load']) and $this->settings['wms_product_variant_load'] == 'updated') {
            $updated = get_option('wms_product_update_start_time');
            if ($updated) {
                $baseFilter = 'updated>' . $updated;
            } else {
                $baseFilter = 'archived=false';
            }
        } elseif (isset($this->settings['wms_product_variant_load']) and $this->settings['wms_product_variant_load'] == 'archived') {
            $baseFilter = 'archived=true';
        } else {
            $baseFilter = 'archived=false';
        }

        // stockMode=positiveonly + склады: только товары с остатком > 0 на указанных складах
        $filterParam = urlencode('stockMode=positiveonly;' . implode(';', $storeFilters) . ';' . $baseFilter);

        $url = WMS_URL_API_V2 . '/entity/assortment/?offset=' . $offset .
            '&limit=' . $this->limit .
            '&expand=product,country,productFolder,components,images' .
            '&filter=' . $filterParam;

        WmsLogs::set_logs('[get_assortment] Режим: ' . (isset($this->settings['wms_product_variant_load']) ? $this->settings['wms_product_variant_load'] : 'archived=false') . ', фильтр по ' . count($stores) . ' складам (stockMode=positiveonly)', true);
        WmsLogs::set_logs('[get_assortment] Фильтр: ' . urldecode($filterParam), true);

        WmsLogs::set_logs('=== Начало запроса assortment ===', true);
        // WmsLogs::set_logs('URL: ' . $url, true);
        WmsLogs::set_logs('Offset: ' . $offset . ', Limit: ' . $this->limit, true);

        // Засекаем время начала запроса
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $assortment = WmsConnectApi::get_instance()->send_request($url);

        // Засекаем время окончания запроса
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        $executionTime = round($endTime - $startTime, 2);
        $memoryUsed = round(($endMemory - $startMemory) / 1024 / 1024, 2);

        // Логируем результаты запроса
        if ($assortment === false) {
            WmsLogs::set_logs('=== ОШИБКА: Запрос вернул false ===', true);
            WmsLogs::set_logs('Время выполнения: ' . $executionTime . ' сек', true);
            WmsWalkerFactory::get_walker('assortment')->delete_walker();
            update_option('wms_product_update_start', array('time' => 'Ошибка'));
            wp_die();
        }

        // Подсчитываем размер ответа
        $responseSize = strlen(json_encode($assortment));
        $responseSizeKB = round($responseSize / 1024, 2);
        $responseSizeMB = round($responseSize / 1024 / 1024, 2);

        // Получаем метаданные
        $rowsCount = isset($assortment['rows']) ? count($assortment['rows']) : 0;
        $totalSize = isset($assortment['meta']['size']) ? $assortment['meta']['size'] : 0;

        // Логируем детальную информацию
        WmsLogs::set_logs('=== Результаты запроса assortment ===', true);
        WmsLogs::set_logs('Время выполнения: ' . $executionTime . ' сек', true);
        WmsLogs::set_logs('Использовано памяти: ' . $memoryUsed . ' MB', true);
        WmsLogs::set_logs('Размер ответа: ' . $responseSizeKB . ' KB (' . $responseSizeMB . ' MB)', true);
        WmsLogs::set_logs('Товаров в ответе: ' . $rowsCount, true);
        WmsLogs::set_logs('Всего товаров (meta.size): ' . $totalSize, true);
        WmsLogs::set_logs('Offset текущий: ' . $offset . ', следующий: ' . ($offset + $this->limit), true);

        // if (isset($assortment['meta'])) {
        //     WmsLogs::set_logs('Метаданные: ' . json_encode($assortment['meta']), true);
        // }

        WmsLogs::set_logs('=== Конец запроса assortment ===', true);

        return $assortment;

    }


    /**
     * @param $products
     * @param string $settings
     * @param null $product
     * @return mixed
     * @throws Exception
     */
    public function add_assortment($products, $settings = '', $product = null)
    {
        $productName = isset($products['name']) ? $products['name'] : '(без имени)';
        $productType = isset($products['meta']['type']) ? $products['meta']['type'] : '(тип неизвестен)';
        $wcId = is_object($product) ? $product->get_id() : '(новый)';
        WmsLogs::set_logs('[add_assortment] Обрабатываем: "' . $productName . '" (тип=' . $productType . ', WC_ID=' . $wcId . ')', true);

        try {
            if ($products['meta']['type'] == 'product' or $products['meta']['type'] == 'service') {
                $assortment = new WmsProductApi($products, $this->settings, $product);
                $assortment->add();
            } elseif ($products['meta']['type'] == 'variant') {
                $assortment = new WmsProductVariantApi($products, $this->settings, $product);
                $assortment->add();
            } elseif ($products['meta']['type'] == 'bundle') {
                $assortment = new WmsBundleApi($products, $this->settings, $product);
                $assortment->add();
            } else {
                WmsLogs::set_logs('[add_assortment] Неизвестный тип товара: "' . $productType . '" — пропускаем', true);
            }

            do_action('wms_add_assortment_sync', $products, $this->settings, $product);
            unset($assortment);
        } catch (WC_Data_Exception $ex) {
            $this->error($ex, $products);
        } catch (Throwable $e) {
            $this->throwable($e, '', $products);
        }

        return $products;

    }


    /**
     * @param $ex
     * @param $products
     * @return string
     */
    public function error($ex, $products)
    {
        $sErrorMessage = '<<<Начало ';
        $sErrorMessage .= 'Товар в Мой Склад ';
        $sErrorMessage .= 'Название: ' . $products['name'] . ' ';
        $sErrorMessage .= isset($products['code']) ? 'Код: ' . $products['code'] . ' ' : '';
        $sErrorMessage .= isset($products['article']) ? 'Артикул: ' . $products['article'] . ' ' : '';
        $sErrorMessage .= 'Ошибка: ' . $ex->getMessage();


        $aErrorData = $ex->getErrorData();
        $aErrorCode = $ex->getErrorCode();

        $bDelete = true;

        if ($aErrorCode == 'product_invalid_sku') {
            $bDelete = false;
            $sErrorMessage .= ' Товар у которого найден похожий артикул ID ' . $aErrorData['resource_id'];
        }

        if (isset($aErrorData['resource_id']) and $aErrorData['resource_id'] > 0 and $bDelete) {
            $this->delete_meta($aErrorData['resource_id']);
            wp_trash_post($aErrorData['resource_id']);
        }

        $sErrorMessage .= ' Конец >>>';

        WmsLogs::set_logs($sErrorMessage, true);

        return $sErrorMessage;

    }


    /**
     * @param $ex
     * @param $article
     * @param $product
     * @return string
     */
    public function throwable($ex, $article, $product)
    {
        $sErrorMessage = '<<<Начало ';
        $sErrorMessage .= 'Товар';
        $sErrorMessage .= 'Артикул: ' . $article . ' ';
        $sErrorMessage .= 'Ошибка: ' . $ex->getMessage() . ' ' . PHP_EOL;
        $sErrorMessage .= 'Код: ' . $ex->getCode() . ' ' . PHP_EOL;
        $sErrorMessage .= 'Line: ' . $ex->getLine() . ' ' . PHP_EOL;
        $sErrorMessage .= 'File: ' . $ex->getFile() . ' ' . PHP_EOL;
        $sErrorMessage .= 'Trace: ' . $ex->getTraceAsString() . ' ' . PHP_EOL;


        if (is_object($product)) {
            //wp_trash_post($product->get_id());
        }

        $sErrorMessage .= ' Конец >>>';

        WmsLogs::set_logs($sErrorMessage, true);

        return $sErrorMessage;

    }


    /**
     * @param $id
     */
    protected function delete_meta($id)
    {
        $meta_data = array(
            '_id_ms',
            '_externalCode',
            '_ms_updated',
            '_ms_type',
            'ms_externalCode',
            'ms_article',
            'ms_code'
        );


        foreach ($meta_data as $key => $value) {
            delete_post_meta($id, $value);
        }

    }

    /**
     * @version  1.0.0
     */
    public function add_meta_box()
    {
        add_meta_box('wms_assortment', 'Мой Склад', array($this, 'meta_box_callback'), 'product', 'side', 'high');
    }


    /**
     * @param $meta
     * @param $post
     * @version  1.0.5
     *
     */
    public function meta_box_callback($post, $meta)
    {

        if (!isset($post) and $post->ID <= 0) {
            printf('<span>Товар не синхронизирован.</span>');
            return;
        }

        return $this->meta_box_message($post->ID);
    }


    /**
     * @param $columns
     *
     * @return array
     * @version  1.0.0
     *
     */
    public function wms_manage_edit_assortment_columns($columns)
    {
        $columns_after = array(
            'wms_assortment' => __("Мой Склад", 'wms'),
        );

        return array_merge($columns, $columns_after);
    }

    /**
     * @param $column
     * @version  1.0.0
     *
     */
    public function wms_manage_assortment_posts_custom_column($column)
    {
        global $post;

        if ($column == 'wms_assortment') {
            $this->meta_box_message($post->ID);
        }
    }

    /**
     * @param $loop
     * @param $variation_data
     * @param $variation
     */
    public function wms_manage_assortment_variation_posts_custom_column($loop, $variation_data, $variation)
    {
        ?>
        <p class="form-field variable_description0_field form-row form-row-full">
            <label for="variable_description0">Мой склад</label>
            <?php $this->meta_box_message($variation->ID); ?>
        </p>
        <?php

    }

    /**
     * @param $id
     */
    public function meta_box_message($id)
    {
        $assortment_ms_id = get_post_meta($id, '_id_ms', true);
        if (empty($assortment_ms_id) or $assortment_ms_id == false) {
            printf('<span ><i class="wdc-check wdc-d">X</i></span>');
            return;
        }
        $assortment_ms_uuid = get_post_meta($id, '_ms_uuid', true);

        if (empty($assortment_ms_uuid) or $assortment_ms_uuid == false) {
            $assortment_ms_uuid = '#';
        }

        $assortment = "<a target='_blank' href='" . $assortment_ms_uuid . "'>Посмотреть</a>";

        printf('<span ><i class="wdc-check wdc-a">✓</i></span>');
        printf('<div class="wdc-as">%s</div>', $assortment);
        return;
    }


}

$o = new WmsAssortmentController();
$o->init();
