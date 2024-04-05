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
$svg_id = $product->get_id() + strval(rand(0, 1000));
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
            '<a href="%s" data-quantity="%s" class="product__buy btn btn-primary %s" %s>%s <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M9.99999 16.6667C13.6819 16.6667 16.6667 13.682 16.6667 10.0001C16.6667 6.31818 13.6819 3.33341 9.99999 3.33341C6.31809 3.33341 3.33332 6.31818 3.33332 10.0001C3.33332 13.682 6.31809 16.6667 9.99999 16.6667ZM9.99999 18.3334C14.6024 18.3334 18.3333 14.6025 18.3333 10.0001C18.3333 5.39771 14.6024 1.66675 9.99999 1.66675C5.39762 1.66675 1.66666 5.39771 1.66666 10.0001C1.66666 14.6025 5.39762 18.3334 9.99999 18.3334Z" fill="white"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99999 3.33341C6.31809 3.33341 3.33332 6.31818 3.33332 10.0001C3.33332 13.682 6.31809 16.6667 9.99999 16.6667C10.4602 16.6667 10.8333 17.0398 10.8333 17.5001C10.8333 17.9603 10.4602 18.3334 9.99999 18.3334C5.39762 18.3334 1.66666 14.6025 1.66666 10.0001C1.66666 5.39771 5.39762 1.66675 9.99999 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001C18.3333 10.4603 17.9602 10.8334 17.5 10.8334C17.0398 10.8334 16.6667 10.4603 16.6667 10.0001C16.6667 6.31818 13.6819 3.33341 9.99999 3.33341Z" fill="url(#paint0_linear_2418_16434%s)"/>
            <defs>
            <linearGradient id="paint0_linear_2418_16434%s" x1="9.99999" y1="10.0001" x2="9.99999" y2="16.6667" gradientUnits="userSpaceOnUse">
            <stop stop-color="white"/>
            <stop offset="1" stop-color="white" stop-opacity="0"/>
            </linearGradient>
            </defs>
            </svg>
            </a>',
            esc_url( $product->add_to_cart_url() ),
            esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
            esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
            isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
            $link_text,
            $svg_id,
            $svg_id
        ),
        $product,
        $args
    );
}
