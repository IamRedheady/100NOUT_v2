<?php


/**
 * Class WmsStockApi
 */
class WmsStockApi
{
    /**
     * @var
     */
    private $settings;

    /**
     * @var mixed|null
     */
    private $product;


    /**
     * WmsStockApi constructor.
     *
     * @param null $aSettings
     * @param null $oProduct
     */
    public function __construct($aSettings = null, $oProduct = null)
    {
        $this->set_settings($aSettings);

        if ($this->settings == null) {
            $aSettings = get_option('wms_settings_product');
            $this->set_settings($aSettings);
        }

        $this->product = $oProduct;
    }

    /**
     * @return mixed
     */
    public function get_settings()
    {
        return $this->settings;
    }


    /**
     * @return mixed|void
     */
    public function set_settings($settings)
    {
        $this->settings = $settings;
    }


    /**
     * @param $stock
     * @return bool|int|string
     */
    private function get_assortment_id_stock($stock)
    {
        return wms_get_product_id('_id_ms', WmsHelper::get_id_ms_explode($stock['meta']['href']));
    }


    /**
     * @param $product_id
     * @param $stock
     * @param int $quantity
     *
     * @return bool|int
     */
    public function update_stock($product_id, $stock, $quantity = 0)
    {
        if ($quantity < 0) {
            $quantity = 0;
        }

        do_action('wms_before_stock_update_action', $product_id, $stock, $this->settings, $quantity);

        $product = ($this->product) ? $this->product : wc_get_product($product_id);

        if (!is_object($product)) {
            WmsLogs::set_logs('Не удалось получить товар  id ' . $product_id, true);
            return false;
        }

        $quantity = apply_filters('wms_stock_quantity_update', $quantity, $product, $this->settings, $this);

        if (!$this->is_update_stock($product, $quantity)) {
            return false;
        }


        if (isset($this->settings['wms_stock_type_status']) and $this->settings['wms_stock_type_status'] === 'stock_status') {
            $product->set_manage_stock('no');
        } else {

            if ($product->is_type('variable') and $quantity <= 0) {
                $product->set_manage_stock('no');
            } else {
                $product->set_manage_stock('yes');
            }

            $product->set_stock_quantity($quantity);
        }

        if ($quantity <= 0) {
            $product->set_stock_status('outofstock');
        } else {
            $product->set_stock_status('instock');
        }

        wp_cache_delete($product_id, 'post_meta');
        delete_transient('wc_product_children_' . ($product->is_type('variation') ? $product->get_parent_id() : $product->get_id()));
        wp_cache_delete('product-' . $product_id, 'products');

        $product->set_date_modified(current_time('timestamp', true));

        $product = apply_filters('wms_stock_update_filter', $product, $stock, $this->settings, $quantity);
        $product->save();

        do_action('wms_stock_update_action', $product->get_id(), $stock, $this->settings, $quantity);

        return $product->get_id();


    }

    /**
     * @param $product
     * @param $quantity
     * @return bool|null|string
     */
    public function is_update_stock($product, $quantity)
    {
        $stockStatusCurrent = $product->get_stock_status();
        $quantityCurrent = $product->get_stock_quantity();

        if ($quantity <= 0 and $product->is_type('variable') and $product->get_manage_stock()) {
            // WmsLogs::set_logs('У вариативных товаров родители не должны управлять остатками ' . $product->get_id(), true);
            return true;
        }

        if ($quantity != $quantityCurrent) {
            // WmsLogs::set_logs('Остатки не совпадают ' . $product->get_id() . ' текуший остаток ' . $quantityCurrent . ' новый ' . $quantity, true);
            return true;
        }

        if ($quantity <= 0 and $stockStatusCurrent == 'instock' and !$product->is_type('variable')) {
            // WmsLogs::set_logs('на остатки ноль но в наличии ' . $product->get_id(), true);
            return true;
        }

        if ($quantity > 0 and $stockStatusCurrent == 'outofstock') {
            // WmsLogs::set_logs('на остатки больше 0 но нет в наличии ' . $product->get_id(), true);
            return true;
        }

        //WmsLogs::set_logs('смысла нет менять ' . $product->get_id(), true);

        return false;
    }

