<?php
/**
 * The Template for displaying wishlist if a current user is owner.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist.php.
 *
 * @version             2.3.3
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
wp_enqueue_script( 'tinvwl' );
?>
<style>
    .tinvwl-table-manage-list {
        display: none;
    }
</style>
    <div class="tinv-wishlist woocommerce tinv-wishlist-clear prs layout">
        <?php do_action( 'tinvwl_before_wishlist', $wishlist ); ?>
        <?php if ( function_exists( 'wc_print_notices' ) && isset( WC()->session ) ) {
            wc_print_notices();
        } ?>
        <?php $form_url = tinv_url_wishlist( $wishlist['share_key'], $wl_paged, true ); ?>
        <form action="<?php echo esc_url( $form_url ); ?>" method="post" autocomplete="off"
            data-tinvwl_paged="<?php echo $wl_paged; ?>" data-tinvwl_per_page="<?php echo $wl_per_page; ?>"
            data-tinvwl_sharekey="<?php echo $wishlist['share_key'] ?>">
            <?php do_action( 'tinvwl_before_wishlist_table', $wishlist ); ?>
            <table class="tinvwl-table-manage-list">
                <tbody>
                <ul class="prs__list hide-scroll">
                <?php do_action( 'tinvwl_wishlist_contents_before' ); ?>
                <?php

                global $product, $post;
                // store global product data.
                $_product_tmp = $product;
                // store global post data.
                $_post_tmp = $post;
                

                foreach ( $products as $wl_product ) {

                    if ( empty( $wl_product['data'] ) ) {
                        continue;
                    }

                    // override global product data.
                    $product = apply_filters( 'tinvwl_wishlist_item', $wl_product['data'] );
                    // override global post data.
                    $post = get_post( $product->get_id() );

                    unset( $wl_product['data'] );
                    if ( $wl_product['quantity'] > 0 && apply_filters( 'tinvwl_wishlist_item_visible', true, $wl_product, $product ) ) {
                        $product_url = apply_filters( 'tinvwl_wishlist_item_url', $product->get_permalink(), $wl_product, $product );
                        do_action( 'tinvwl_wishlist_row_before', $wl_product, $product );

                        $p_image_url = wp_get_attachment_url( $product->get_image_id());
                        $link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
                        $product_id = $product->get_id();
                        $photos_ids = $product->get_gallery_image_ids();
                        ?>

                        <li class="prs__list-item">
                            <div class="product js-product-<?php echo $product_id; ?>"  data-swiper="false">
                                <?php 
                                    $right_now = date("Y-m-d H:i:s");
                                    $product_date = $product->get_date_created();

                                    if (strtotime('+10 day', strtotime($product_date)) > strtotime($right_now)) {
                                        ?>
                                        <span class="product__tag product__tag--green">
                                            Новинка
                                        </span>
                                        <?php
                                    }
                                ?>

                                <div class="product__swiper-wrap"
                                    onmouseleave="onLeaveProductPhoto(event, 'js-product-<?php echo $product_id; ?>')"
                                    onmousemove="onInitProductByMouse(event, 'js-product-<?php echo $product_id; ?>')"
                                    ontouchstart="onInitProductByTouch(event, 'js-product-<?php echo $product_id; ?>')"
                                    >
                                    <div class="product__swiper js-product-swiper">
                                        <div class='swiper-wrapper' onclick="onProductClick('<?php echo $link;?>')">
                                            <div class='swiper-slide'>
                                                <a href="<?php echo $link;?>" class="product__pic">
                                                    <img src="<?php if ($p_image_url) { echo $p_image_url;} else {echo "https://100nout.by/wp-content/uploads/2023/03/zagl.svg";}?>" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                            <?php
                                            $arrCount = 0;
                                            if($photos_ids) {
                                                foreach ( $photos_ids as $photo_id ) { 
                                                    if ( $arrCount != 0  and $arrCount < 5) {
                                                    ?>
                                                    <div class='swiper-slide'>
                                                        <a href="<?php echo $link;?>" class="product__pic">
                                                            <img src="<?php echo wp_get_attachment_url( $photo_id ); ?>" alt="Фото" class="product__pic-img"
                                                                loading="lazy">
                                                            <div class="swiper-lazy-preloader ">
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <?php
                                                    }
                                                    $arrCount = $arrCount + 1;
                                                }
                                            }?>
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                                <div class="product__center">
                                    <a href="<?php echo $link;?>" class="product__title">
                                        <?php echo $product->get_name();?>
                                    </a>
                                    <?php 
                                        $status = $product->get_status();

                                        
                                    ?>
                                    <p class="product__status <?php if ($status == 'publish') {echo 'product__status--in-stock';}?>">
                                        <?php if ($status == 'publish') {echo 'В наличии';} ?>
                                    </p>
                                    <div class="product__info-wrap">
                                        <?php woocommerce_template_loop_add_to_cart(); ?>
                                        <ul class="product__info">
                                            <?php 
                                                if( get_field('harakteristiki', 'option') ): ?>

                                                    <?php while( has_sub_field('harakteristiki', 'option') ): 
                                                        $productAttrCats = get_sub_field('kategoriya_tovarov');
                                                        $currentCat = $product->get_category_ids()[0];

                                                        if (in_array($currentCat, $productAttrCats)) {
                                                            if( get_sub_field('atributy', 'option') ) {
                                                                while( has_sub_field('atributy', 'option') ): ?>
                                                                    <?php
                                                                        $attrSlug = get_sub_field('slug_atributa', 'option');
                                                                        if ($product->get_attribute($attrSlug)) {
                                                                            ?>
                                                                            <li class="product__info-item">
                                                                                <p class="product__info-item-name">
                                                                                    <?php
                                                                                    echo wc_attribute_label( 'pa_'.$attrSlug );?>
                                                                                </p>
                                                                                <p class="product__info-item-val">
                                                                                    <?php echo $product->get_attribute($attrSlug)?>
                                                                                </p>
                                                                            </li>
                                                                            <?php
                                                                        }
                                                                    ?>
                                                                <?php endwhile; 
                                                            }
                                                        }
                                                        ?>                        
                                                    <?php endwhile; ?>
                                                
                                                <?php endif; ?>
                                        </ul>
                                        <div class="product__tags hide-scroll">
                                            <?php
                                                // Product Status
                                                if ($product->get_attribute('sostoyanie') == 'новый') {
                                                    ?>
                                                        <div class="product__tags-label product__tags-label--new">
                                                            Новый
                                                        </div>
                                                    <?php
                                                }

                                                if ($product->get_attribute('sostoyanie') == '5') {
                                                    ?>
                                                        <div class="product__tags-label product__tags-label--new">
                                                            Как новый
                                                        </div>
                                                    <?php
                                                }

                                                if ($product->get_attribute('sostoyanie') == '4' or $product->get_attribute('sostoyanie') == '3') {
                                                    ?>
                                                        <div class="product__tags-label product__tags-label--used">
                                                            Б/У
                                                        </div>
                                                    <?php
                                                }

                                                // Shop status
                                                $in_shop = get_field( 'v_magazine' );
                                                $to_order = get_field('pod_zakaz');

                                                if ( $in_shop >= 1 )  {
                                                ?>
                                                        <div class="product__tags-label product__tags-label--in-stock">
                                                            В магазине
                                                        </div>
                                                <?php
                                                } elseif ( $to_order >= 1 )  {
                                                    ?>
                                                        <div class="product__tags-label product__tags-label--order">
                                                            Под заказ
                                                        </div>
                                                <?php
                                                } else {
                                                    echo '';
                                                }


                                            ?>
                                            <div class="product__tags-label product__tags-label--credit">
                                                В рассрочку
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="product__box">
                                    <div class="product__actions">
                                        <p class="product__price">
                                            <?php echo $product->get_price();?>
                                            <span>BYN</span>
                                        </p>
                                        <p class="product__price-old">
                                            <?php echo $product->get_regular_price();?>
                                        </p>
                                        <?php echo do_shortcode('[br_compare_button]');?>
                                        <style>
                                            .product__btn.active.p::before {
                                                content: "Убрать из избранного";
                                            }
                                            .product__btn.active.p svg path {
                                                stroke: #D72F09;
                                                fill: #D72F09;
                                            }
                                        </style>
                                        <button class="product__btn p btn btn-icon active btn-tooltip" type="submit" name="tinvwl-remove" style="position: relative;"
                                                value="<?php echo esc_attr( $wl_product['ID'] ); ?>"
                                               >
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M4.31802 6.31802C2.56066 8.07538 2.56066 10.9246 4.31802 12.682L12.0001 20.364L19.682 12.682C21.4393 10.9246 21.4393 8.07538 19.682 6.31802C17.9246 4.56066 15.0754 4.56066 13.318 6.31802L12.0001 7.63609L10.682 6.31802C8.92462 4.56066 6.07538 4.56066 4.31802 6.31802Z"
                                                    stroke="#21201F" stroke-width="1.2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php
                        do_action( 'tinvwl_wishlist_row_after', $wl_product, $product );
                    } // End if().
                } // End foreach().
                // restore global product data.
                $product = $_product_tmp;
                // restore global post data.
                $post = $_post_tmp;
                ?>
                <?php do_action( 'tinvwl_wishlist_contents_after' ); ?>
                </ul>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="100">
                        <?php do_action( 'tinvwl_after_wishlist_table', $wishlist ); ?>
                        <?php wp_nonce_field( 'tinvwl_wishlist_owner', 'wishlist_nonce' ); ?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </form>
        <?php do_action( 'tinvwl_after_wishlist', $wishlist ); ?>
        <div class="tinv-lists-nav tinv-wishlist-clear">
            <?php do_action( 'tinvwl_pagenation_wishlist', $wishlist ); ?>
        </div>
    </div>
