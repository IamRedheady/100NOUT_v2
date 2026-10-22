<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 24.01.2018
 * Time: 17:07
 */

class WmsBundleApi extends WmsProductApi
{

    /**
     * @param $product_id
     * @throws Exception
     */
    protected function update_custom_function($product_id)
    {
        $this->update_components($product_id);
    }


    /**
     * @param $product_id
     * @throws Exception
     */
    private function update_components($product_id)
    {

        $components = array();
        $components_ms = $this->get_bundle_components();

        if(!$components_ms || !isset($components_ms['rows']) || !count($components_ms['rows']) > 0){
            $this->exception($this->product->get_id(), "ошибка получения товаров для комплекта ");
        }

        foreach ($components_ms['rows'] as $k => $v) {

            try {
                $component_id = get_wms_product_id(WmsHelper::get_id_ms_explode($v['assortment']['meta']['href']));

                if (!$component_id || !$component_id > 0) {
                    $this->exception(
                        $this->product->get_id(),
                        "ошибка получения товара для комплекта name: {$v['assortment']['meta']['href']}"
                    );
                }

                $product_wc = wc_get_product($component_id);

                if (!is_object($product_wc)) {
                    $this->exception(
                        $this->product->get_id(),
                        "Товара для комплекта нет в базе данных name: {$v['assortment']['meta']['href']}"
                    );
                }

                $components[] = array(
                    'id' => $component_id,
                    '_id_ms' => WmsHelper::get_id_ms_explode($v['assortment']['meta']['href']),
                    '_sku' => $product_wc->get_sku(),
                    'name' => $product_wc->get_name(),
                    'slug' => get_permalink($component_id),
                    'quantity' => $v['quantity'],
                );

            } catch (Exception $exception) {
                $this->exception($this->product->get_id(), $exception->getMessage());
            }

        }

        $this->product->update_meta_data('_components_ms', $components);

    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    private function get_bundle_components()
    {
        return WmsConnectApi::get_instance()->send_request($this->assortment['components']['meta']['href']);

    }

}