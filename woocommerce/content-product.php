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

defined( 'ABSPATH' ) || exit;

global $product;
$p_image_url = wp_get_attachment_url( $product->get_image_id());
$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
$product_id = $product->get_id();
$photos_ids = $product->get_gallery_image_ids();

$productNameSost = "";
if ($product->get_attribute('sostoyanie') == "новый") {
    $productNameSost = "Новый ".$product->get_name()."";
} else if ($product->get_attribute('sostoyanie') == "5") {
    $productNameSost = "".$product->get_name().", отличное состояние";
} else {
    $productNameSost = "Б/у ".$product->get_name()."";
}

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
 <li class="catalog__list-item">
    <div 
    class="product js-product-<?php echo $product_id; ?>" 
    data-swiper="false"
    >
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
                            <img src="<?php if ($p_image_url) { echo $p_image_url;} else {echo "https://100nout.by/wp-content/uploads/2023/03/zagl.png";}?>" class="product__pic-img"
                                loading="lazy">
                            <div class="swiper-lazy-preloader ">
                            </div>
                        </a>
                    </div>
                    <?php
                    $arrCount = 0;
                    if($photos_ids) {
                        foreach ( $photos_ids as $photo_id ) { 
                            if ( $arrCount != 0 and $arrCount < 5) {
                            ?>
                            <div class='swiper-slide'>
                                <a href="<?php echo $link;?>" class="product__pic">
                                    <img src="<?php echo wp_get_attachment_url( $photo_id ); ?>" class="product__pic-img"
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
                <?php echo $productNameSost;?>
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
                <?php 
                        if ($product->get_price()) {
                            echo number_format($product->get_price(), 0, '', ' ');
                        }?>
                    <span>BYN</span>
                </p>
                <p class="product__price-old">
                    <?php echo $product->get_regular_price();?>
                </p>
                <?php echo do_shortcode('[br_compare_button]');?>
                <?php echo do_shortcode('[ti_wishlists_addtowishlist]');?>
            </div>
        </div>
    </div>
</li>
