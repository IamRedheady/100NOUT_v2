<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
// do_action( 'woocommerce_before_single_product' );
if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}

$cat = $product->get_category_ids();
$category_link = get_category_link( $cat[0] );
$ancestors = get_ancestors( $cat[0], 'product_cat' );

$productNameSost = "";
if ($product->get_attribute('sostoyanie') == "новый") {
    $productNameSost = "Новый ".$product->get_name()."";
} else if ($product->get_attribute('sostoyanie') == "5") {
    $productNameSost = "".$product->get_name().", отличное состояние";
} else {
    $productNameSost = "Б/у ".$product->get_name()."";
}

$status = $product->get_status();
$photos_ids = $product->get_gallery_image_ids();
$p_image_url = wp_get_attachment_url( $product->get_image_id());

// $catss = $product->get_categories();

// $childrenCats = get_term_children( $ancestors[0],  'product_cat');
// foreach ( $childrenCats as $child ) {
// 	echo $child->name;
// }
// echo $catss;
// echo $ancestors[0];
?>

<div class="prp__header js-prp-header">
    <div class="layout prp__header-inner">
        <div class="prp__header-pic">
            <img src="<?php if ($p_image_url) { echo $p_image_url;} else {echo "https://100nout.by/wp-content/uploads/2023/03/zagl.png";}?>" alt="Фото">
        </div>
        <div class="prp__header-content">
            <h3 class="prp__header-title text-sm">
                <?php echo $productNameSost; ?>
            </h3>
            <p class="text-sm prp__header-status">В наличии</p>
        </div>
        <p class="prp__price text text-3xl">
        <?php echo number_format($product->get_price(), 0, '', ' ');?><span class="prp__price-currency text-xs">BYN</span>
        </p>
        <p class="prp__price-old text-xs">
            <?php echo $product->get_regular_price();?>
        </p>
        <?php woocommerce_template_loop_add_to_cart(); ?>
    </div>
