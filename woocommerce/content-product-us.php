<?php

/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;
$p_image_url = wp_get_attachment_url($product->get_image_id());
$link = apply_filters('woocommerce_loop_product_link', get_the_permalink(), $product);
$product_id = $product->get_id();
$photos_ids = $product->get_gallery_image_ids();

$productNameSost = $product->get_name();
/* if ($product->get_attribute('sostoyanie') == "новый") {
    $productNameSost = "Новый " . $product->get_name() . "";
} else if ($product->get_attribute('sostoyanie') == "5") {
    $productNameSost = "" . $product->get_name() . ", отличное состояние";
} else {
    $productNameSost = "Б/у " . $product->get_name() . "";
} */

// Ensure visibility.
if (empty($product) || ! $product->is_visible()) {
    return;
}
?>
<li class="prs__list-item">
    <div class="product">
        <div class="product__center">
            <?php woocommerce_template_loop_add_to_cart(); ?>
            <a href="<?php echo $link; ?>" class="product__title">
                <?php echo $productNameSost; ?>
            </a>
            <p class="product__price">
                <?php
                if ($product->get_price()) {
                    echo number_format($product->get_price(), 0, '', ' ');
                } ?>
                <span>BYN</span>
            </p>
        </div>
    </div>
</li>