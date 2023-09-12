<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>
  <section class="catalog layout">
    <?php echo woocommerce_breadcrumb();?>
    <?php
    if ( woocommerce_product_loop() ) {
    ?>
        <div class="catalog__top">
            <h1 class="catalog__title text-5xl">
                <?php woocommerce_page_title(); ?>
            </h1>
            <span class="catalog__count text-lg">
                <?php woocommerce_result_count();?>
            </span>
        </div>
        <!-- <div class="catalog__banner banner">
            <img class="banner-xl" src="<?php echo nout_image_directory();?>banners/xl1.png" alt="Баннер">
            <img class="banner-l" src="<?php echo nout_image_directory();?>banners/l1.png" alt="Баннер">
            <img class="banner-m" src="<?php echo nout_image_directory();?>banners/m1.png" alt="Баннер">
            <img class="banner-s" src="<?php echo nout_image_directory();?>banners/s1.png" alt="Баннер">
            <img class="banner-xs" src="<?php echo nout_image_directory();?>banners/xs1.png" alt="Баннер">
        </div> -->
        <style>
            .catalog__pre:has(.swiper-button-lock) {
                padding: 0;
            }
        </style>
        <div class="catalog__pre">
            <?php
            $cat = get_term_by( 'slug', get_query_var('product_cat'), 'product_cat' );
            $cat_id = $cat->term_id;
            
            if( get_field('filtry_kategorii', 'term_' . $cat_id) ): ?>
                <div class="catalog__pre-list-wrap js-swiper-pre-list">
                    <div class="swiper-button-prev"></div>
                    <div class="catalog__pre-list swiper-wrapper">
                    <?php while( has_sub_field('filtry_kategorii', 'term_' . $cat_id) ): ?>
                        <div class="catalog__pre-list-item swiper-slide">
                            <a href="<?php the_sub_field('pre-ssylka', 'term_' . $cat_id); ?>" class="catalog__pre-list-item-btn btn btn-secondary">
                                <?php the_sub_field('pre-nazvanie', 'term_' . $cat_id); ?>
                            </a>
                        </div>
                    <?php endwhile; ?>
                    </div>
                    <div class="swiper-button-next"></div>
                </div>
            
            <?php endif; ?>
        </div>
        <input type="radio" class="catalog__mode-input" name="catalog-mode" id="content-base" checked>
        <input type="radio" class="catalog__mode-input" name="catalog-mode" id="content-column">
        <?php
            if ( wc_get_loop_prop( 'total' ) ) {
                ?>
                    <div class="catalog__body">
                        <div class="catalog__filter js-catalog-filter hide-scroll">
                            <button class="catalog__filter-close btn btn-secondary btn-icon js-filter-toggle">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.292893 0.292893C0.683417 -0.0976311 1.31658 -0.0976311 1.70711 0.292893L6 4.58579L10.2929 0.292893C10.6834 -0.0976311 11.3166 -0.0976311 11.7071 0.292893C12.0976 0.683417 12.0976 1.31658 11.7071 1.70711L7.41421 6L11.7071 10.2929C12.0976 10.6834 12.0976 11.3166 11.7071 11.7071C11.3166 12.0976 10.6834 12.0976 10.2929 11.7071L6 7.41421L1.70711 11.7071C1.31658 12.0976 0.683417 12.0976 0.292893 11.7071C-0.0976311 11.3166 -0.0976311 10.6834 0.292893 10.2929L4.58579 6L0.292893 1.70711C-0.0976311 1.31658 -0.0976311 0.683417 0.292893 0.292893Z" fill="#707070"/>
                                </svg>
                            </button>
                            <p class="catalog__filter-title text-3xl">
                                Фильтры
                            </p>
                            <?php echo do_shortcode('[wpf-filters id=1]');?>
                            <button type="button" class="catalog__filter-btn btn btn-primary js-filter-toggle">
                                Применить фильтр
                            </button>
                        </div>
                        <div class="catalog__content">
                            <div class="catalog__content-top">
                                <button class="catalog__btn-filter btn btn-primary js-filter-toggle"><span>Фильтры</span><svg width="20"
                                        height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M14.9993 9.99967L16.666 9.99967M14.9993 9.99967C14.9993 9.0792 14.2532 8.33301 13.3327 8.33301C12.4122 8.33301 11.666 9.0792 11.666 9.99967M14.9993 9.99967C14.9993 10.9201 14.2532 11.6663 13.3327 11.6663C12.4122 11.6663 11.666 10.9201 11.666 9.99967M4.99935 4.99967C4.99935 5.92015 5.74554 6.66634 6.66602 6.66634C7.58649 6.66634 8.33268 5.92015 8.33268 4.99967M4.99935 4.99967C4.99935 4.0792 5.74554 3.33301 6.66602 3.33301C7.58649 3.33301 8.33268 4.0792 8.33268 4.99967M4.99935 4.99967L3.33268 4.99967M8.33268 4.99967L16.666 4.99967M11.666 9.99967L3.33268 9.99967M4.99935 14.9997C4.99935 15.9201 5.74554 16.6663 6.66602 16.6663C7.58649 16.6663 8.33268 15.9201 8.33268 14.9997M4.99935 14.9997C4.99935 14.0792 5.74554 13.333 6.66602 13.333C7.58649 13.333 8.33268 14.0792 8.33268 14.9997M4.99935 14.9997L3.33268 14.9997M8.33268 14.9997L16.666 14.9997"
                                            stroke="white" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <div class="catalog__sort">
                                    <div class="dropdown js-dropdown js-dropdown-wpf">
                                        <button type="button" class="dropdown__btn js-current-sort">
                                            <span class="text-base text">По новизне</span>
                                            <svg class="dropdown__arrow" width="20" height="20" viewBox="0 0 20 20"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M5.29289 7.29289C5.68342 6.90237 6.31658 6.90237 6.70711 7.29289L10 10.5858L13.2929 7.29289C13.6834 6.90237 14.3166 6.90237 14.7071 7.29289C15.0976 7.68342 15.0976 8.31658 14.7071 8.70711L10.7071 12.7071C10.3166 13.0976 9.68342 13.0976 9.29289 12.7071L5.29289 8.70711C4.90237 8.31658 4.90237 7.68342 5.29289 7.29289Z"
                                                    fill="#B8B8B8" />
                                            </svg>
                                        </button>
                                        <ul class="dropdown__list">
                                            <li class="dropdown__list-item">
                                                <button type="button" class="dropdown__list-item-btn text-sm js-select-sort text" data-val="date">
                                                    По новизне
                                                </button>
                                            </li>
                                            <li class="dropdown__list-item">
                                                <button type="button" class="dropdown__list-item-btn text-sm js-select-sort text" data-val="price-desc">
                                                    По цене ↓
                                                </button>
                                            </li>
                                            <li class="dropdown__list-item">
                                                <button type="button" class="dropdown__list-item-btn text-sm js-select-sort text" data-val="price">
                                                    По цене ↑
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="catalog__mode">
                                    <?php 
                                        echo do_shortcode('[pricelist output="pdf" sel_category="'.$cat_id.'" company="100NOUT.BY"]');
                                    ?>
                                    <label for="content-base" class="catalog__mode-btn btn btn-icon btn-secondary">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M5 3C3.89543 3 3 3.89543 3 5V7C3 8.10457 3.89543 9 5 9H7C8.10457 9 9 8.10457 9 7V5C9 3.89543 8.10457 3 7 3H5Z"
                                                fill="#A3A3A3" />
                                            <path
                                                d="M5 11C3.89543 11 3 11.8954 3 13V15C3 16.1046 3.89543 17 5 17H7C8.10457 17 9 16.1046 9 15V13C9 11.8954 8.10457 11 7 11H5Z"
                                                fill="#A3A3A3" />
                                            <path
                                                d="M11 5C11 3.89543 11.8954 3 13 3H15C16.1046 3 17 3.89543 17 5V7C17 8.10457 16.1046 9 15 9H13C11.8954 9 11 8.10457 11 7V5Z"
                                                fill="#A3A3A3" />
                                            <path
                                                d="M11 13C11 11.8954 11.8954 11 13 11H15C16.1046 11 17 11.8954 17 13V15C17 16.1046 16.1046 17 15 17H13C11.8954 17 11 16.1046 11 15V13Z"
                                                fill="#A3A3A3" />
                                        </svg>
                                    </label>
                                    <label for="content-column" class="catalog__mode-btn btn btn-icon btn-secondary">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M3 4C3 3.44772 3.44772 3 4 3H16C16.5523 3 17 3.44772 17 4C17 4.55228 16.5523 5 16 5H4C3.44772 5 3 4.55228 3 4ZM3 8C3 7.44772 3.44772 7 4 7H16C16.5523 7 17 7.44772 17 8C17 8.55228 16.5523 9 16 9H4C3.44772 9 3 8.55228 3 8ZM3 12C3 11.4477 3.44772 11 4 11H16C16.5523 11 17 11.4477 17 12C17 12.5523 16.5523 13 16 13H4C3.44772 13 3 12.5523 3 12ZM3 16C3 15.4477 3.44772 15 4 15H16C16.5523 15 17 15.4477 17 16C17 16.5523 16.5523 17 16 17H4C3.44772 17 3 16.5523 3 16Z"
                                                fill="#A3A3A3" />
                                        </svg>
                                    </label>
                                </div>
                            </div>
                            <ul class="catalog__list">
                                <?php
                                    while ( have_posts() ) {
                                        the_post();

                                        do_action( 'woocommerce_shop_loop' );

                                        wc_get_template_part( 'content', 'product' );
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="catalog__content-desc text-base">
                        <?php
                            $prod_term    =    get_term($cat_id,'product_cat');
                            $description=    $prod_term->description;
                            echo $description;
                        ?>                  
                    </div>
                <?php
            }
        ?>
    <?php

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}


/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
// do_action( 'woocommerce_after_main_content' );
?>
</section>

<? get_footer( 'shop' );