</div>
<section class="prp layout">
    <div class="prp__big-slider-wrap js-prp-big-slider-wrap">
        <button class="btn btn-icon prp__big-slider-close js-prp-slider-toggle">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M1.15225 1.15128C1.62088 0.682647 2.38068 0.682647 2.84931 1.15128L8.00078 6.30275L13.1523 1.15128C13.6209 0.682647 14.3807 0.682647 14.8493 1.15128C15.3179 1.61991 15.3179 2.3797 14.8493 2.84833L9.69784 7.9998L14.8493 13.1513C15.3179 13.6199 15.3179 14.3797 14.8493 14.8483C14.3807 15.317 13.6209 15.317 13.1523 14.8483L8.00078 9.69686L2.84931 14.8483C2.38068 15.317 1.62088 15.317 1.15225 14.8483C0.683624 14.3797 0.683624 13.6199 1.15225 13.1513L6.30373 7.9998L1.15225 2.84833C0.683624 2.3797 0.683624 1.61991 1.15225 1.15128Z"
                    fill="#474747" />
            </svg>
        </button>
        <div class="prp__big-slider js-prp-slider-big">
            <div class='swiper-wrapper'>
                <?php  if($photos_ids) {?>
                    <?php foreach ( $photos_ids as $photo_id ) { ?>
                        <div class='swiper-slide'>
                            <div class="prp__big-slider-pic">
                                <div class="swiper-zoom-container" style="background: url('<?php echo wp_get_attachment_url( $photo_id ); ?>') 10000% -10000% / 130% no-repeat;">
                                    <img src="<?php echo wp_get_attachment_url( $photo_id ); ?>" alt="<?php echo $product->get_name(); ?>">
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class='swiper-slide'>
                        <img src="https://100nout.by/wp-content/uploads/2023/03/zagl.png" alt="Здесь ДОЛЖНО быть фото товара...">
                    </div>
                <?php }?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
        <div thumbsSlider="" class="prp__big-slider-thumbs js-prp-thumbs-big">
            <div class='swiper-wrapper'>
                <?php  if($photos_ids) {?>
                    <?php foreach ( $photos_ids as $photo_id ) { ?>
                        <div class='swiper-slide'>
                            <img src="<?php echo wp_get_attachment_url( $photo_id ); ?>" alt="<?php echo $product->get_name(); ?>">
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class='swiper-slide'>
                        <img src="https://100nout.by/wp-content/uploads/2023/03/zagl.png " alt="Здесь ДОЛЖНО быть фото товара...">
                    </div>
                <?php }?>
            </div>
        </div>
    </div>
    <?php echo woocommerce_breadcrumb();?>
    <h1 class="prp__title text-3xl">
    <?php echo $productNameSost; ?>
    </h1>
    <p class="prp__subtitle">
        <span class="text-sm prp__subtitle-status"><?php if ($status == 'publish') {echo 'В наличии';} ?></span>
        <span class="text-sm prp__subtitle-code-name">Код товара:</span>
        <span class="text-sm prp__subtitle-code-val"><?php echo $product->get_sku(); ?></span>
    </p>
    <div class="prp__intro">
        <div class="prp__slider-wrap">
            <div thumbsSlider="" class="prp__thumbs js-prp-thumbs">
                <div class='swiper-wrapper'>
                    <?php  if($photos_ids) {?>
                        <?php foreach ( $photos_ids as $photo_id ) { ?>
                            <div class='swiper-slide'>
                                <img src="<?php echo wp_get_attachment_url( $photo_id ); ?>" alt="<?php echo $product->get_name(); ?>">
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class='swiper-slide'>
                            <img src="https://100nout.by/wp-content/uploads/2023/03/zagl.png " alt="Здесь ДОЛЖНО быть фото товара...">
                        </div>
                    <?php }?>
                </div>
            </div>
            <div class="prp__slider js-prp-slider">
                <div class='swiper-wrapper'>
                    <?php  if($photos_ids) {?>
                        <?php foreach ( $photos_ids as $photo_id ) { ?>
                            <div class='swiper-slide js-prp-slider-toggle'>
                                <img src="<?php echo wp_get_attachment_url( $photo_id ); ?>" alt="<?php echo $product->get_name(); ?>">
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class='swiper-slide'>
                            <img src="https://100nout.by/wp-content/uploads/2023/03/zagl.png " alt="Здесь ДОЛЖНО быть фото товара...">
                        </div>
                    <?php }?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
        <div class="prp__content">
            <div class="prp__content-center">
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
                <h3 class="text-lg text--semibold">Основные характеристики</h3>
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
                <a href="#chars" class="link link-primary text-sm">
                    Все характеристики
                </a>
                <div class="prp__card">
                    <p class="text-lg text--semibold">
                        Состояние
                        <span class="text text-xs"><?php
                             echo $product->get_attribute('sostoyanie');
                        ?></span>
                    </p>
                    <div class="prp__state">
                        <div class="prp__state-line" style="width: <?php
                            // Product Status
                        if ($product->get_attribute('sostoyanie') == 'новый') {
                            ?>100<?php
                        }

                        if ($product->get_attribute('sostoyanie') == '5') {
                            ?>90<?php
                        }

                        if ($product->get_attribute('sostoyanie') == '4' or $product->get_attribute('sostoyanie') == '3') {
                            ?>80<?php
                        }
                        ?>%;"></div>
                    </div>
                    <p class="text text-xs">
                        <?php
                             echo $product->get_attribute('vneshnij-vid');
                        ?></p>
                </div>
            </div>
            <div class="prp__content-more">
                <div class="prp__content-more-card">
                    <div class="prp__content-more-top">
                        <p class="prp__price text text-3xl">
                        <?php echo number_format($product->get_price(), 0, '', ' ');?><span class="prp__price-currency text-xs">BYN</span>
                        </p>
                        <p class="prp__price-old text-xs">
                            <?php echo $product->get_regular_price();?>
                        </p>
                        <?php echo do_shortcode('[ti_wishlists_addtowishlist]');?>
                    </div>
                    <?php woocommerce_template_loop_add_to_cart(); ?>
                </div>
                <div class="prp__content-more-card">
                    <div class="prp__content-more-card-inner">
                        <a href="/dostavka/" class="link link-primary text-lg">
                            Доставка
                        </a>
                        <?php 
                            $todayH = date("H") + 3;
                        ?>
                        <ul class="prp__content-more-list">
                            <li class="prp__content-more-list-item">
                                <p class="text text-sm text--semibold">Забрать в магазине</p>
                                <p class="text text-xs">Минск — сегодня, до 20:00</p>
                            </li>
                            <li class="prp__content-more-list-item">
                                <p class="text text-sm text--semibold">Доставка курьером</p>
                                <p class="text text-xs">Минск — 
                                    <?php
                                        if ($todayH < 16) {
                                            ?>
                                                Сегодня, с 18:00 до 21:00
                                            <?php
                                        } else {
                                            ?>
                                                завтра, до 21:00
                                            <?php
                                        }
                                    ?></p>
                            </li>
                            <li class="prp__content-more-list-item">
                                <p class="text text-sm text--semibold">Доставка по Беларуси</p>
                                <p class="text text-xs">
                                    <?php
                                        $arr = [
                                            'января',
                                            'февраля',
                                            'марта',
                                            'апреля',
                                            'мая',
                                            'июня',
                                            'июля',
                                            'августа',
                                            'сентября',
                                            'октября',
                                            'ноября',
                                            'декабря'
                                        ];

                                        $d = strtotime("+2 day");
                                        $todayM = date("n", $d);
                                        $nextDay = date("d", $d);
                                    ?>
                                Курьером до <?php 
                                echo $nextDay;
                                echo " ";
                                echo $arr[$todayM - 1];?></p>
                            </li>
                        </ul>
                    </div>
                    <div class="prp__content-more-card-inner">
                        <a href="/oplata/" class="link link-primary text-lg">
                            Оплата
                        </a>
                        <ul class="prp__content-more-list">
                            <li class="prp__content-more-list-item">
                                <p class="text text-xs">Наличный расчёт</p>
                            </li>
                            <li class="prp__content-more-list-item">
                                <p class="text text-xs">Безналичный расчёт</p>
                            </li>
                            <li class="prp__content-more-list-item">
                                <p class="text text-xs">Оплата картами рассрочки</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="prp__tabs" id="chars">
        <?php 
            if ($product->get_description()) {
                ?>
                 <input id="prp1" type="radio" name="prp-tabs" checked>
                 <input id="prp2" type="radio" name="prp-tabs">
                <?php
            } else {
                ?>
                 <input id="prp2" type="radio" name="prp-tabs" checked>
                <?php
            }

            if ($ancestors[0] == 2309) {
                ?>
                 <input id="prp4" type="radio" name="prp-tabs">
                <?php
            }
        ?>
        <input id="prp3" type="radio" name="prp-tabs">
        <div class="prp__tabs-inner hide-scroll">
        <?php 
            if ($product->get_description()) {
                ?>
                 <label for="prp1" class="prp__tabs-label prp__tabs-label-1">
                    О товаре
                </label>
                <?php
            }
        ?>
            <label for="prp2" class="prp__tabs-label prp__tabs-label-2">
                Характеристики
            </label>
            <label for="prp3" class="prp__tabs-label prp__tabs-label-3">
                Отзывы о магазине
            </label>
            <?php 
                 if ($ancestors[0] == 2309) {
                    ?>
                    <label for="prp4" class="prp__tabs-label prp__tabs-label-4">
                        Аксессуары
                    </label>    
                    <?php
                 }
            ?>
        </div>
        <?php 
            if ($product->get_description()) {
                ?>
                    <div class="prp__tabs-body prp__tabs-body-1">
                        <div class="text text-base">
                            <?php 
                                echo $product->get_description();
                            ?>
                        </div>
                    </div>
                <?php
            }
        ?>
        <div class="prp__tabs-body prp__tabs-body-2">
            <?php echo do_shortcode( '[woocommerce_group_attributes_table]' );?>
        </div>
        <div class="prp__tabs-body prp__tabs-body-3">
        <div class="reviews__body">
        <div class="reviews__column">
            <ul class="reviews__list">
                <?php

                /*
                *  Перебираем Повторитель
                */

                if( get_field('otzyvy-ya', 62) ): ?>

                    <?php while( has_sub_field('otzyvy-ya', 62) ): ?>
                        <li class="reviews__list-item">
                            <div class="card reviews__card">
                                <div class="reviews__list-item-pic">
                                    <img src="<?php the_sub_field('otzyvy-ya-izobrazhenie', 62); ?>" alt="Фото"
                                        class="reviews__list-item-pic-img">
                                </div>
                                <div class="reviews__content">
                                    <h4 class="reviews__content-title text-2xl">
                                        <?php the_sub_field('otzyvy-ya-imya_i_familiya', 62); ?>
                                    </h4>
                                    <p class="text-base">
                                        <?php the_sub_field('otzyvy-ya-text', 62); ?>
                                    </p>
                                </div>
                            </div>
                        </li>
                    <?php endwhile; ?>

                <?php endif; ?>
            </ul>
            <a class="btn btn-primary reviews__btn" href="<?php the_field('ssylka_na_vse_otzyvy-ya', 62);?>">Больше отзывов на Яндекс Картах</a>
        </div>
        <div class="reviews__column">
            <ul class="reviews__list">
                <?php
                    if( get_field('otzyvy-go', 62) ): ?>

                        <?php while( has_sub_field('otzyvy-go', 62) ): ?>
                            <li class="reviews__list-item">
                                <div class="card reviews__card">
                                    <div class="reviews__list-item-pic">
                                        <img src="<?php the_sub_field('otzyvy-go-izobrazhenie', 62); ?>" alt="Фото"
                                            class="reviews__list-item-pic-img">
                                    </div>
                                    <div class="reviews__content">
                                        <h4 class="reviews__content-title text-2xl">
                                            <?php the_sub_field('otzyvy-go-imya_i_familiya', 62); ?>
                                        </h4>
                                        <p class="text-base">
                                            <?php the_sub_field('otzyvy-go-text', 62); ?>
                                        </p>
                                    </div>
                                </div>
                            </li>
                        <?php endwhile; ?>

                    <?php endif; ?>
            </ul>
            <a class="btn btn-primary reviews__btn" href="<?php the_field('ssylka_na_vse_otzyvy-go', 62);?>">Смотреть все отзывы в Google</a>
        </div>
    </div>
        </div>
        <?php
             if ($ancestors[0] == 2309) {
                ?>
                    <div class="prp__tabs-body prp__tabs-body-4">
                        <div class="prs">
                            <h2 class="prs__title text-3xl" style="margin: 24px 0;">
                                С этим товаром часто покупают
                            </h2>
                            <ul class="prs__list hide-scroll">
                                <?php 
                                    nout_show_products_by_id(2330);
                                    ?>
                            </ul>
                        </div>
                    </div>
                <?php
             }
        ?>
    </div>
    <div class="prs prp__cats">
        <h2 class="prs__title text-3xl" style="margin: 24px 0;">
            Похожие товары
        </h2>
        <ul class="prs__list prs__list-2334 hide-scroll">
            <?php 
                nout_show_products_by_id($cat[0]);
                ?>
        </ul>
    </div>
        <?php 
            $args = array(
                'taxonomy'     => 'product_cat',
                'child_of'     => 0,
                'parent'       => $ancestors[0],
                'orderby'      => 'name',
                'order'        => 'ASC',
                'hide_empty'   => 1,
                'hierarchical' => 1,
                // 'number'       => 0, // сколько выводить?
                // полный список параметров смотрите в описании функции http://wp-kama.ru/function/get_terms
            );
        
            $categories = get_categories( $args );
            if( $categories ){
                ?>
                <div class="prp__cats">
                    <h3 class="prp__cats-title text-3xl">
                        Подборки товаров в категории <?php echo single_cat_title();?>
                    </h3>
                    <ul class="prp__cats-list hide-scroll">
                        <?php
                            foreach( $categories as $sub_cat ){
                            ?>
                                <li class="prp__cats-list-item">
                                    <a href="<?php echo get_category_link( $sub_cat->term_id );?>" type="button" class="prp__cats-list-item-btn btn btn-secondary">
                                        <?php echo $sub_cat->name;?>
                                    </a>
                                </li>
                            <?php
                            }
                        ?>
                    </ul>
                </div>
                <?php
            }
        ?>
    
        <?php 
            echo nout_recently_viewed_products();
            ?>
       
</section>
<script>
   document.querySelectorAll('a[href^="#"').forEach(link => {

link.addEventListener('click', function(e) {
    e.preventDefault();

    let href = this.getAttribute('href').substring(1);

    const scrollTarget = document.getElementById(href);

    // const topOffset = document.querySelector('.prp__tabs').offsetHeight;
    const topOffset = 100; // если не нужен отступ сверху 
    const elementPosition = scrollTarget.getBoundingClientRect().top;
    const offsetPosition = elementPosition - topOffset;

    window.scrollBy({
        top: offsetPosition,
        behavior: 'smooth'
    });
});
});
</script>


