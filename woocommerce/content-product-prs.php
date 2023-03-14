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

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
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
        <!-- onclick="location.href='<?php echo $link;?>'" -->
        <div class="product__swiper-wrap"
            onmouseleave="onLeaveProductPhoto(event, 'js-product-<?php echo $product_id; ?>')"
            onmousemove="onInitProductByMouse(event, 'js-product-<?php echo $product_id; ?>')"
            ontouchstart="onInitProductByTouch(event, 'js-product-<?php echo $product_id; ?>')"
            >
            <div class="product__swiper js-product-swiper">
                <div class='swiper-wrapper'>
                    <div class='swiper-slide'>
                        <a href="<?php echo $link;?>" class="product__pic">
                            <img src="<?php if ($p_image_url) { echo $p_image_url;} else {echo "https://100nout.redheady.ru/wp-content/uploads/2023/03/zagl.svg";}?>" alt="Фото" class="product__pic-img"
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
                        if ($product->get_attribute('diagonal-ekrana')) {
                            ?>
                             <li class="product__info-item">
                                <p class="product__info-item-name">
                                    Диагональ экрана:
                                </p>
                                <p class="product__info-item-val">
                                    <?php echo $product->get_attribute('diagonal-ekrana')?>
                                </p>
                            </li>
                            <?php
                        }
                        if ($product->get_attribute('model-proczessora')) {
                            ?>
                             <li class="product__info-item">
                                <p class="product__info-item-name">
                                    Модель процессора:
                                </p>
                                <p class="product__info-item-val">
                                    <?php echo $product->get_attribute('model-proczessora')?>
                                </p>
                            </li>
                            <?php
                        }
                        if ($product->get_attribute('razreshenie-ekrana')) {
                            ?>
                             <li class="product__info-item">
                                <p class="product__info-item-name">
                                    Разрешение экрана:
                                </p>
                                <p class="product__info-item-val">
                                    <?php echo $product->get_attribute('razreshenie-ekrana')?>
                                </p>
                            </li>
                            <?php
                        }
                        if ($product->get_attribute('graficheskij-adapter')) {
                            ?>
                             <li class="product__info-item">
                                <p class="product__info-item-name">
                                    Графический адаптер:
                                </p>
                                <p class="product__info-item-val">
                                    <?php echo $product->get_attribute('graficheskij-adapter')?>
                                </p>
                            </li>
                            <?php
                        }
                        if ($product->get_attribute('operativnaya-pamyat')) {
                            ?>
                             <li class="product__info-item">
                                <p class="product__info-item-name">
                                    Оперативная память, ГБ:
                                </p>
                                <p class="product__info-item-val">
                                    <?php echo $product->get_attribute('operativnaya-pamyat')?>
                                </p>
                            </li>
                            <?php
                        }
                    ?>
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
                <button class="product__btn btn btn-icon btn-tooltip active"
                    name="Добавить в сравнение">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 6H20M4 12H20M4 18H11" stroke="#21201F" stroke-width="1.2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button class="product__btn btn btn-icon btn-tooltip active"
                    name="Добавить в избранное">
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
