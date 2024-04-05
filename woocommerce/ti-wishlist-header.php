<?php
/**
 * The Template for displaying header for wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-header.php.
 *
 * @version             1.21.5
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<h2 class="prs__title text-4xl">
        Избранное
</h2>
<?php do_action( 'tinvwl_in_title_wishlist', $wishlist ); ?>

