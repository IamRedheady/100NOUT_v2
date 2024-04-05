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


        add_action('wms_walker_hook_assortment', array($this, 'sync'));
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
        sleep((float) (rand(0, 60) / 100) + rand(0, 6));

        $sStartTime = WmsWalkerFactory::get_walker('assortment')->get_start_walker();
        if ($sStartTime and is_numeric($sStartTime) and $sStartTime > 0) {
            if ((time() - $sStartTime) < (apply_filters('wms_assortment_start_time_checking_for_hang_ups', 3600 * 6))) {
                WmsLogs::set_logs('Уже идет синхронизация товаров', true);
                return;
            }
        }

        WmsWalkerFactory::get_walker('assortment')->delete_walker();
        WmsWalkerFactory::get_walker('assortment')->cron_init();
        WmsWalkerFactory::get_walker('assortment')->start_walker();

        WmsLogs::set_logs('Стартуем (синхронизация товаров)', true);
        update_option('wms_product_update_start_time', current_time("Y-m-d"));
        update_option('wms_product_update_start', array('load' => 'start', 'message' => 'Начало полной синхронизации...'));
        Queues::addAsync('sync_products_updates',[] , 'mswoo');
        wp_die();
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

        if($endHook){
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
        Queues::unscheduleAllActions('checking_for_products_updates', [], 'mswoo');
        Queues::addSingle((time() + (60 * 5)), 'checking_for_products_updates', [] , 'mswoo');

        do_action('wms_assortment_start_sync_loop');

        $offset = get_transient('wms_offset_assortment');
        $offset = $offset === false ? 0 : $offset;

        WmsWalkerFactory::get_walker('assortment')->start_loop($offset);

        $products = $this->get_assortment($offset);

        if ($aProducts = $this->get_products($products)) {

            $aProducts = $this->update_products($aProducts);

            if (!empty($aProducts)) {
                $this->create_products($aProducts);
            }

        }

        $count = count($products['rows']);

        if ($count < $this->limit or empty($products['rows'])) {

            Queues::unscheduleAllActions('checking_for_products_updates', [], 'mswoo');

            $count_product = $offset + $count;
            WmsLogs::set_logs('Синхронизация закончилась ' . $count_product . ' товаров было синхронизировано', true);
            update_option('wms_product_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Полная синхронизация'));

            WmsWalkerFactory::get_walker('assortment')->delete_walker();

            do_action('wms_assortment_end_sync');

            return ['ok'];
        }


        update_option('wms_product_update_start', array('size' => $products['meta']['size'], "count" => $offset, "time" => 0, 'load' => 'load'));
        WmsWalkerFactory::get_walker('assortment')->end_loop($offset + $this->limit, true);

        do_action('wms_assortment_end_sync_loop');

        if (isset($this->settings['wms_product_type_load']) and $this->settings['wms_product_type_load'] == 'speed') {

             wp_remote_get(
                RestRoute::getUrlRoute('v1/products/sync'),
                apply_filters(
                    'wms_ajax_wp_remote_get_config',
                    array('timeout' => 5, 'redirection' => 0, 'blocking' => false, 'sslverify' => false)
                )
            );

            return ['ok'];
        }

        return Queues::addAsync('sync_products_updates',[] , 'mswoo');

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
    protected function update_products($aProducts)
    {

        if ($aoProducts = wms_get_products_by_uuid_ms(array_keys($aProducts), $this->limit * 10)) {

            $aSearchFieldsName = [
                '_id_ms',
                '_externalCode',
                'ms_externalCode',
                'ms_article',
                'ms_code',
            ];


            foreach ($aoProducts as $oProduct) {

                if(!empty($oProduct->get_sku()) and isset($aProducts[$oProduct->get_sku()])){
                    $this->add_assortment($aProducts[$oProduct->get_sku()], $this->settings, $oProduct);
                    unset($aProducts[$oProduct->get_sku()]);
                    continue;
                }else{

                    foreach ($aSearchFieldsName as $sSearchFieldName) {

                        $sWCFieldName = wms_get_meta_by_object($oProduct, $sSearchFieldName);

                        if (!empty($sWCFieldName) and isset($aProducts[$sWCFieldName])) {
                            $this->add_assortment($aProducts[$sWCFieldName], $this->settings, $oProduct);
                            unset($aProducts[$sWCFieldName]);
                            continue;
                        }
                    }


                }

            }


        }

        return $aProducts;

    }


    /**
     * @param $aProducts
     * @return mixed
     * @throws Exception
     */
    protected function create_products($aProducts)
    {

        if (!empty($aProducts)) {
            foreach ($aProducts as $value) {
                $this->add_assortment($value);
            }

        }

        return $aProducts;

    }

    /**
     * @param $offset
     *
     * @return bool|mixed
     */
    private function get_assortment($offset)
    {
        $args = [
            'offset' => $offset,
            'limit' => $this->limit,
            'expand' => apply_filters('wms_get_assortment_url_expand', 'images,productFolder,country,components'),
        ];

        if (isset($this->settings['wms_product_variant_load']) and $this->settings['wms_product_variant_load'] == 'updated') {
            $updated = get_option('wms_product_update_start_time');
            if ($updated) {
                $args['filter'] = urlencode('updated>' . $updated);
            }
        } elseif (isset($this->settings['wms_product_variant_load']) and $this->settings['wms_product_variant_load'] == 'archived') {
            $args['filter'] = urlencode('archived=true');
        } else {
            $args['filter'] = urlencode('archived=false');
        }

        // $args = apply_filters('wms_get_assortment_url_args', $args);

        // $url = add_query_arg($args, apply_filters('wms_get_assortment_url', WMS_URL_API_V2 . '/entity/assortment/'));

        // $assortment = WmsConnectApi::get_instance()->send_request($url);

        // Готовая продукция (минус) – https://online.moysklad.ru/api/remap/1.2/entity/store/aad3dac1-ebd4-11ec-0a80-0c32001fe3b7
        // Независимости – https://online.moysklad.ru/api/remap/1.2/entity/store/1581bede-1ed4-11ec-0a80-06650037e169
        // Независимости сервис – https://online.moysklad.ru/api/remap/1.2/entity/store/6cc303d7-dde0-11ed-0a80-0830000fbf5c
        // Независимости приемка – https://api.moysklad.ru/api/remap/1.2/entity/store/476658d3-ab92-11ee-0a80-07a9004572e9
        // ТЦ Радиомаркет – https://api.moysklad.ru/api/remap/1.2/entity/store/fc9022b7-926b-11ed-0a80-071b000f2474
        // Кульман готовое – https://api.moysklad.ru/api/remap/1.2/entity/store/565324a1-cefc-11ee-0a80-14120034de0b

        // $url = add_query_arg($args, apply_filters('wms_get_assortment_url', WMS_URL_API_V2 . '/entity/assortment/'));
        // ////////
        
        $url = WMS_URL_API_V2 . '/entity/assortment/?offset='.$offset.'&limit='.$this->limit.'&expand=product,country,productFolder,components,images&filter=stockMode=positiveonly;stockStore=https://api.moysklad.ru/api/remap/1.2/entity/store/1581bede-1ed4-11ec-0a80-06650037e169;stockStore=https://api.moysklad.ru/api/remap/1.2/entity/store/6cc303d7-dde0-11ed-0a80-0830000fbf5c;stockStore=https://api.moysklad.ru/api/remap/1.2/entity/store/476658d3-ab92-11ee-0a80-07a9004572e9;stockStore=https://api.moysklad.ru/api/remap/1.2/entity/store/fc9022b7-926b-11ed-0a80-071b000f2474;stockStore=https://api.moysklad.ru/api/remap/1.2/entity/store/565324a1-cefc-11ee-0a80-14120034de0b;';

        WmsLogs::set_logs($url, true);
        
        $assortment = WmsConnectApi::get_instance()->send_request($url);
        // Услуги
        // $url = 'https://online.moysklad.ru//api//remap/1.2/entity/assortment/?filter=productFolder=https://online.moysklad.ru/api/remap/1.2/entity/productfolder/c7384a20-f390-11eb-0a80-054300328ab3;';
        // ////////
        //echo '<pre>';
        //print_r($products);
        if ($assortment === false) {
            WmsWalkerFactory::get_walker('assortment')->delete_walker();
            update_option('wms_product_update_start', array('time' => 'Ошибка'));
            wp_die();
        }

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
            }

            do_action('wms_add_assortment_sync', $products, $this->settings, $product);
            unset($assortment);
        } //Перехватываем (catch) исключение, если что-то идет не так.
        catch (WC_Data_Exception $ex) {
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