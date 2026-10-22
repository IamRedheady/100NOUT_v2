<?php

use WCSTORES\WC\MS\Facades\ImageUpdatesQueuesDataStore;
use WCSTORES\WC\MS\MoySklad\Images;
use WCSTORES\WC\MS\Queues\Queues;


/**
 * Class WmsAssortment
 */
abstract class WmsAssortment extends WmsData
{

    /**
     * @var
     */
    protected $assortment;

    /**
     * @var
     */
    protected $publish = 'publish';

    /**
     * @var
     */
    protected $product = null;

    /**
     * @var mixed
     */
    protected $product_id = false;

    /**
     * @var int
     */
    protected $parent_id = 0;

    /**
     * @var
     */
    protected $type;

    /**
     * @var
     */
    protected $product_type = 'product';


    /**
     * @var null
     */
    protected $product_current_hash = null;


    /**
     * @var array
     */
    protected $data = array(
        'post_title' => '',
        'post_type' => '',
        'post_status' => 'publish',
        'post_parent' => 0,
    );


    /**
     * WmsAssortment constructor.
     *
     * @param $assortment
     * @param string $settings
     * @param bool $oProduct
     * @throws Exception
     */
    public function __construct($assortment, $settings = 'wms_settings_product', $oProduct = false)
    {
        $this->set_settings($settings);

        if (is_object($oProduct)) {
            $this->product = $oProduct;
            $this->product_id = $oProduct->get_id();
        } else {
            $this->product_id = $oProduct;
        }

        do_action('before_wms_assortment_action', $this, $assortment, $settings);

        if (!$this->product_id) {
            $var_sync = wms_var_sync($this->settings['wms_product_select_var']);
            $this->product_id = wms_get_product_id($this->settings['wms_product_select_var'], $assortment[$var_sync]);
        }

        $this->set_id($this->product_id);
        $this->set_product($this->product_id);

        $assortment = apply_filters('wms_assortment_ms_array', $assortment, $this->settings, $this->product_id, $this);

        $this->set_assortment($assortment);

    }

    /**
     * @return bool|int|mixed|string|WP_Error|null
     * @throws Exception
     */
    public function add()
    {
        $productName = isset($this->assortment['name']) ? $this->assortment['name'] : '(без имени)';
        $productType = isset($this->assortment['meta']['type']) ? $this->assortment['meta']['type'] : '(тип неизвестен)';

        if (isset($this->assortment['archived']) and $this->assortment['archived'] == true) {
            WmsLogs::set_logs('[WmsAssortment::add] Пропуск товара "' . $productName . '" (тип=' . $productType . ', WC_ID=' . $this->product_id . '): товар в архиве МойСклад', true);
            $this->exception($this->product_id, 'Товар находится в архиве');
        }

        if (is_object($this->product) and isset($this->settings['wms_product_variant_sync']) and $this->settings['wms_product_variant_sync'] == 'full') {
            $sProductHash = $this->product->get_meta('_ms_product_hash');

            if ($sProductHash and $sProductHash === md5(serialize($this->assortment))) {
                WmsLogs::set_logs('[WmsAssortment::add] Пропуск товара "' . $productName . '" (WC_ID=' . $this->product_id . '): хэш данных не изменился, обновление не требуется', true);
                return $this->product;
            }
        }

        if ($this->product_id == 'empty') {
            WmsLogs::set_logs('[WmsAssortment::add] Пропуск товара "' . $productName . '": product_id = empty', true);
            return false;
        }

        $products = apply_filters('wms_load_array_products', $this->assortment, $this->settings, $this->product_id, $this);
        if ($products == false) {
            WmsLogs::set_logs('[WmsAssortment::add] Пропуск товара "' . $productName . '": фильтр wms_load_array_products вернул false', true);
            return false;
        }

        $this->set_name($this->assortment['name']);
        $this->set_type($this->product_type);

        if (!isset($this->settings['wms_product_variant_sync']) or $this->settings['wms_product_variant_sync'] == 'fullno') {
            $this->settings['wms_product_variant_sync'] = 'full';
        }

        $syncMode = $this->settings['wms_product_variant_sync'];
        WmsLogs::set_logs('[WmsAssortment::add] Товар "' . $productName . '" (тип=' . $productType . ', WC_ID=' . $this->product_id . '): режим синхронизации=' . $syncMode, true);

        if ($this->product_id == false and $syncMode == 'full') {
            WmsLogs::set_logs('[WmsAssortment::add] Товар "' . $productName . '": не найден в WooCommerce, создаём новый (INSERT)', true);
            $this->product = $this->get_object_product();
            return $this->insert();
        }

        if ($this->product_id == false) {
            WmsLogs::set_logs('[WmsAssortment::add] Товар "' . $productName . '": не найден в WooCommerce, режим ' . $syncMode . ' не создаёт новые товары — пропуск', true);
            return false;
        }

        if (apply_filters('wms_load_array_products_ms_updated', false)) {
            $meta_values = get_post_meta($this->product_id, '_ms_updated', true);

            if (!empty($meta_values) and isset($this->assortment['updated']) and $meta_values === $this->assortment['updated']) {
                WmsLogs::set_logs('[WmsAssortment::add] Пропуск товара "' . $productName . '" (WC_ID=' . $this->product_id . '): _ms_updated совпадает с updated из МойСклад (' . $meta_values . ')', true);
                return false;
            }
        }

        if ($this->product_id > 0) {
            $this->product = $this->get_product($this->product_id);

            if ($syncMode == 'updated' or $syncMode == 'full') {
                WmsLogs::set_logs('[WmsAssortment::add] Товар "' . $productName . '" (WC_ID=' . $this->product_id . '): запуск полного UPDATE (цена, категории, атрибуты, изображения)', true);
                return $this->update();
            }

            if ($syncMode == 'updated_meta') {
                WmsLogs::set_logs('[WmsAssortment::add] Товар "' . $productName . '" (WC_ID=' . $this->product_id . '): режим updated_meta — обновляются только мета-поля (цена и категории НЕ обновляются)', true);
                return $this->update_only_the_meta();
            }

            WmsLogs::set_logs('[WmsAssortment::add] Товар "' . $productName . '" (WC_ID=' . $this->product_id . '): режим ' . $syncMode . ' — обновляется только цена', true);
            return $this->update_only_the_price();
        }

        return false;

    }

