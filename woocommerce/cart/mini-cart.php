<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>


	
<?php
do_action( 'woocommerce_before_mini_cart_contents' );

$cartCount = 1;
foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    if ($cartCount == WC()->cart->get_cart_contents_count()) {
        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
    
        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
            $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
            $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
            $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
    
            $p_image_url = wp_get_attachment_url( $_product->get_image_id());
            $link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $_product );
    
            ?>
            <div class="cartP__product">
                <a href="<?php echo $link;?>" class="cartP__product-pic">
                    <img src="<?php if ($p_image_url) { echo $p_image_url;} else {echo "https://100nout.by/wp-content/uploads/2023/03/zagl.png";}?>" alt="Фото" class="cartP__product-pic-img">
                </a>
                <div class="cartP__product-content">
                    <a href="<?php echo $link;?>" class="cartP__product-title text text-lg text--semibold">
                        <?php
                        $productNameSost = "";
                        if ($_product->get_attribute('sostoyanie') == "новый") {
                            $productNameSost = "Новый ".$_product->get_name()."";
                        } else if ($_product->get_attribute('sostoyanie') == "5") {
                            $productNameSost = "".$_product->get_name().", отличное состояние";
                        } else {
                            $productNameSost = "Б/у ".$_product->get_name()."";
                        }
                        echo $productNameSost;?>
                    </a>
                    <div class="cartP__product-price text-3xl">
                        <span>
                            
                            <?php echo number_format($_product->get_price(), 0, '', ' ');?></span><span class="cartP__product-price-currency text text-xs">BYN</span>
                        <span class="cartP__product-price-old text text-xs">
                            <?php echo number_format($_product->get_regular_price(), 0, '', ' ');?></span>
                    </div>
                    <?php
                        echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            'woocommerce_cart_item_remove_link',
                            sprintf(
                                '<a href="%s" class="remove remove_from_cart_button cartP__product-remove link link-secondary js-remove-from-cart-prs" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" onclick="onCartClose()">Удалить</a>',
                                esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                esc_attr__( 'Remove this item', 'woocommerce' ),
                                esc_attr( $product_id ),
                                esc_attr( $cart_item_key ),
                                esc_attr( $_product->get_sku() )
                            ),
                            $cart_item_key
                        );
                    ?>
                    <div class="cartP__product-bottom">
                        <p class="cartP__product-bottom-text text text-sm">
                            Защитите свою технику с пакетом дополнительного сервисного обслуживания
                        </p>
                        <a href="/dso" class="cartP__product-bottom-more btn btn-secondary">
                            Узнать больше
                        </a>
                    </div>
                </div>
            </div>
            <?php
                $prsCats = "";

                $cat = $_product->get_category_ids();
                $ancestors = get_ancestors( $cat[0], 'product_cat' );

                if ($ancestors[0] == 2309) {
                    $prsCats = array('2330');
                }

                if ($ancestors[0] == 2333) {
                    $prsCats = array('2432', '2445');
                }

                if ($prsCats !== "") {
                    ?>
                        <div class="cartP__related">
                            <div class="prs">
                                <h2 class="prs__title text-3xl">
                                    С этим товаром покупают
                                </h2>
                                <ul class="prs__list hide-scroll">
                                    <?php 
                                        nout_show_products_by_id_mini_cart($prsCats);
                                        ?>
                                </ul>
                            </div>
                        </div>
                    <?php
                }
            ?>
            <div class="cartP__total">
                <div class="cartP__total-content">
                    <p class="cartP__total-count text text-xs">В корзине <?php
                        $productCount = WC()->cart->get_cart_contents_count();

                        if ($productCount == 1) {
                            echo $productCount;
                            echo " товар";
                        }

                        if ($productCount == 2 or $productCount == 3 or $productCount == 4) {
                            echo $productCount;
                            echo " товара";
                        }

                        if ($productCount > 4) {
                            echo $productCount;
                            echo " товаров";
                        }

                        
                    ?></p>
                    <p class="cartP__total-sum">
                        <span class="text text-lg">Итого:</span>
                        <span class="text text-2xl text--semibold">
                            
                            <?php echo number_format(WC()->cart->total, 0, '', ' '); ?> BYN</span>
                    </p>
                </div>
                <div class="cartP__total-actions">
                    <button onclick="onCartClose()" type="button" class="btn btn-secondary">Продолжить покупки</button>
                    <a href="/cart" class="btn btn-primary btn_icon-left"><svg width="16" height="17"
                            viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1 0C0.447715 0 0 0.447715 0 1C0 1.55228 0.447715 2 1 2H2.21922L2.52478 3.22224C2.52799 3.23637 2.5315 3.25039 2.5353 3.26429L3.89253 8.69321L2.99995 9.58578C1.74002 10.8457 2.63235 13 4.41416 13H13C13.5522 13 14 12.5523 14 12C14 11.4477 13.5522 11 13 11L4.41417 11L5.41416 10H12C12.3788 10 12.725 9.786 12.8944 9.44721L15.8944 3.44721C16.0494 3.13723 16.0329 2.76909 15.8507 2.47427C15.6684 2.17945 15.3466 2 15 2H4.28078L3.97014 0.757464C3.85885 0.312297 3.45887 0 3 0H1Z"
                                fill="white" />
                            <path
                                d="M14 15.5C14 16.3284 13.3284 17 12.5 17C11.6716 17 11 16.3284 11 15.5C11 14.6716 11.6716 14 12.5 14C13.3284 14 14 14.6716 14 15.5Z"
                                fill="white" />
                            <path
                                d="M4.5 17C5.32843 17 6 16.3284 6 15.5C6 14.6716 5.32843 14 4.5 14C3.67157 14 3 14.6716 3 15.5C3 16.3284 3.67157 17 4.5 17Z"
                                fill="white" />
                        </svg>
                        Перейти в корзину</a>
                </div>
            </div>
            <?php
        }
    }

    $cartCount = $cartCount + 1;
}

do_action( 'woocommerce_mini_cart_contents' );
?>

	<!-- <p class="woocommerce-mini-cart__total total"> -->
		<?php
		/**
		 * Hook: woocommerce_widget_shopping_cart_total.
		 *
		 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
		 */
		// do_action( 'woocommerce_widget_shopping_cart_total' );
		?>
	<!-- </p> -->

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<!-- <p class="woocommerce-mini-cart__buttons buttons"> -->
        <?php 
        // do_action( 'woocommerce_widget_shopping_cart_buttons' ); 
        ?>
        <!-- </p> -->

	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

<?php else : ?>

	<p class="woocommerce-mini-cart__empty-message"><?php esc_html_e( 'No products in the cart.', 'woocommerce' ); ?></p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>