    /**
     * @param $id
     * @return bool|null|string
     */
    public function get_meta($id)
    {
        if (!is_numeric($id)) {
            return false;
        }

        global $wpdb;
        $value =
            $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT meta_key, meta_value 
                        FROM $wpdb->postmeta 
                        WHERE post_id = %s 
                        AND meta_key IN('_stock', '_stock_status')",
                    $id
                ),
                OBJECT_K
            );


        if (isset($value) and !empty($value)) {
            return $value;
        }

        return false;
    }


    /**
     * @param $product_id
     * @param $stocks
     * @return int
     */
    protected function get_stock_quantity_bystore($product_id, $stocks)
    {

        $quantity = 0;

        $stocks = apply_filters('wms_get_stock_quantity_bystore', $stocks['stockByStore'], $product_id, $this->settings);

        foreach ($stocks as $stock) {

            $store_id = (explode('/', $stock['meta']['href']));
            $store_id = array_pop($store_id);

            if (in_array($store_id, $this->settings['wms_stock_store'])) {

                $quantity += apply_filters('wms_stock_quantity_filter', (float) $stock['stock'] - (float) $stock['reserve'], $stock, $product_id, $this->settings);

                do_action('wms_stock_bystore_action', $product_id, $store_id, $stock, $this->settings, $quantity);
            }

        }

        return (float) $quantity;

    }


    /**
     * @param $stock
     *
     * @return bool
     */
    public function update_stock_all($stock)
    {

        if (!$product_id = $this->get_product_id($stock)) {
            return false;
        }

        // ////////
        // WmsLogs::set_logs("Начало обработки товара ID: " . $product_id, true);

        $pmsid = get_post_meta($product_id, '_id_ms', true);
        // WmsLogs::set_logs("Получен ID МойСклад: " . $pmsid, true);

        $s_link = 'https://api.moysklad.ru/api/remap/1.2/report/stock/bystore?filter=product=https://api.moysklad.ru/api/remap/1.2/entity/product/' . $pmsid . ';';
        // WmsLogs::set_logs("Сформирован URL для запроса остатков: " . $s_link, true);

        $s_stock = WmsConnectApi::get_instance()->send_request($s_link);
        // WmsLogs::set_logs("Получен ответ от API МойСклад", true);

        // sleep(1);
        // WmsLogs::set_logs("Пауза 0 секунд проверка товара на склад", true);

        $s_stocks = $s_stock['rows'][0]['stockByStore'];
        // WmsLogs::set_logs("Извлечены данные по складам из ответа API", true);

        $s_product = wc_get_product($product_id);
        // WmsLogs::set_logs("Получен объект товара WooCommerce", true);

        $s_status = get_post_status($product_id);
        // WmsLogs::set_logs("Получен статус поста: " . $s_status, true);

        $checkDraft = false;
        $checkInStock = false;
        $checkVmagazine = true;
        $checkPodZakaz = true;
        WmsLogs::set_logs("Инициализированы переменные проверки: checkDraft=false, checkInStock=false, checkVmagazine=true, checkPodZakaz=true", true);
        // 
        foreach ($s_stocks as $s_istock) {
            // WmsLogs::set_logs("Обработка склада: " . $s_istock['name'] . " с остатком: " . $s_istock['stock'], true);

            $storeHref = '';
            if (!empty($s_istock['meta']['href'])) {
                $storeHref = $s_istock['meta']['href'];
            }
            $storeId = $storeHref ? basename((string) parse_url($storeHref, PHP_URL_PATH)) : '';
            $storeName = isset($s_istock['name']) ? $s_istock['name'] : '';

            if ($storeName == "материальный, нез." or $storeName == "Независимости приёмка" or $storeId == "476658d3-ab92-11ee-0a80-07a9004572e9") {
                // WmsLogs::set_logs("Проверка склада 'материальный, нез.' или 'Независимости приёмка'", true);
                if ($s_istock['stock'] != 0) {
                    $checkDraft = true;
                    // WmsLogs::set_logs("Установлен флаг checkDraft=true для склада: " . $s_istock['name'], true);
                }
            }

            // Независимости 94 — https://online.moysklad.ru/app/#Warehouse/edit?id=6d91a58f-5aa1-11f1-0a80-03be0012a19c
            if ($storeId == "6d91a58f-5aa1-11f1-0a80-03be0012a19c" or $storeName == "Независимости" or $storeName == "Независимости 94") {
                WmsLogs::set_logs("Проверка склада 'Независимости' с остатком: " . $s_istock['stock'], true);
                // Убрано условие get_stock_quantity() - проверяем только остаток в МойСклад и цену
                if ($s_istock['stock'] != 0 and $s_istock['stock'] != "0" and $s_product->get_price() > 0) {
                    $checkVmagazine = $s_istock['stock'];
                    $checkInStock = true;
                    WmsLogs::set_logs("Установлен checkInStock=true, checkVmagazine=" . $s_istock['stock'] . " для склада 'Независимости'", true);
                }
            }

            if ($s_istock['name'] == "iPhone") {
                WmsLogs::set_logs("Проверка склада 'iPhone' с остатком: " . $s_istock['stock'], true);
                // Убрано условие get_stock_quantity() - проверяем только остаток в МойСклад и цену
                if ($s_istock['stock'] != 0 and $s_istock['stock'] != "0" and $s_product->get_price() > 0) {
                    $checkPodZakaz = $s_istock['stock'];
                    $checkInStock = true;
                    WmsLogs::set_logs("Установлен checkInStock=true, checkPodZakaz=" . $s_istock['stock'] . " для склада 'iPhone'", true);
                }
            }
        }

        WmsLogs::set_logs("Итоговые флаги: checkDraft=" . ($checkDraft ? 'true' : 'false') . ", checkInStock=" . ($checkInStock ? 'true' : 'false') . ", checkVmagazine=" . $checkVmagazine . ", checkPodZakaz=" . $checkPodZakaz, true);

        if ($checkDraft == true and $checkInStock == false) {
            // WmsLogs::set_logs("Условие: товар на материальном складе, но не в наличии. Переводим в черновик", true);
            wp_update_post(
                array(
                    'ID' => $s_product->get_id(),
                    'post_status' => 'draft'
                )
            );
            // WmsLogs::set_logs("Товар переведен в статус 'draft'", true);

            // WmsLogs::set_logs($s_product->get_sku() . " находится на складе материальный, нез. Скрываем до следующей синхронизации.", true);
        } else {
            // WmsLogs::set_logs("Условие: товар не на материальном складе или есть в наличии. Публикуем товар", true);
            wp_update_post(
                array(
                    'ID' => $s_product->get_id(),
                    'post_status' => 'publish'
                )
            );
            WmsLogs::set_logs("Товар переведен в статус 'publish'", true);

            if ($checkVmagazine !== true) {
                update_post_meta($product_id, 'v_magazine', $checkVmagazine);
                // WmsLogs::set_logs("Обновлено поле v_magazine = " . $checkVmagazine, true);
            } else {
                update_post_meta($product_id, 'v_magazine', 0);
                // WmsLogs::set_logs("Установлено поле v_magazine = 0", true);
            }

            if ($checkPodZakaz !== true) {
                update_post_meta($product_id, 'pod_zakaz', $checkPodZakaz);
                // WmsLogs::set_logs("Обновлено поле pod_zakaz = " . $checkPodZakaz, true);
            } else {
                update_post_meta($product_id, 'pod_zakaz', 0);
                // WmsLogs::set_logs("Установлено поле pod_zakaz = 0", true);
            }

            // WmsLogs::set_logs('Публикуем товар: '.$s_product->get_sku().'. Отметка в магазине = '.$checkVmagazine, true);
        }

        // WmsLogs::set_logs("Проверка цены товара: " . $s_product->get_price() . " руб.", true);
        if ($s_product->get_price() > 0 == false) {
            // WmsLogs::set_logs("Цена товара равна 0 или меньше. Переводим в черновик", true);
            wp_update_post(
                array(
                    'ID' => $s_product->get_id(),
                    'post_status' => 'draft'
                )
            );
            // WmsLogs::set_logs("Товар переведен в статус 'draft' из-за нулевой цены", true);

            // WmsLogs::set_logs($s_product->get_sku() . " статус " . $s_product->get_status() . ". " . $s_product->get_price() . " руб. – Цена. " . $s_product->get_stock_quantity() . " – текущий остаток.", true);
        } else {
            // WmsLogs::set_logs("Цена товара больше 0. Товар остается опубликованным", true);
        }

        WmsLogs::set_logs("Завершение обработки товара ID: " . $s_product->get_sku(), true);
        // ////////

        $quantity = (float) $stock['stock'] - (float) $stock['reserve'];

        $this->update_stock($product_id, $stock, $quantity);
        do_action('wms_stock_allstore_action', $product_id, $stock, $this->settings);
    }


    /**
     * @param $stock
     *
     * @return bool
     */
    public function update_stock_bystore($stock)
    {
        if (!$product_id = $this->get_product_id($stock)) {
            return false;
        }

        $stock_quantity = $this->get_stock_quantity_bystore($product_id, $stock);
        $this->update_stock($product_id, $stock, $stock_quantity);
        do_action('wms_stock_bystore_quantity_action', $product_id, $stock, $this->settings, $stock_quantity, $this);
    }


    /**
     * @return mixed|null
     */
    public function get_product()
    {
        return $this->product;
    }


    /**
     * @param $stock
     * @param null $key
     * @param null $href
     * @return bool|int|string
     */
    public function get_product_id($stock, $key = null, $href = null)
    {
        if ($this->product) {
            return $this->product->get_id();
        }

        $product_id = $this->get_assortment_id_stock($stock, $key, $href);

        if ($product_id === false) {
            return false;
        }

        return $product_id;
    }


}