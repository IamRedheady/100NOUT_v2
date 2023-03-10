<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$link_text = "В корзину";
if(WC()->cart->find_product_in_cart( WC()->cart->generate_cart_id( $product->get_id() ) )) {
    $cart_i = "";
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        if($cart_item['product_id'] ==  $product->get_id()){
            $cart_i = $cart_item_key;
        }
    }
    echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        'woocommerce_cart_item_remove_link',
        sprintf(
            '<a href="%s" class="remove product__buy btn btn-secondary js-remove-from-cart-prs" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_item_key="%s">Убрать из корзины</a>',
            esc_url( wc_get_cart_remove_url( $cart_i ) ),
            esc_html__( 'Remove this item', 'woocommerce' ),
            esc_attr( $product->get_id() ),
            esc_attr( $product->get_sku() ),
            esc_attr( $cart_i )
        ),
        $cart_i
    );
} else {
    echo apply_filters(
        'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
        sprintf(
            '<a href="%s" data-quantity="%s" class="product__buy btn btn-primary %s" %s>%s</a>',
            esc_url( $product->add_to_cart_url() ),
            esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
            esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
            isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
            $link_text
        ),
        $product,
        $args
    );
}