    /**
     * @param $product_id
     * @throws Exception
     */
    public function set_product($product_id)
    {
        if ($product_id > 0) {
            $this->product = $this->get_product($product_id);
        }
    }


    /**
     * @return WC_Product
     */
    public function get_object_product()
    {
        return new WC_Product();
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public function update_only_the_price()
    {
        return ($this->update_price()) ? $this->save_product() : false;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function update_only_the_meta()
    {
        return ($this->update_meta()) ? $this->save_product() : false;
    }


    /**
     * @param mixed $product_id
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }


    /**
     * @param bool $publish
     *
     * @return int|mixed|null|WP_Error
     * @throws Exception
     */
    protected function insert($publish = false)
    {

        do_action('before_wms_assortment_insert_action', $this->assortment, $this);

        if (!is_object($this->product)) {
            $this->product = new WC_Product();
        }

        if (isset($this->settings['wms_post_status']) and $publish === false) {
            $this->product->set_status($this->settings['wms_post_status']);
            $this->publish = $this->settings['wms_post_status'];
        } else {
            $this->product->set_status('publish');
        }

        $this->product->set_name($this->assortment['name']);
        $this->product->set_stock_status('instock');
        $this->product->set_catalog_visibility('visible');

        $this->product->update_meta_data('_id_ms', $this->assortment['id']);
        $this->product->update_meta_data('_product_attributes', array());
        $this->updateSku();

        $this->product_id = $this->product->save();

        if ($this->product_id > 0) {

            $this->update_product_type();

            do_action('wms_product_insert_action', $this->product_id, $this->assortment, $this);

            $this->product = $this->get_product($this->product_id);
            $this->update();

            do_action('after_wms_assortment_insert_action', $this->assortment, $this->product_id, $this);

            return $this->product_id;
        } else {
            $this->exception("ОШИБКА создания продукта ");
        }

        return $this->product_id;

    }

    /**
     *
     */
    public function updateSku()
    {
        if (isset($this->settings['wms_product_sku']) and !empty($this->assortment[$this->settings['wms_product_sku']])) {
            $sku = $this->assortment[$this->settings['wms_product_sku']];
        }
        if (isset($sku) and !empty(trim($sku)) and $this->product->get_sku() !== $sku) {
            $this->product->set_sku($sku);
        }

    }


    /**
     * @return bool|mixed|string
     * @throws Exception
     */
    protected function update()
    {
        $productName = isset($this->assortment['name']) ? $this->assortment['name'] : '(без имени)';
        WmsLogs::set_logs('[WmsAssortment::update] "' . $productName . '" (WC_ID=' . $this->product_id . '): старт полного обновления', true);

        do_action('before_wms_assortment_update_action', $this->product_id, $this->assortment, $this);

        if (!isset($this->settings['wms_name']) or $this->settings['wms_name'] !== 'on') {
            if ($this->product->get_name() != $this->assortment['name']) {
                WmsLogs::set_logs('[WmsAssortment::update] "' . $productName . '": обновляем название ("' . $this->product->get_name() . '" → "' . $this->assortment['name'] . '")', true);
                $this->product->set_name($this->assortment['name']);
            }
        } else {
            WmsLogs::set_logs('[WmsAssortment::update] "' . $productName . '": обновление названия отключено (wms_name=on)', true);
        }

        if (!isset($this->settings['wms_description']) or $this->settings['wms_description'] !== 'on') {
            if (isset($this->assortment['description'])) {
                if (isset($this->settings['wms_description_type'])) {
                    switch ($this->settings['wms_description_type']) {
                        case 'description':
                            $this->product->set_description($this->assortment['description']);
                            break;
                        case 'short_description':
                            $this->product->set_short_description($this->assortment['description']);
                            break;
                    }
                } else {
                    $this->product->set_description($this->assortment['description']);
                }
            }
        }

        if (isset($this->assortment['weight']) and $this->product->get_weight() !== $this->assortment['weight']) {
            $this->product->set_weight($this->assortment['weight']);
        }

        $this->updateSku();
        $this->update_price();
        $this->update_meta();
        $this->update_product_image();
        $this->update_custom_function($this->product_id);

        $publish = apply_filters('wms_product_update_action_publish', false, $this->assortment, $this->settings, $this->product_id, $this);

        if ($publish) {
            $this->product->set_status($publish);
        }

        do_action('wms_assortment_update_action', $this->product_id, $this->assortment, $this);

        $savedId = $this->save_product();
        WmsLogs::set_logs('[WmsAssortment::update] "' . $productName . '" (WC_ID=' . $this->product_id . '): товар сохранён, результат=' . var_export($savedId, true), true);

        return $savedId;

    }

    /**
     * @return mixed
     * @throws Exception
     */
    protected function save_product()
    {
        if (!is_object($this->product)) {
            $this->exception("Товара нет для сохранения");
        }

        $date_modified = $this->product->get_date_modified();

        if (isset($date_modified) and !empty($date_modified) and $date_modified !== false) {
            $this->product->set_date_modified(current_time('timestamp', true));
        }

        $this->product = apply_filters('wms_assortment_update_filter', $this->product, $this->assortment, $this);

        $this->product->update_meta_data('_ms_product_hash', md5(serialize($this->assortment)));

        if (!$iProductId = $this->product->save()) {
            $this->exception($this->product->get_id(), "ОШИБКА сохранение товара ");
        }

        do_action('after_wms_assortment_update_action', $iProductId, $this->assortment, $this);

        return $iProductId;

    }


    /**
     * @param string $id
     * @param string $message
     * @throws Exception
     */
    protected function exception($id = '', $message = '')
    {
        throw new WC_Data_Exception('product_invalid', $message, 400, array('resource_id' => $id));
    }


    /**
     * @param $product_id
     *
     * @return null
     * @throws Exception
     */
    public function get_product($product_id = 0)
    {
        if (is_object($this->product)) {
            return $this->product;
        }

        if ($product_id == 0) {
            $this->exception('Не удалось получить товар  id равно 0');
        }

        $this->product = wc_get_product($product_id);

        if (!is_object($this->product)) {
            $this->exception($product_id, 'Не удалось получить товар  id ' . $this->product_id);
        }

        return $this->product;
    }

    /**
     * добавить настройку про обновление изображений
     * @throws Exception
     */
    protected function update_product_image()
    {
        if (isset($this->assortment['images']) && $this->assortment['images']['meta']['size'] > 0 && ($this->settings['wms_load_image'] == 'on' || $this->settings['wms_load_image'] == 'all')) {

            if ($data = Images::getDataImage($this->assortment['images'])) {

                if ($this->product->get_image_id() > 0 && $this->product->get_meta('_ms_image_update_hash') == $data['hash']) {
                    return;
                }

                ImageUpdatesQueuesDataStore::create([
                    'product_id' => $this->product->get_id(),
                    'data' => $data
                ]);

                $this->product->update_meta_data('_ms_image_update_hash', $data['hash']);

                do_action('wms_assortment_update_images_action', $this->product->get_id(), $this->assortment['images'], $this->assortment, $this);
            }

        }

    }


    /**
     * @return mixed
     */
    public function get_prices()
    {
        $productName = isset($this->assortment['name']) ? $this->assortment['name'] : '(без имени)';

        $price = new WmsPriceApi($this->product_id, $this->assortment, $this->settings);

        $assortment_price = $price->price;

        $priceSource = 'МойСклад';
        if (empty($assortment_price) and $this->parent_id > 0) {
            $assortment_price = get_post_meta($this->parent_id, '_price', true);
            $priceSource = 'родительский товар WC ID=' . $this->parent_id;
        }

        $assortment_price = floatval(apply_filters('wms_product_price', $assortment_price, $this->settings));
        $product_price['price'] = $assortment_price;
        $product_price['regular_price'] = $assortment_price;

        $product_sale_price = $price->sale_price;

        if (empty($product_sale_price) and $this->parent_id > 0) {
            $product_sale_price = get_post_meta($this->parent_id, '_sale_price', true);
        }

        if ($product_sale_price <= 0) {
            $product_sale_price = '';
        }

        $product_price['sale_price'] = apply_filters('wms_product_sale_price', $product_sale_price, $this->settings);

        $salePriceLog = $product_price['sale_price'] !== '' ? $product_price['sale_price'] : '(нет)';
        WmsLogs::set_logs('[get_prices] "' . $productName . '" (WC_ID=' . $this->product_id . '): цена=' . $product_price['price'] . ' (источник: ' . $priceSource . '), акц.цена=' . $salePriceLog, true);

        if (!isset($this->settings['wms_price'])) {
            WmsLogs::set_logs('[get_prices] "' . $productName . '": настройка wms_price не задана — используется первая цена из salePrices[]', true);
        } else {
            WmsLogs::set_logs('[get_prices] "' . $productName . '": используется тип цены wms_price=' . $this->settings['wms_price'], true);
            if (!isset($this->assortment['salePrices']) or empty($this->assortment['salePrices'])) {
                WmsLogs::set_logs('[get_prices] "' . $productName . '": ВНИМАНИЕ — salePrices отсутствует в данных МойСклад! Цена будет 0', true);
            } elseif ($product_price['price'] == 0) {
                $availablePriceTypes = array_map(function($p) {
                    return isset($p['priceType']['id']) ? $p['priceType']['name'] . '(' . $p['priceType']['id'] . ')=' . ($p['value']/100) : '?';
                }, $this->assortment['salePrices']);
                WmsLogs::set_logs('[get_prices] "' . $productName . '": ВНИМАНИЕ — цена = 0! Тип цены wms_price=' . $this->settings['wms_price'] . ' не найден среди доступных: [' . implode(', ', $availablePriceTypes) . ']', true);
            }
        }

        return $product_price;
    }


    /**
     * @param $assortment
     *
     * @return mixed
     */
    protected function set_assortment($assortment)
    {
        return $this->assortment = $assortment;
    }


    /**
     * @param $name
     *
     * @return mixed
     */
    protected function set_name($name)
    {
        return $this->data['post_title'] = $name;
    }


    /**
     * @param $type
     *
     * @return mixed
     */
    protected function set_type($type)
    {
        return $this->data['post_type'] = $type;
    }


    /**
     * @param $status
     *
     * @return mixed
     */
    protected function set_status($status)
    {
        return $this->data['post_status'] = $status;
    }

    /**
     * @param $parent
     *
     * @return mixed
     */
    protected function set_parent($parent)
    {
        return $this->data['post_parent'] = $parent;
    }


    /**
     *
     */
    protected function update_product_type()
    {
        $product_type = $this->assortment['meta']['type'] == 'variant' ? 'variable' : 'simple';

        $product_id = $product_type == 'variable' ? $this->parent_id : $this->product_id;

        wp_set_object_terms($product_id, $product_type, 'product_type', false);
    }


    /**
     */
    protected function update_price()
    {
        $productName = isset($this->assortment['name']) ? $this->assortment['name'] : '(без имени)';
        $price = $this->get_prices();
        $update = false;

        $current_price = floatval($this->product->get_price());
        $new_price = floatval($price['price']);

        WmsLogs::set_logs('[update_price] "' . $productName . '" (WC_ID=' . $this->product_id . '): текущая цена=' . $current_price . ', новая цена=' . $new_price, true);

        if ($current_price != $new_price) {
            WmsLogs::set_logs('[update_price] "' . $productName . '": цена изменилась (' . $current_price . ' → ' . $new_price . '), обновляем', true);
            $this->product->set_price($price['price']);
            $this->product->set_regular_price($price['regular_price']);
            $update = true;
        } else {
            WmsLogs::set_logs('[update_price] "' . $productName . '": цена не изменилась (' . $current_price . '), пропускаем', true);
        }

        if (isset($this->settings['wms_sale_price_on']) and $this->settings['wms_sale_price_on'] == 'on') {
            $current_sale = $this->product->get_sale_price();
            $new_sale = $price['sale_price'];
            $current_sale_float = $current_sale !== '' && $current_sale !== null ? floatval($current_sale) : null;
            $new_sale_float = $new_sale !== '' && $new_sale !== null ? floatval($new_sale) : null;

            WmsLogs::set_logs('[update_price] "' . $productName . '": текущая акц.цена=' . var_export($current_sale_float, true) . ', новая акц.цена=' . var_export($new_sale_float, true), true);

            if ($current_sale_float !== $new_sale_float) {
                WmsLogs::set_logs('[update_price] "' . $productName . '": акц.цена изменилась, обновляем', true);
                $this->product->set_sale_price($price['sale_price']);
                $update = true;
            }
        } else {
            WmsLogs::set_logs('[update_price] "' . $productName . '": обновление акц.цены отключено (wms_sale_price_on != on)', true);
        }

        if ($update) {
            do_action('wms_assortment_update_price_action', $this, $price);
        }

        return $update;

    }


    /**
     *
     * @return bool
     */
    protected function update_meta()
    {
        $meta_data = array(
            '_id_ms' => $this->assortment['id'],
            '_externalCode' => $this->assortment['externalCode'],
            '_ms_updated' => $this->assortment['updated'],
            '_ms_uuid' => $this->assortment['meta']['uuidHref'],
            '_ms_type' => $this->assortment['meta']['type'],
            'ms_externalCode' => $this->assortment['externalCode'],
            'ms_article' => (isset($this->assortment['article'])) ? $this->assortment['article'] : false,
            'ms_code' => (isset($this->assortment['code'])) ? $this->assortment['code'] : false,
        );

        $update = false;

        foreach ($meta_data as $key => $value) {
            if ($value and $this->is_meta_data_update($key, $value)) {
                $this->product->update_meta_data($key, $value);
                $update = true;
            }
        }

        return $update;

    }


    /**
     * @param string $key
     * @param string $value
     * @return bool
     */
    protected function is_meta_data_update($key = '', $value = '')
    {
        $key = $this->product->get_meta($key);

        if ($key != $value) {
            return true;
        }

        return false;
    }


    /**
     *
     */
    protected function update_groop()
    {
        $productName = isset($this->assortment['name']) ? $this->assortment['name'] : '(без имени)';

        if (!isset($this->settings['wms_load_groops']) or $this->settings['wms_load_groops'] !== 'on') {
            WmsLogs::set_logs('[update_groop] "' . $productName . '" (WC_ID=' . $this->product_id . '): обновление категорий отключено (wms_load_groops != on) — категории НЕ обновляются', true);
            return;
        }

        if (!isset($this->assortment['productFolder'])) {
            WmsLogs::set_logs('[update_groop] "' . $productName . '" (WC_ID=' . $this->product_id . '): поле productFolder отсутствует в данных МойСклад — категория не может быть установлена', true);
            return;
        }

        $folderName = isset($this->assortment['productFolder']['name']) ? $this->assortment['productFolder']['name'] : '(имя папки отсутствует)';
        $folderId = isset($this->assortment['productFolder']['id']) ? $this->assortment['productFolder']['id'] : '(ID отсутствует)';

        WmsLogs::set_logs('[update_groop] "' . $productName . '" (WC_ID=' . $this->product_id . '): папка МойСклад="' . $folderName . '" (id=' . $folderId . ')', true);

        $groop = new WmsGroopApi();
        $groopIds = $groop->update_product_groop($this->product_id, $this->assortment);

        $groopIds = apply_filters('wms_assortment_groop_ids', $groopIds, $this->assortment, $this->settings, $this->product_id);

        if ($groopIds) {
            WmsLogs::set_logs('[update_groop] "' . $productName . '" (WC_ID=' . $this->product_id . '): устанавливаем категории WC IDs=[' . implode(',', $groopIds) . ']', true);
            $this->product->set_category_ids($groopIds);
        } else {
            WmsLogs::set_logs('[update_groop] "' . $productName . '" (WC_ID=' . $this->product_id . '): ПРЕДУПРЕЖДЕНИЕ — update_product_groop вернул false (категория не найдена и не создана)', true);
        }

    }


    /**
     * @param $product_id
     * @return mixed
     */
    protected function update_custom_function($product_id)
    {
        return $product_id;
    }


}