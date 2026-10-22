<?php


/**
 * Class WmsBundlePublic
 */
class WmsBundlePublic
{
    /**
     * WmsBundlePublic constructor.
     */
    public function __construct()
    {
        add_action('wp_ajax_wms_load_component', array($this, 'view_component'));
        add_action('wp_ajax_nopriv_wms_load_component', array($this, 'view_component'));

        add_action('woocommerce_before_add_to_cart_form', array($this, 'view_components'), 1);
        add_action('woocommerce_available_variation', array($this, 'view_components_variation'), 1);


    }

    /**
     *
     */
    public function view_component()
    {
        $html = '';
        $product_id = intval(sanitize_text_field($_POST['id']));

        if($product_id > 0){
            $html = apply_filters('wms_view_component', $this->view_component_html($product_id), $product_id);
        }

        if(wp_doing_ajax()){
            wp_send_json_success($html);
        }

        echo $html;

    }

    /**
     * @param $product_id
     * @return false|string
     */
    public function view_component_html($product_id)
    {

        $product = wc_get_product($product_id);
        ob_start();
        ?>

        <div class="wms-view-component-main">
            <div class="wms-view-component-row">
                <div class="wms-view-component-col wms-product-gallery">
                    <figure class="woocommerce-product-gallery__wrapper">
                        <?php

                        $post_thumbnail_id = $product->get_image_id();
                        if ($post_thumbnail_id > 0) {
                            $html = wc_get_gallery_image_html($post_thumbnail_id, true);
                        } else {
                            $html = '<div class="woocommerce-product-gallery__image--placeholder">';
                            $html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src()), esc_html__('Awaiting product image', 'woocommerce'));
                            $html .= '</div>';
                        }

                        echo $html; ?>
                    </figure>
                </div>
                <div class="wms-view-component-col">
                    <h4 lass="product_title entry-title"><?php echo $product->name; ?></h4>
                </div>
            </div>

            <div class="wms-view-component-row wms-view-component-content">
                <p class="price"><?php echo $product->get_price_html(); ?></p>

                <?php echo wc_get_stock_html($product); ?>

                <div class="wms-view-component-meta">
                                        <span class="sku_wrapper"><?php esc_html_e('SKU:', 'woocommerce'); ?> <span
                                                    class="sku"><?php echo ($sku = $product->get_sku()) ? $sku : esc_html__('N/A', 'woocommerce'); ?></span></span>


                    <?php echo wc_get_product_category_list($product->get_id(), ', ', '<span class="posted_in">' . _n('Category:', 'Categories:', count($product->get_category_ids()), 'woocommerce') . ' ', '</span>'); ?>

                    <?php echo wc_get_product_tag_list($product->get_id(), ', ', '<span class="tagged_as">' . _n('Tag:', 'Tags:', count($product->get_tag_ids()), 'woocommerce') . ' ', '</span>'); ?>
                </div>

            </div>
        </div>

        <?php
        return ob_get_clean();
    }

    /**
     *
     */
    public function view_components()
    {
        if(!apply_filters('wms_is_visible_view_component', true)){
            return;
        }
        
        global $product;

        $components = $product->get_meta( '_components_ms');

        if ($components) {
            $components_table_html = apply_filters('wms_view_components_table_html', null, $components, $product);

            if(!$components_table_html){
                $components_table_html = call_user_func_array(
                    array($this, 'view_components_table'),
                    array($components, $product)
                );
            }

            echo $components_table_html;

        }
    }

    /**
     * @param $value
     * @return mixed
     */
    public function view_components_variation($value)
    {
         if(!apply_filters('wms_is_visible_view_component', true)){
            return $value;
        }
        
        $components = get_post_meta($value['variation_id'], '_components_ms', true);

        if (isset($components) and !empty($components) and $components != false) {
            $action_function = 'view_components_table_variation';
            $value['availability_html'] .= call_user_func_array(array($this, $action_function), array($components, $value['variation_id']));
        }

        return $value;
    }

    /**
     * @param $components
     * @return string
     */
    public function view_components_table_variation($components)
    {
        $html = '<div class="wms-view-component wms-view-component-none"></div>';
        $html .= '<table class="wms-bundle-public-components"><tr><th class="wms-bundle-public-components-title">';
        $html .= apply_filters('wms_bundle_public_components_title', 'Товары в комплекте');
        $html .= '</th><th class="wms-bundle-public-components-qty">';
        $html .= apply_filters('wms_bundle_public_components_qty', 'Количество');
        $html .= '</th> </tr><tbody>';


        foreach ($components as $k => $v) {
            $html .= '<tr><td class="wms-bundle-public-components-name" onmouseover="wms_open_component(' . $v["id"] . ')" onmouseout="wms_close_component()">';
            $html .= '<a  href="' . $v["slug"] . ' " >' . $v['name'] . ' <span>' . $v["_sku"] . '</span></a></td>';
            $html .= '<td class="wms-bundle-public-components-quantity">' . $v["quantity"] . '</td></tr>';
        }
        $html .= '</tbody></table>';

        return $html;

    }

    /**
     * @param $components
     * @return false|string
     */
    public function view_components_table($components)
    {
        ob_start();
        ?>
        <div class="wms-view-component wms-view-component-none">
        </div>
        <table class="wms-bundle-public-components">
            <tr>
                <th class="wms-bundle-public-components-title"><?php echo apply_filters('wms_bundle_public_components_title', 'Товары в комплекте'); ?></th>
                <th class="wms-bundle-public-components-qty"><?php echo apply_filters('wms_bundle_public_components_qty', 'Количество'); ?></th>
            </tr>
            <tbody>
            <?php foreach ($components as $k => $v) { ?>
                <tr>
                    <td class="wms-bundle-public-components-name">
                        <a href="<?php echo $v['slug']; ?> "
                           onmouseover="wms_open_component(<?php echo $v['id']; ?>)"
                           onmouseout="wms_close_component()"
                        ><?php echo $v['name']; ?>
                            <span>( <?php echo $v['_sku']; ?>)</span></a></td>
                    <td class="wms-bundle-public-components-quantity"><?php echo $v['quantity']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php
        return ob_get_clean();

    }


    /**
     * @param $components
     */
    public function view_components_list($components)
    { ?>
        <div class="wms-bundle-public-components-title">
            <?php echo apply_filters('wms_bundle_public_components_title', 'Комплектация:'); ?>
        </div>

        <ul class="wms-bundle-public-components" style="list-style: none">
            <?php foreach ($components as $k => $v) { ?>
                <li>
                    <span class="wms-bundle-public-components-quantity"><?php echo $v['quantity']; ?> x </span>
                    <a class="wms-bundle-public-components-name" href="<?php echo $v['slug']; ?>"><?php echo $v['name'] ?? ''; ?></a>
                </li>

            <?php } ?>
        </ul>
    <?php }

}

new WmsBundlePublic();

