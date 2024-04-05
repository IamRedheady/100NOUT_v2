<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="shop_table woocommerce-checkout-review-order-table">

<div class="ordering__sum-row">
                    <p class="ordering__sum-row-name text text-lg">
                        Товары:
                    </p>
                    <p class="ordering__sum-row-val text text-lg text--semibold">
                        <?php echo number_format(WC()->cart->subtotal - nout_get_dso_cost(), 0, '', ' ');?> BYN
                    </p>
                </div>
                <div class="ordering__sum-row">
                    <p class="ordering__sum-row-name text text-lg">
                        Доставка:
                    </p>
                    <p class="ordering__sum-row-val text text-lg text--semibold">
                        <?php 
                            if (WC()->cart->get_cart_shipping_total() != "Бесплатно!") {
                                echo WC()->cart->get_cart_shipping_total();
                            } else {
                                echo '0 BYN';
                            }
                        ?>
                    </p>
                </div>
                <?php
                    if (nout_get_dso_cost() != 0) {
                        ?>
                        <div class="ordering__sum-row">
                            <p class="ordering__sum-row-name text text-lg">
                                Доп. гарантия:
                            </p>
                            <p class="ordering__sum-row-val text text-lg text--semibold">
                                <?php echo nout_get_dso_cost();?> BYN
                            </p>
                        </div>
                        <?php
                    }
                ?>
                <div class="ordering__sum-final">
                    <p class="ordering__sum-final-name text-lg text--semibold">
                        К оплате:
                    </p>
                    <p class="ordering__sum-final-val text-2xl">
                    <?php echo number_format(WC()->cart->total, 0, '', ' ');?> BYN
                    </p>
                </div>
        <div style="display: none;">

       
		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<?php wc_cart_totals_shipping_html(); ?>
		<?php endif; ?>

        </div>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

        

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

</div>