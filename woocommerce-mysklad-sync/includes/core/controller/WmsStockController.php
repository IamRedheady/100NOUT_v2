<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 28.01.2018
 * Time: 19:28
 */

//TODO:добавить возможность обновлять остатки товара из карточки
use WCSTORES\WC\MS\Queues\Queues;
use WCSTORES\WC\MS\Wordpress\Rest\RestRoute;


/**
 * Class WmsStockController
 */
class WmsStockController
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
     * @var string
     */
    private $type = 'stock';


    /**
     * @version 1.0.3
     * WmsAssortmentController constructor.
     */

    public function __construct()
    {

        $settings = get_option('wms_settings_stock');
        $this->set_settings($settings);

        $limit = 50;
        if (isset($this->settings['wms_stock_limit']) and $this->settings['wms_stock_limit'] > 5) {
            $limit = $this->settings['wms_stock_limit'];
        }
        $this->limit = apply_filters('wms_limit_stock', $limit);

    }


    /**
     *
     */
    public function init()
    {

        add_action('wms_walker_hook_stock', array($this, 'sync'));

        if (wp_doing_ajax()) {
            add_action('wp_ajax_wms-load-start-stock-syn', array($this, 'start_stock_syn'));
            add_action('wp_ajax_nopriv_wms-load-start-stockt-syn', array($this, 'start_stock_syn'));
        }

        add_action('wcstores_moysklad_queues_stock_synchronization_automatic', array($this, 'start_stock_syn'));
        add_action('wcstores_moysklad_queues_sync_stocks_updates', array($this, 'sync'));
        add_action('wcstores_moysklad_queues_checking_for_stocks_updates', array($this, 'sync'));
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
    public function stock_webhook($url, $action)
    {
        $stock = WmsConnectApi::get_instance()->send_request($url);

        if ($stock && isset($stock['positions']['meta']['href'])) {
            $this->stock_webhook_position($stock['positions']['meta']['href']);
        } else if ($stock && isset($stock['id']) && isset($stock['meta']['href'])) {
            $this->update_stock_product_by_href($stock['meta']['href'], $stock['meta']['type']);
        }

    }

    /**
     * @param $products
     * @return bool
     */
    public function update_stock_products_by_filter_href($products)
    {
        $count = count($products);

        $this->type = 'webhook_stock';

        if ($products) {

            $this->limit = $count + 10;

            foreach ($products as $product) {
                if ($product['type'] === 'product' || $product['type'] === 'variant') {
                    $this->update(0, "&filter={$product['type']}={$product['href']}");
                }
            }

            update_option('wms_stock_update_start', array('load' => 'stop', 'time' => date('d-m-Y H:i:s'), 'message' => 'Сработал вебхук'));

        }

        return true;


    }

    /**
     * @param $href
     * @param $type
     * @return bool
     */
    public function update_stock_product_by_href($href, $type)
    {
        return $this->update_stock_products_by_filter_href(array(
            array(
                'type' => $type,
                'href' => $href
            )
        ));

    }


    /**
     * @param $url
     * @return false
     * @throws Exception
     */
    private function stock_webhook_position($url)
    {

        $stock_position = WmsConnectApi::get_instance()->send_request($url);

        if ($stock_position === false) {
            update_option('wms_stock_update_start', array('time' => 'Ошибка'));
            return false;
        }

        if (isset($stock_position['rows']) && !empty($stock_position['rows'])) {

            $products = array();

            foreach ($stock_position['rows'] as $key => $position) {

                $products[] = array(
                    'type' => $position['assortment']['meta']['type'],
                    'href' => $position['assortment']['meta']['href']
                );

            }

            if ($products) {
                return $this->update_stock_products_by_filter_href($products);
            }
        }


    }


    /**
     * Стартуем синхрон остатков
     */
    public function start_stock_syn()
    {
        // Увеличиваем время выполнения PHP скрипта
        @set_time_limit(600);
        @ini_set('max_execution_time', 600);

        // // wmsLogs::set_logs('=== start_stock_syn() НАЧАЛО ===', true);
        // // wmsLogs::set_logs('Тип: ' . $this->type, true);

        // Проверяем блокировку синхронизации товаров
        if (!WmsHelper::can_start_stock_sync()) {
            WmsLogs::set_logs('Синхронизация остатков отменена: идет синхронизация товаров', true);
            return;
        }

        $sStartTime = WmsWalkerFactory::get_walker($this->type)->get_start_walker();
        if ($sStartTime and is_numeric($sStartTime) and $sStartTime > 0) {
            $timePassed = time() - $sStartTime;
            $maxTime = apply_filters('wms_stock_start_time_checking_for_hang_ups', 3600 * 6);
            // // wmsLogs::set_logs("Проверка времени: прошло {$timePassed} сек, максимум {$maxTime} сек", true);

            if ($timePassed < $maxTime) {
                // // wmsLogs::set_logs('Уже идет синхронизация остатков', true);
                return;
            }
        }

        WmsWalkerFactory::get_walker($this->type)->delete_walker();
        // // wmsLogs::set_logs('Walker удален', true);

        WmsWalkerFactory::get_walker($this->type)->cron_init();
        // // wmsLogs::set_logs('Walker инициализирован', true);

        WmsWalkerFactory::get_walker($this->type)->start_walker();
        // // wmsLogs::set_logs('Walker запущен', true);

        // Устанавливаем блокировку синхронизации остатков
        WmsHelper::set_sync_lock('stock');

        // // wmsLogs::set_logs('Стартуем(синхронизация остатков)', true);

        /////////

        /**
         * Получаем все остатки из МойСклад с автопагинацией
         */
        function get_all_stock($stores, $limit = 600)
        {
            wmsLogs::set_logs('=== get_all_stock() НАЧАЛО ===', true);
            wmsLogs::set_logs('Количество складов: ' . count($stores) . ', Limit: ' . $limit, true);

            $offset = 0;
            $all_rows = [];

            // Исправлено: заменена стрелочная функция на обычную для совместимости
            $filterStores = implode(";", array_map(function ($s) {
                return "store=" . $s;
            }, $stores));

            wmsLogs::set_logs('Фильтр складов сформирован', true);

            $iteration = 0;
            do {
                $iteration++;
                // wmsLogs::set_logs("Итерация #{$iteration}: offset={$offset}, limit={$limit}", true);

                $url = WMS_URL_API_V2 . "/report/stock/all?offset={$offset}&limit={$limit}&filter=stockMode=all;{$filterStores};";

                $requestStartTime = microtime(true);
                $response = WmsConnectApi::get_instance()->send_request($url);
                $requestEndTime = microtime(true);
                $requestTime = round($requestEndTime - $requestStartTime, 2);

                if ($response === false) {
                    wmsLogs::set_logs("ОШИБКА: Запрос вернул false на итерации #{$iteration}", true);
                    break;
                }

                if (!empty($response['rows'])) {
                    $rowsCount = count($response['rows']);
                    $all_rows = array_merge($all_rows, $response['rows']);
                    wmsLogs::set_logs("Получено строк: {$rowsCount}, Всего накоплено: " . count($all_rows) . ", Время запроса: {$requestTime} сек", true);
                } else {
                    wmsLogs::set_logs("Пустой ответ на итерации #{$iteration}", true);
                }

                $total = isset($response['meta']['size']) ? intval($response['meta']['size']) : 0;
                wmsLogs::set_logs("Всего товаров в МС: {$total}, Текущий offset: {$offset}", true);

                $offset += $limit;

            } while ($offset < $total);

            wmsLogs::set_logs('=== get_all_stock() ЗАВЕРШЕНО ===', true);
            wmsLogs::set_logs('Всего получено строк: ' . count($all_rows), true);

            return $all_rows;
        }

        /**
         * Получаем все товары из WooCommerce с автопагинацией
         */
        function get_all_wc_products($per_page = 500)
        {
            // wmsLogs::set_logs('=== get_all_wc_products() НАЧАЛО ===', true);
            // wmsLogs::set_logs('Per page: ' . $per_page, true);

            $paged = 1;
            $all_products = [];

            do {
                // wmsLogs::set_logs("Загрузка страницы #{$paged}", true);

                $args = [
                    'status' => 'publish',
                    'limit' => $per_page,
                    'page' => $paged,
                    'paginate' => true
                ];

                $queryStartTime = microtime(true);
                $products = wc_get_products($args);
                $queryEndTime = microtime(true);
                $queryTime = round($queryEndTime - $queryStartTime, 2);

                if (!empty($products->products)) {
                    $productsCount = count($products->products);
                    $all_products = array_merge($all_products, $products->products);
                    // wmsLogs::set_logs("Получено товаров: {$productsCount}, Всего накоплено: " . count($all_products) . ", Время запроса: {$queryTime} сек", true);
                } else {
                    // wmsLogs::set_logs("Пустой результат на странице #{$paged}", true);
                }

                $total = isset($products->total) ? $products->total : 0;
                // wmsLogs::set_logs("Всего товаров в WC: {$total}", true);

                $paged++;

            } while (count($all_products) < $total);

            // wmsLogs::set_logs('=== get_all_wc_products() ЗАВЕРШЕНО ===', true);
            // wmsLogs::set_logs('Всего получено товаров: ' . count($all_products), true);

            return $all_products;
        }

        // ---- Использование ----

        $stores = [
            "https://api.moysklad.ru/api/remap/1.2/entity/store/6d91a58f-5aa1-11f1-0a80-03be0012a19c",
            "https://api.moysklad.ru/api/remap/1.2/entity/store/6cc303d7-dde0-11ed-0a80-0830000fbf5c",
            "https://api.moysklad.ru/api/remap/1.2/entity/store/476658d3-ab92-11ee-0a80-07a9004572e9",
            "https://api.moysklad.ru/api/remap/1.2/entity/store/fc9022b7-926b-11ed-0a80-071b000f2474",
            "https://api.moysklad.ru/api/remap/1.2/entity/store/3e71ca84-9857-11ef-0a80-0b92001125ef",
            "https://api.moysklad.ru/api/remap/1.2/entity/store/1194b3f3-7b93-11ed-0a80-011100257ff5",
            "https://api.moysklad.ru/api/remap/1.2/entity/store/01de1406-8fa0-11ef-0a80-19b700267bd1",
            "https://api.moysklad.ru/api/remap/1.2/entity/store/365ed422-75f2-11f1-0a80-1c14001db9ce", // Боровая
            "https://api.moysklad.ru/api/remap/1.2/entity/store/fe67cd68-8f2a-11f1-0a80-1afc0031dfd6", // Черняховского
        ];

        WmsLogs::set_logs('=== Начало получения данных ===', true);
        $dataStartTime = microtime(true);

        $s_all_rows = get_all_stock($stores);
        $s_all_rows_code = array_column($s_all_rows, 'code');

        WmsLogs::set_logs('Получение товаров из WooCommerce...', true);
        $s_all_products = get_all_wc_products(500);

        $dataEndTime = microtime(true);
        $dataTime = round($dataEndTime - $dataStartTime, 2);

        WmsLogs::set_logs("К-во товаров в МС: " . count($s_all_rows), true);
        WmsLogs::set_logs("К-во товаров в WooCommerce: " . count($s_all_products), true);
        WmsLogs::set_logs("Время получения данных: {$dataTime} сек", true);

        // Скрываем отсутствующие товары
        WmsLogs::set_logs('=== Начало скрытия отсутствующих товаров ===', true);
        $hiddenCount = 0;
        $processedCount = 0;
        $totalProducts = count($s_all_products);

        foreach ($s_all_products as $s_product) {
            $sku = $s_product->get_sku();
            $productName = $s_product->get_name();

            if (!in_array($sku, $s_all_rows_code) && strpos($productName, 'ДСО') === false) {
                wp_update_post([
                    'ID' => $s_product->get_id(),
                    'post_status' => 'draft'
                ]);
                $hiddenCount++;
            }

            $processedCount++;
            // Логируем каждые 500 товаров
            if ($processedCount % 500 === 0) {
                WmsLogs::set_logs("Обработано {$processedCount}/{$totalProducts} товаров, скрыто: {$hiddenCount}", true);
            }
        }

        WmsLogs::set_logs("Скрыто товаров: {$hiddenCount}", true);
        WmsLogs::set_logs("=== Скрытие товаров завершено ===", true);

        // Обработка изображений - пропускаем если папки нет
        $images_dir = ABSPATH . 'images/';
        if (file_exists($images_dir)) {
            WmsLogs::set_logs("Начинаем загрузку изображений из папки images", true);

            $sku_folders = glob($images_dir . '*', GLOB_ONLYDIR);
            $imagesProcessed = 0;

            foreach ($sku_folders as $sku_folder) {
                $sku = basename($sku_folder);

                $product_id = wc_get_product_id_by_sku($sku);
                if (!$product_id) {
                    continue;
                }

                $product = wc_get_product($product_id);
                if (!$product) {
                    continue;
                }

                $image_files = glob($sku_folder . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
                if (empty($image_files)) {
                    continue;
                }

                // Удаляем старые изображения
                $old_image_id = $product->get_image_id();
                if ($old_image_id)
                    wp_delete_attachment($old_image_id, true);

                $old_gallery_ids = $product->get_gallery_image_ids();
                foreach ($old_gallery_ids as $old_id) {
                    wp_delete_attachment($old_id, true);
                }

                // Загружаем новые изображения
                $gallery_ids = [];

                foreach ($image_files as $index => $image_path) {
                    $filename = basename($image_path);
                    $upload_file = wp_upload_bits($filename, null, file_get_contents($image_path));

                    if (!$upload_file['error']) {
                        $wp_filetype = wp_check_filetype($filename, null);
                        $attachment = [
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => sanitize_file_name($filename),
                            'post_content' => '',
                            'post_status' => 'inherit',
                        ];

                        $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $product_id);
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
                        wp_update_attachment_metadata($attachment_id, $attach_data);

                        if ($index === 0) {
                            $product->set_image_id($attachment_id);
                        }

                        $gallery_ids[] = $attachment_id;
                        unlink($image_path);
                    }
                }

                if (!empty($gallery_ids)) {
                    $product->set_gallery_image_ids($gallery_ids);
                }

                $product->save();

                if (is_dir($sku_folder) && count(glob($sku_folder . '/*')) === 0) {
                    rmdir($sku_folder);
                }

                $imagesProcessed++;
            }

            WmsLogs::set_logs("Обработка изображений завершена. Обработано: {$imagesProcessed} товаров", true);
        } else {
            WmsLogs::set_logs("Папка images не найдена, пропускаем обработку изображений", true);
        }

        WmsLogs::set_logs("=== Запускаем синхронизацию остатков через очередь ===", true);

        update_option('wms_stock_update_start', array('load' => 'start', 'message' => 'Начало полной синхронизации...'));

        $queueResult = Queues::addAsync('sync_stocks_updates', [], 'mswoo');
        WmsLogs::set_logs("Очередь добавлена, результат: " . print_r($queueResult, true), true);

        wp_die();
    }


    /**
     * @param string $typeSync
     * @param string $parameter_url
     * @param bool $webhook
     * @return int|string[]|void
     */
    public function sync($typeSync = 'full', $parameter_url = '', $webhook = false)
    {
        // Увеличиваем время выполнения PHP скрипта
        @set_time_limit(60000);
        @ini_set('max_execution_time', 60000);

        // wmsLogs::set_logs('=== sync() НАЧАЛО ===', true);
        // wmsLogs::set_logs('Тип синхронизации: ' . $typeSync . ', Parameter URL: ' . $parameter_url . ', Webhook: ' . ($webhook ? 'true' : 'false'), true);

        // Проверяем блокировку синхронизации товаров
        if (!WmsHelper::can_start_stock_sync()) {
            WmsLogs::set_logs('Синхронизация остатков отменена: идет синхронизация товаров', true);
            return ['blocked'];
        }

        // Устанавливаем блокировку синхронизации остатков
        WmsHelper::set_sync_lock('stock');

        Queues::addSingle((time() + (60 * 5)), 'checking_for_stocks_updates', [], 'mswoo');

        if (isset($_REQUEST['type'])) {
            $this->type = $_REQUEST['type'];
            // wmsLogs::set_logs('Тип изменен из REQUEST: ' . $this->type, true);
        }

        // Читаем offset из option (более надежно) или из transient (для обратной совместимости)
        $option_key = 'wms_offset_' . $this->type;
        $offset = get_option($option_key, false);

        if ($offset === false) {
            // Если в option нет, читаем из transient
            $offset = get_transient('wms_offset_' . $this->type);
            // Если нашли в transient, сохраняем в option для надежности
            if ($offset !== false) {
                update_option($option_key, $offset);
            }
        }

        // Принудительно очищаем кэш перед чтением
        wp_cache_delete('wms_offset_' . $this->type, 'transient');
        wp_cache_delete($option_key, 'options');

        $offset = $offset === false ? 0 : intval($offset);

        // Проверяем, что offset действительно число
        if (!is_numeric($offset)) {
            WmsLogs::set_logs('ОШИБКА: offset не является числом: ' . var_export($offset, true) . ', сбрасываем на 0', true);
            $offset = 0;
            delete_option($option_key);
            delete_transient('wms_offset_' . $this->type);
        }

        WmsLogs::set_logs('=== sync() НАЧАЛО: offset=' . $offset . ', limit=' . $this->limit . ' (из option=' . get_option($option_key, 'не найден') . ', transient=' . var_export(get_transient('wms_offset_' . $this->type), true) . ')', true);

        WmsWalkerFactory::get_walker($this->type)->start_loop($offset);

        $updateStartTime = microtime(true);

        // ВАЖНО: Сначала получаем данные из API и СРАЗУ сохраняем новый offset
        // Это гарантирует, что даже если скрипт упадет во время обработки товаров,
        // следующий запуск начнется с правильного offset
        $stock = $this->get_stock($offset, $parameter_url);
        $count = isset($stock['rows']) ? count($stock['rows']) : 0;
        $size = isset($stock['meta']['size']) ? $stock['meta']['size'] : 0;
        $isProducts = !empty($stock['rows']);

        WmsLogs::set_logs('sync(): получено ' . $count . ' строк из API, всего ' . $size, true);

        // Проверяем, это последняя порция или нет
        $isLastBatch = ($count < $this->limit) || !$isProducts;

        if (!$isLastBatch) {
            // Сохраняем новый offset СРАЗУ, до обработки товаров
            $new_offset = $offset + $this->limit;
            update_option($option_key, $new_offset);
            set_transient('wms_offset_' . $this->type, $new_offset, WEEK_IN_SECONDS);
            WmsLogs::set_logs('sync(): новый offset=' . $new_offset . ' сохранен ДО обработки товаров', true);
        }

        // Теперь обрабатываем товары (даже если скрипт упадет, offset уже сохранен)
        $meta = $this->process_stock_data($stock);

        $updateEndTime = microtime(true);
        $updateTime = round($updateEndTime - $updateStartTime, 2);
        WmsLogs::set_logs('Обработка завершена за ' . $updateTime . ' сек. count=' . $count . ', size=' . $size . ', isProducts=' . ($isProducts ? 'true' : 'false'), true);

        Queues::unscheduleAllActions('checking_for_stocks_updates', [], 'mswoo');

        // Логируем условие для отладки
        $condition1 = $count < $this->limit;
        $condition2 = !$isProducts;
        WmsLogs::set_logs('Проверка условия завершения: count(' . $count . ') < limit(' . $this->limit . ') = ' . ($condition1 ? 'true' : 'false') . ', !isProducts(' . ($isProducts ? 'true' : 'false') . ') = ' . ($condition2 ? 'true' : 'false'), true);

        if ($condition1 || $condition2) {
            WmsLogs::set_logs('=== Синхронизация завершена (последняя порция) ===', true);

            $count_stock = $offset + $count;
            WmsLogs::set_logs('У ' . $count_stock . ' товаров остатки успешно загружены.', true);
            update_option('wms_stock_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Полная синхронизация'));

            WmsWalkerFactory::get_walker($this->type)->delete_walker();
            unset($this->walker);

            delete_transient('wc_low_stock_count');
            delete_transient('wc_outofstock_count');

            // Снимаем блокировку синхронизации остатков
            WmsHelper::remove_sync_lock('stock');

            do_action('wms_stock_end_sync');

            WmsLogs::set_logs('=== sync() ЗАВЕРШЕНО (конец синхронизации) ===', true);

            return ['ok'];

        }

        // Offset уже сохранён выше (до обработки товаров)
        // Просто вызываем end_loop для запуска следующей итерации
        $new_offset = $offset + $this->limit;
        WmsLogs::set_logs('Продолжаем синхронизацию: offset уже сохранён=' . $new_offset, true);

        WmsWalkerFactory::get_walker($this->type)->end_loop($new_offset, true);

        update_option('wms_stock_update_start', array('load' => 'load', 'size' => $size, "count" => $offset, "time" => 0));

        if (isset($this->settings['wms_stock_type_load']) and $this->settings['wms_stock_type_load'] == 'speed') {

            wp_remote_get(
                RestRoute::getUrlRoute('v1/stock/sync'),
                apply_filters(
                    'wms_ajax_wp_remote_get_config',
                    array('timeout' => 5, 'redirection' => 0, 'blocking' => false, 'sslverify' => false)
                )
            );

            return ['ok'];
        }

        return Queues::addAsync('sync_stocks_updates', [], 'mswoo');

    }

    /**
     * Обрабатывает уже полученные данные из API (без запроса)
     * @param array $stock Данные из API
     * @return array
     */
    protected function process_stock_data($stock)
    {
        if ($aProducts = $this->get_products($stock)) {
            $productsCount = count($aProducts);
            WmsLogs::set_logs('process_stock_data(): начинаем обработку ' . $productsCount . ' продуктов', true);

            $aProducts = $this->update_products($aProducts);

            $remainingCount = count($aProducts);
            WmsLogs::set_logs('process_stock_data(): после update_products осталось ' . $remainingCount . ' продуктов', true);

            $this->update_single_product($aProducts);

            WmsLogs::set_logs('process_stock_data(): обработка завершена', true);
        } else {
            WmsLogs::set_logs('process_stock_data(): нет продуктов для обработки', true);
        }

        return [
            'count' => isset($stock['rows']) ? count($stock['rows']) : 0,
            'size' => isset($stock['meta']['size']) ? $stock['meta']['size'] : 0,
            'isProducts' => !empty($stock['rows'])
        ];
    }

    /**
     * @param $offset
     * @param $parameter_url
     * @return array
     */
    protected function update($offset, $parameter_url)
    {
        WmsLogs::set_logs('=== update() НАЧАЛО: offset=' . $offset . ' ===', true);

        $stock = $this->get_stock($offset, $parameter_url);

        $count = isset($stock['rows']) ? count($stock['rows']) : 0;
        WmsLogs::set_logs('update(): получено ' . $count . ' строк из API', true);

        if ($aProducts = $this->get_products($stock)) {
            $productsCount = count($aProducts);
            WmsLogs::set_logs('update(): начинаем обработку ' . $productsCount . ' продуктов', true);

            $aProducts = $this->update_products($aProducts);

            $remainingCount = count($aProducts);
            WmsLogs::set_logs('update(): после update_products осталось ' . $remainingCount . ' продуктов', true);

            $this->update_single_product($aProducts);

            WmsLogs::set_logs('update(): update_single_product завершен', true);
        } else {
            WmsLogs::set_logs('update(): нет продуктов для обработки', true);
        }

        $result = [
            'count' => $count,
            'size' => (isset($stock['meta']['size'])) ? $stock['meta']['size'] : 0,
            'isProducts' => (empty($stock['rows'])) ? false : true
        ];

        WmsLogs::set_logs('=== update() ЗАВЕРШЕНО: count=' . $result['count'] . ', size=' . $result['size'] . ' ===', true);

        return $result;


    }


    /**
     * @param $products
     * @return array|null
     */
    protected function get_products($products)
    {
        $aProducts = [];

        if (!isset($products['rows'])) {
            return null;
        }


        if (empty($products['rows'])) {
            return null;
        }

        foreach ($products['rows'] as $product) {
            $sSearchFieldName = msw_get_assortment_search_fields($product, '_id_ms');
            $aProducts[$sSearchFieldName] = $product;
        }

        return $aProducts;

    }


    /**
     * @param $aProducts
     * @return mixed
     */
    protected function update_products($aProducts)
    {
        if ($aoProducts = wms_get_products_by_uuid_ms(array_keys($aProducts), $this->limit * 10)) {
            $processed = 0;
            $total = count($aoProducts);
            $startTime = microtime(true);

            foreach ($aoProducts as $oProduct) {
                $uuid = $oProduct->get_meta('_id_ms');
                if (!empty($uuid) and isset($aProducts[$uuid])) {
                    $this->update_stock($aProducts[$uuid], $oProduct);
                    unset($aProducts[$uuid]);
                    $processed++;

                    // Логируем каждые 20 товаров
                    if ($processed % 20 === 0) {
                        $elapsed = round(microtime(true) - $startTime, 1);
                        WmsLogs::set_logs('update_products: обработано ' . $processed . '/' . $total . ' за ' . $elapsed . ' сек', true);
                    }
                    continue;
                }
            }
            $totalTime = round(microtime(true) - $startTime, 1);
            WmsLogs::set_logs('update_products: завершено, обработано ' . $processed . ' товаров за ' . $totalTime . ' сек', true);
        }

        return $aProducts;

    }


    /**
     * @param $aProducts
     * @return mixed
     */
    protected function update_single_product($aProducts)
    {
        if (!empty($aProducts)) {
            foreach ($aProducts as $aProduct) {
                $this->update_stock($aProduct);
            }

        }

        return $aProducts;

    }


    /**
     * @param $offset
     *
     * @param string $parametr_url
     * @return bool|mixed
     * @throws Exception
     */
    private function get_stock($offset, $parametr_url = '')
    {
        // wmsLogs::set_logs('=== get_stock() НАЧАЛО ===', true);
        // wmsLogs::set_logs('Offset: ' . $offset . ', Limit: ' . $this->limit . ', Parameter URL: ' . $parametr_url, true);

        // Исправлено: убрана лишняя вложенность массива
        $args = [
            'offset' => $offset,
            'limit' => $this->limit,
            'filter' => 'stockMode=all'
        ];

        if (in_array('all', $this->settings['wms_stock_store'])) {
            $args['filter'] .= ';quantityMode=all';
            $url = WMS_URL_API_V2 . 'report/stock/all/';
        } else {
            $url = WMS_URL_API_V2 . 'report/stock/bystore/';
        }

        // $args = apply_filters('wms_get_stock_url_args', $args);
        // $url = apply_filters('wms_get_stock_url', add_query_arg($args, $url) . $parametr_url);

        // $stock = WmsConnectApi::get_instance()->send_request($url);

        // if (!$stock) {
        //     WmsWalkerFactory::get_walker($this->type)->delete_walker();
        //     update_option('wms_stock_update_start', array('time' => 'Ошибка'));
        //     wp_die();
        // }



        // return $stock;

        // if ($parametr_url != "") {
        //     $args = apply_filters('wms_get_stock_url_args', $args);
        //     $url = apply_filters('wms_get_stock_url', add_query_arg($args, $url) . $parametr_url);

        //     $stock = WmsConnectApi::get_instance()->send_request($url);

        //     if (!$stock) {
        //         WmsWalkerFactory::get_walker($this->type)->delete_walker();
        //         update_option('wms_stock_update_start', array('time' => 'Ошибка'));
        //         wp_die();
        //     }
        // } else {
        // $url = apply_filters('wms_get_stock_url', add_query_arg($args, $url) . $parametr_url);


        // Готовая продукция (минус) – https://online.moysklad.ru/api/remap/1.2/entity/store/aad3dac1-ebd4-11ec-0a80-0c32001fe3b7
        // Независимости – https://online.moysklad.ru/api/remap/1.2/entity/store/6d91a58f-5aa1-11f1-0a80-03be0012a19c
        // Независимости сервис – https://online.moysklad.ru/api/remap/1.2/entity/store/6cc303d7-dde0-11ed-0a80-0830000fbf5c
        // Независимости приемка – https://api.moysklad.ru/api/remap/1.2/entity/store/476658d3-ab92-11ee-0a80-07a9004572e9
        // ТЦ Радиомаркет – https://api.moysklad.ru/api/remap/1.2/entity/store/fc9022b7-926b-11ed-0a80-071b000f2474
        // Подзаказ Iphone https://api.moysklad.ru/api/remap/1.2/entity/store/3e71ca84-9857-11ef-0a80-0b92001125ef
        // Подзаказ Цифра https://api.moysklad.ru/api/remap/1.2/entity/store/1194b3f3-7b93-11ed-0a80-011100257ff5
        // ГП Кульман https://api.moysklad.ru/api/remap/1.2/entity/store/01de1406-8fa0-11ef-0a80-19b700267bd1
        // Боровая – https://api.moysklad.ru/api/remap/1.2/entity/store/365ed422-75f2-11f1-0a80-1c14001db9ce
        // Черняховского – https://api.moysklad.ru/api/remap/1.2/entity/store/fe67cd68-8f2a-11f1-0a80-1afc0031dfd6


        // ////////
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
            return 'store=' . $store;
        }, $stores);

        $filter = 'stockMode=all;' . implode(';', $storeFilters);

        $url = WMS_URL_API_V2 . '/report/stock/all?offset=' . $offset . '&limit=' . $this->limit . '&filter=' . urlencode($filter);
        // ////////

        // wmsLogs::set_logs('URL запроса: ' . $url, true);

        $requestStartTime = microtime(true);
        $stock = WmsConnectApi::get_instance()->send_request($url);
        $requestEndTime = microtime(true);
        $requestTime = round($requestEndTime - $requestStartTime, 2);

        // wmsLogs::set_logs('Время выполнения запроса: ' . $requestTime . ' сек', true);

        if ($stock === false) {
            // wmsLogs::set_logs('=== ОШИБКА: Запрос вернул false ===', true);
            WmsWalkerFactory::get_walker($this->type)->delete_walker();
            update_option('wms_stock_update_start', array('time' => 'Ошибка'));
            wp_die();
        }

        $rowsCount = isset($stock['rows']) ? count($stock['rows']) : 0;
        $totalSize = isset($stock['meta']['size']) ? $stock['meta']['size'] : 0;

        // wmsLogs::set_logs('Получено строк: ' . $rowsCount . ', Всего (meta.size): ' . $totalSize, true);
        // wmsLogs::set_logs('=== get_stock() ЗАВЕРШЕНО ===', true);

        return $stock;

    }


    /**
     *
     * @param $stock
     *
     * @param null $product
     * @return bool
     * @version 1.0.4
     */
    private function update_stock($stock, $product = null)
    {
        try {
            $productId = $product ? $product->get_id() : 'null';
            $productSku = $product ? $product->get_sku() : 'null';

            // wmsLogs::set_logs("update_stock() для товара ID: {$productId}, SKU: {$productSku}", true);

            $stock = apply_filters('wms_stock_filter_controller', $stock, $this->settings);

            if ($stock === false) {
                // wmsLogs::set_logs('Фильтр вернул false, пропускаем товар', true);
                return false;
            }

            $stocks = new WmsStockApi($this->settings, $product);

            if (in_array('all', $this->settings['wms_stock_store'])) {
                // wmsLogs::set_logs('Обновляем остатки для всех складов', true);
                $stocks->update_stock_all($stock);
            } else {
                // wmsLogs::set_logs('Обновляем остатки по складам', true);
                $stocks->update_stock_bystore($stock);
            }

            unset($stocks);

            // wmsLogs::set_logs("Остатки успешно обновлены для товара ID: {$productId}", true);

        } //Перехватываем (catch) исключение, если что-то идет не так.
        catch (Exception $ex) {
            // wmsLogs::set_logs('ОШИБКА в update_stock(): ' . $ex->getMessage(), true);
            // wmsLogs::set_logs('Файл: ' . $ex->getFile() . ', Строка: ' . $ex->getLine(), true);

        }
        return true;

    }

}

$o = new WmsStockController();
$o->init();