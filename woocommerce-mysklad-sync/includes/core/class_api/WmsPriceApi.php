<?php

use WCSTORES\WC\MS\MoySklad\PriceType;


/**
 * Class WmsPriceApi
 */
class WmsPriceApi
{


    /**
     * @var
     */
    public $price;
    /**
     * @var
     */
    public $sale_price;


    /**
     * @var mixed
     */
    private $prices_array;
    /**
     * @var null
     */
    private $product_id;
    /**
     * @var null
     */
    private $settings;


    /**
     * @param null $product_id
     * @param null $products
     * @param null $settings
     * @version  1.0.3
     * WmsPriceApi constructor.
     *
     */
    public function __construct($product_id = null, $products = null, $settings = null)
    {
        $this->product_id = $product_id;
        $this->settings = $settings;

        $this->prices_array = $this->set_price_array();
        $this->set_prices($products);


    }


    /**
     * @return mixed
     * @version  1.0.3
     */
    public function get_prices()
    {
        return $this->prices_array;
    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    protected function get_prices_ms()
    {
        return WmsConnectApi::get_instance()->send_request(WMS_URL_API_V2 . 'context/companysettings/pricetype');
    }


    /**
     * @return mixed
     */
    private function set_price_array()
    {
        return PriceType::make()->getByData();
    }

    /**
     * @param $products
     * @return void
     */
    private function set_prices($products)
    {
        $productName = isset($products['name']) ? $products['name'] : '(без имени)';

        if (!isset($products['salePrices'])) {
            WmsLogs::set_logs('[WmsPriceApi::set_prices] "' . $productName . '": поле salePrices отсутствует в данных — цена будет 0', true);
            return;
        }

        $main_price = 0;
        $sale_price = 0;

        if (!isset($this->settings['wms_price'])) {
            $main_price = intval($products['salePrices'][0]['value']);
            $firstPriceType = isset($products['salePrices'][0]['priceType']['name']) ? $products['salePrices'][0]['priceType']['name'] : '?';
            WmsLogs::set_logs('[WmsPriceApi::set_prices] "' . $productName . '": wms_price не задана, берём первую цену "' . $firstPriceType . '"=' . ($main_price / 100), true);
        } else {
            $foundMain = false;
            $foundSale = false;

            foreach ($products['salePrices'] as $price) {
                $priceTypeName = isset($price['priceType']['name']) ? $price['priceType']['name'] : '?';
                $priceTypeId = isset($price['priceType']['id']) ? $price['priceType']['id'] : '?';
                $priceValue = intval($price['value']);

                if ($priceTypeId == $this->settings['wms_price']) {
                    $main_price = $priceValue;
                    $foundMain = true;
                    WmsLogs::set_logs('[WmsPriceApi::set_prices] "' . $productName . '": найдена основная цена "' . $priceTypeName . '"=' . ($main_price / 100), true);
                } elseif (isset($this->settings['wms_sale_price']) && $priceTypeId == $this->settings['wms_sale_price']) {
                    $sale_price = $priceValue;
                    $foundSale = true;
                    WmsLogs::set_logs('[WmsPriceApi::set_prices] "' . $productName . '": найдена акц.цена "' . $priceTypeName . '"=' . ($sale_price / 100), true);
                }
            }

            if (!$foundMain) {
                $available = array_map(function($p) {
                    return isset($p['priceType']['name']) ? '"' . $p['priceType']['name'] . '"(id=' . $p['priceType']['id'] . ')=' . ($p['value'] / 100) : '?';
                }, $products['salePrices']);
                WmsLogs::set_logs('[WmsPriceApi::set_prices] "' . $productName . '": ВНИМАНИЕ — тип цены wms_price=' . $this->settings['wms_price'] . ' НЕ НАЙДЕН! Доступные типы: [' . implode(', ', $available) . ']', true);
            }

            if (isset($this->settings['wms_sale_price']) and !$foundSale) {
                WmsLogs::set_logs('[WmsPriceApi::set_prices] "' . $productName . '": тип акц.цены wms_sale_price=' . $this->settings['wms_sale_price'] . ' не найден в salePrices', true);
            }

            if (!$main_price > 0 && $sale_price > 0) {
                WmsLogs::set_logs('[WmsPriceApi::set_prices] "' . $productName . '": основная цена = 0, используем акц.цену как основную', true);
                $main_price = $sale_price;
                $sale_price = 0;
            }
        }

        $this->set_price($main_price);
        $this->set_sale_price($sale_price);
    }

    /**
     * @param mixed $price
     * @return float|int
     */
    private function set_price($price)
    {
        return $this->price = $price / 100;
    }

    /**
     * @param mixed $sale_price
     * @return float|int|string
     */
    private function set_sale_price($sale_price)
    {
        if (!$sale_price > 0) {
            return $this->sale_price = '';
        }
        return $this->sale_price = $sale_price / 100;
    }


}