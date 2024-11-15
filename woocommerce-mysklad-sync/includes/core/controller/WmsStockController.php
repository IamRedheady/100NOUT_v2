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
                $this->update(0, "&filter={$product['type']}={$product['href']}");
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

        $sStartTime = WmsWalkerFactory::get_walker($this->type)->get_start_walker();
        if ($sStartTime and is_numeric($sStartTime) and $sStartTime > 0) {
            if ((time() - $sStartTime) < (apply_filters('wms_stock_start_time_checking_for_hang_ups', 3600 * 6))) {
                WmsLogs::set_logs('Уже идет синхронизация остатков', true);
                return;
            }
        }

        WmsWalkerFactory::get_walker($this->type)->delete_walker();
        WmsWalkerFactory::get_walker($this->type)->cron_init();
        WmsWalkerFactory::get_walker($this->type)->start_walker();

        WmsLogs::set_logs('Стартуем(синхронизация остатков)', true);

        // ////////

        $s_offset = 0;
        $s_limit = 1000;

        // Готовая продукция (минус) – https://online.moysklad.ru/api/remap/1.2/entity/store/aad3dac1-ebd4-11ec-0a80-0c32001fe3b7
        // Независимости – https://online.moysklad.ru/api/remap/1.2/entity/store/1581bede-1ed4-11ec-0a80-06650037e169
        // Независимости сервис – https://online.moysklad.ru/api/remap/1.2/entity/store/6cc303d7-dde0-11ed-0a80-0830000fbf5c
        // Независимости приемка – https://api.moysklad.ru/api/remap/1.2/entity/store/476658d3-ab92-11ee-0a80-07a9004572e9
        // ТЦ Радиомаркет – https://api.moysklad.ru/api/remap/1.2/entity/store/fc9022b7-926b-11ed-0a80-071b000f2474
        // Подзаказ Iphone https://api.moysklad.ru/api/remap/1.2/entity/store/3e71ca84-9857-11ef-0a80-0b92001125ef
        // Подзаказ Цифра https://api.moysklad.ru/api/remap/1.2/entity/store/1194b3f3-7b93-11ed-0a80-011100257ff5
        // ГП Кульман https://api.moysklad.ru/api/remap/1.2/entity/store/01de1406-8fa0-11ef-0a80-19b700267bd1


        $pre_url = WMS_URL_API_V2 . '/report/stock/all?offset='.$s_offset.'&limit='.$s_limit.'&filter=stockMode=all;store=https://api.moysklad.ru/api/remap/1.2/entity/store/1581bede-1ed4-11ec-0a80-06650037e169;store=https://api.moysklad.ru/api/remap/1.2/entity/store/6cc303d7-dde0-11ed-0a80-0830000fbf5c;store=https://api.moysklad.ru/api/remap/1.2/entity/store/476658d3-ab92-11ee-0a80-07a9004572e9;store=https://api.moysklad.ru/api/remap/1.2/entity/store/fc9022b7-926b-11ed-0a80-071b000f2474;store=https://api.moysklad.ru/api/remap/1.2/entity/store/3e71ca84-9857-11ef-0a80-0b92001125ef;store=https://api.moysklad.ru/api/remap/1.2/entity/store/1194b3f3-7b93-11ed-0a80-011100257ff5;store=https://api.moysklad.ru/api/remap/1.2/entity/store/01de1406-8fa0-11ef-0a80-19b700267bd1;';

        $pre_stock = WmsConnectApi::get_instance()->send_request($pre_url);

        $s_rows = $pre_stock['rows'];
        WmsLogs::set_logs("К-во товаров ВСЕГО " . $pre_stock['meta']['size'], true);

        $finaly_rows = array();

        if (count($s_rows) < intval($pre_stock['meta']['size'])) {
            // WmsLogs::set_logs("Попали куда надо", true);
            $s_offset = 1000;

            $s_pre_url = WMS_URL_API_V2 . '/report/stock/all?offset='.$s_offset.'&limit='.$s_limit.'&filter=stockMode=all;store=https://api.moysklad.ru/api/remap/1.2/entity/store/1581bede-1ed4-11ec-0a80-06650037e169;store=https://api.moysklad.ru/api/remap/1.2/entity/store/6cc303d7-dde0-11ed-0a80-0830000fbf5c;store=https://api.moysklad.ru/api/remap/1.2/entity/store/476658d3-ab92-11ee-0a80-07a9004572e9;store=https://api.moysklad.ru/api/remap/1.2/entity/store/fc9022b7-926b-11ed-0a80-071b000f2474;store=https://api.moysklad.ru/api/remap/1.2/entity/store/3e71ca84-9857-11ef-0a80-0b92001125ef;store=https://api.moysklad.ru/api/remap/1.2/entity/store/1194b3f3-7b93-11ed-0a80-011100257ff5;store=https://api.moysklad.ru/api/remap/1.2/entity/store/01de1406-8fa0-11ef-0a80-19b700267bd1;';

            $s_pre_stock = WmsConnectApi::get_instance()->send_request($s_pre_url);

            $finaly_rows = $s_pre_stock['rows'];

            if (2000 < intval($pre_stock['meta']['size'])) {
                // WmsLogs::set_logs("Попали куда надо", true);
                $s_offset = 2000;
    
                $s_pre_url = WMS_URL_API_V2 . '/report/stock/all?offset='.$s_offset.'&limit='.$s_limit.'&filter=stockMode=all;store=https://api.moysklad.ru/api/remap/1.2/entity/store/1581bede-1ed4-11ec-0a80-06650037e169;store=https://api.moysklad.ru/api/remap/1.2/entity/store/6cc303d7-dde0-11ed-0a80-0830000fbf5c;store=https://api.moysklad.ru/api/remap/1.2/entity/store/476658d3-ab92-11ee-0a80-07a9004572e9;store=https://api.moysklad.ru/api/remap/1.2/entity/store/fc9022b7-926b-11ed-0a80-071b000f2474;store=https://api.moysklad.ru/api/remap/1.2/entity/store/3e71ca84-9857-11ef-0a80-0b92001125ef;store=https://api.moysklad.ru/api/remap/1.2/entity/store/1194b3f3-7b93-11ed-0a80-011100257ff5;store=https://api.moysklad.ru/api/remap/1.2/entity/store/01de1406-8fa0-11ef-0a80-19b700267bd1;';
    
                $ss_pre_stock = WmsConnectApi::get_instance()->send_request($s_pre_url);
    
                $s_finaly_rows = $ss_pre_stock['rows'];

                $finaly_rows = array_merge($s_finaly_rows, $finaly_rows);
            }
        }

        // WmsLogs::set_logs(count($finaly_rows)." 2 ссылка", true);

        $s_all_rows = array_merge($s_rows, $finaly_rows);

        $s_all_rows_code = array_column($s_all_rows, 'code');

        WmsLogs::set_logs(count($s_all_rows)." ВСЕГО", true);
        // WmsLogs::set_logs(count($s_all_rows_code)." длина нового массива " . $s_all_rows_code[0] . " Первый элемент", true);

        $s_args_all = array(
            'status' => 'publish',
            'limit' => 2000
        );

        $s_all_products = wc_get_products($s_args_all);
        
        if (count($s_all_rows) > 0) {
        	foreach ($s_all_products as $s_product) {
                if (in_array($s_product->get_sku(), $s_all_rows_code) == false and strpos($s_product->get_name(), 'ДСО') == false) {
                    WmsLogs::set_logs("Товар ".$s_product->get_name()." не найден в выборке МС. Скрываем товар " .$s_product->get_sku(), true);

                    wp_update_post(
                        array( 
                            'ID' => $s_product->get_id(), 
                            'post_status' => 'draft'
                        )
                    );
                }
            }

        	WmsLogs::set_logs("Предварительная проверка завершена", true);
        }

        // ////////
        update_option('wms_stock_update_start', array('load' => 'start', 'message' => 'Начало полной синхронизации...'));

        wp_die(Queues::addAsync('sync_stocks_updates', [], 'mswoo'));
    }


    /**
     * @param string $typeSync
     * @param string $parameter_url
     * @param bool $webhook
     * @return int|string[]|void
     */
    public function sync($typeSync = 'full', $parameter_url = '', $webhook = false)
    {
        Queues::addSingle((time() + (60 * 5)), 'checking_for_stocks_updates', [], 'mswoo');

        if (isset($_REQUEST['type'])) {
            $this->type = $_REQUEST['type'];
        }

        $offset = get_transient('wms_offset_' . $this->type);
        $offset = $offset === false ? 0 : $offset;

        WmsWalkerFactory::get_walker($this->type)->start_loop($offset);

        $meta = $this->update($offset, $parameter_url);

        Queues::unscheduleAllActions('checking_for_stocks_updates', [], 'mswoo');


        if ($meta['count'] < $this->limit || !$meta['isProducts']) {

            $count_stock = $offset + $meta['count'];
            WmsLogs::set_logs('У ' . $count_stock . ' товаров остатки успешно загружены.', true);
            update_option('wms_stock_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Полная синхронизация'));

            WmsWalkerFactory::get_walker($this->type)->delete_walker();
            unset($this->walker);

            delete_transient('wc_low_stock_count');
            delete_transient('wc_outofstock_count');

            do_action('wms_stock_end_sync');

            return ['ok'];

        }

        WmsWalkerFactory::get_walker($this->type)->end_loop($offset + $this->limit, true);
        update_option('wms_stock_update_start', array('load' => 'load', 'size' => $meta['size'], "count" => $offset, "time" => 0));

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
     * @param $offset
     * @param $parameter_url
     * @return array
     */
    protected function update($offset, $parameter_url)
    {

        $stock = $this->get_stock($offset, $parameter_url);

        $count = count($stock['rows']);

        if ($aProducts = $this->get_products($stock)) {
            $aProducts = $this->update_products($aProducts);

            $this->update_single_product($aProducts);
        }

        return [
            'count' => $count,
            'size' => (isset($stock['meta']['size'])) ? $stock['meta']['size'] : 0,
            'isProducts' => (empty($stock['rows'])) ? false : true
        ];


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
            foreach ($aoProducts as $oProduct) {
                $uuid = $oProduct->get_meta('_id_ms');
                if (!empty($uuid) and isset($aProducts[$uuid])) {
                    $this->update_stock($aProducts[$uuid], $oProduct);
                    unset($aProducts[$uuid]);
                    continue;
                }
            }
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
     */
    private function get_stock($offset, $parametr_url = '')
    {
        $args = apply_filters('wms_get_stock_url_args', [
            'offset' => $offset,
            'limit' => $this->limit,
            'filter' => 'stockMode=all'
        ]);

        if (in_array('all', $this->settings['wms_stock_store'])) {
            $args['filter'] .= ';quantityMode=all';
            $url = WMS_URL_API_V2 . 'report/stock/all/';
        } else {
            $url = WMS_URL_API_V2 . 'report/stock/bystore/';
        }

        
        if ($parametr_url != "") {
            $args = apply_filters('wms_get_stock_url_args', $args);
            $url = apply_filters('wms_get_stock_url', add_query_arg($args, $url) . $parametr_url);

            $stock = WmsConnectApi::get_instance()->send_request($url);

            if (!$stock) {
                WmsWalkerFactory::get_walker($this->type)->delete_walker();
                update_option('wms_stock_update_start', array('time' => 'Ошибка'));
                wp_die();
            }
        } else {
            // $url = apply_filters('wms_get_stock_url', add_query_arg($args, $url) . $parametr_url);


            // Готовая продукция (минус) – https://online.moysklad.ru/api/remap/1.2/entity/store/aad3dac1-ebd4-11ec-0a80-0c32001fe3b7
            // Независимости – https://online.moysklad.ru/api/remap/1.2/entity/store/1581bede-1ed4-11ec-0a80-06650037e169
            // Независимости сервис – https://online.moysklad.ru/api/remap/1.2/entity/store/6cc303d7-dde0-11ed-0a80-0830000fbf5c
            // Независимости приемка – https://api.moysklad.ru/api/remap/1.2/entity/store/476658d3-ab92-11ee-0a80-07a9004572e9
            // ТЦ Радиомаркет – https://api.moysklad.ru/api/remap/1.2/entity/store/fc9022b7-926b-11ed-0a80-071b000f2474
            // Подзаказ Iphone https://api.moysklad.ru/api/remap/1.2/entity/store/3e71ca84-9857-11ef-0a80-0b92001125ef
            // Подзаказ Цифра https://api.moysklad.ru/api/remap/1.2/entity/store/1194b3f3-7b93-11ed-0a80-011100257ff5
            // ГП Кульман https://api.moysklad.ru/api/remap/1.2/entity/store/01de1406-8fa0-11ef-0a80-19b700267bd1


            // ////////
            $url = WMS_URL_API_V2 . '/report/stock/all?offset='.$offset.'&limit='.$this->limit.'&filter=stockMode=all;store=https://api.moysklad.ru/api/remap/1.2/entity/store/1581bede-1ed4-11ec-0a80-06650037e169;store=https://api.moysklad.ru/api/remap/1.2/entity/store/6cc303d7-dde0-11ed-0a80-0830000fbf5c;store=https://api.moysklad.ru/api/remap/1.2/entity/store/476658d3-ab92-11ee-0a80-07a9004572e9;store=https://api.moysklad.ru/api/remap/1.2/entity/store/fc9022b7-926b-11ed-0a80-071b000f2474;store=https://api.moysklad.ru/api/remap/1.2/entity/store/3e71ca84-9857-11ef-0a80-0b92001125ef;store=https://api.moysklad.ru/api/remap/1.2/entity/store/1194b3f3-7b93-11ed-0a80-011100257ff5;store=https://api.moysklad.ru/api/remap/1.2/entity/store/01de1406-8fa0-11ef-0a80-19b700267bd1;';
            // ////////

            $stock = WmsConnectApi::get_instance()->send_request($url);
            
            if ($stock === false) {
                WmsWalkerFactory::get_walker($this->type)->delete_walker();
                update_option('wms_stock_update_start', array('time' => 'Ошибка'));
                wp_die();
            }
    
            return $stock;
        }

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

            $stock = apply_filters('wms_stock_filter_controller', $stock, $this->settings);

            if ($stock === false) {
                return false;
            }

            $stocks = new WmsStockApi($this->settings, $product);

            if (in_array('all', $this->settings['wms_stock_store'])) {
                $stocks->update_stock_all($stock);
            } else {
                $stocks->update_stock_bystore($stock);
            }

            unset($stocks);

        } //Перехватываем (catch) исключение, если что-то идет не так.
        catch (Exception $ex) {
            WmsLogs::set_logs($ex->getMessage(), true);

        }
        return true;

    }

}

$o = new  WmsStockController();
$o->init();