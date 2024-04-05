<?php
/**
 * The Template for displaying dropdown wishlist products.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-product-counter.php.
 *
 * @version             2.3.1
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
wp_enqueue_script( 'tinvwl' );
if ( $icon_class && 'custom' === $icon && ! empty( $icon_upload ) ) {
	$text = sprintf( '<img src="%s" /> %s', esc_url( $icon_upload ), $text );
}
?>
<a style="display: none;" href=""
   class="wishlist_products_counter<?php echo ' ' . $icon_class . ' ' . $icon_style . ( empty( $text ) ? ' no-txt' : '' ) . ( 0 < $counter ? ' wishlist-counter-with-products' : '' ); // WPCS: xss ok. ?>">
	<span class="wishlist_products_counter_text"><?php echo $text; // WPCS: xss ok. ?></span>
	<?php if ( $show_counter ) : ?>
		<span class="wishlist_products_counter_number"></span>
	<?php endif; ?>
</a>

<a href="<?php echo esc_url( tinv_url_wishlist_default() ); ?>" class="header__favorite btn btn-secondary btn-icon" name="<?php echo esc_attr( sanitize_title( $text ) ); ?>" aria-label="<?php echo esc_attr( $text ); ?>">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd"
            d="M3.80628 6.20569C5.68079 4.33118 8.71999 4.33118 10.5945 6.20569L12.0004 7.61158L13.4063 6.20569C15.2808 4.33118 18.32 4.33118 20.1945 6.20569C22.069 8.08021 22.069 11.1194 20.1945 12.9939L12.0004 21.188L3.80628 12.9939C1.93176 11.1194 1.93176 8.08021 3.80628 6.20569Z"
            fill="#21201F" />
    </svg>
   
        <span class="favorite-empty">
        <span class="favorite-empty__title">
            В избранном пусто 
        </span>
        <span class="favorite-empty__subtitle">
            Добавляйте товары с помощью ❤️
        </span>
    </span>
</a>
