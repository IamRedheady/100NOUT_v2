<?php

/**
 * nout functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package nout
 */

if (!defined('_S_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', '1.1.2');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function nout_setup()
{
    /*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on nout, use a find and replace
		* to change 'nout' to the name of your theme in all the template files.
		*/
    load_theme_textdomain('nout', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
    add_theme_support('title-tag');

    /*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
    add_theme_support('post-thumbnails');

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(
        array(
            'menu-1' => esc_html__('Primary', 'nout'),
        )
    );

    /*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Set up the WordPress core custom background feature.
    add_theme_support(
        'custom-background',
        apply_filters(
            'nout_custom_background_args',
            array(
                'default-color' => 'ffffff',
                'default-image' => '',
            )
        )
    );

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Add support for core custom logo.
     *
     * @link https://codex.wordpress.org/Theme_Logo
     */
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );
}
add_action('after_setup_theme', 'nout_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function nout_content_width()
{
    $GLOBALS['content_width'] = apply_filters('nout_content_width', 640);
}
add_action('after_setup_theme', 'nout_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function nout_widgets_init()
{
    register_sidebar(
        array(
            'name'          => esc_html__('Sidebar', 'nout'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('Add widgets here.', 'nout'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action('widgets_init', 'nout_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function nout_scripts()
{
    // Получаем путь до файлов
    $style_file = get_template_directory() . '/dist/css/app.css';
    $script_file = get_template_directory() . '/dist/js/app.js';

    // Используем filemtime() для получения времени изменения файла
    $style_version = file_exists($style_file) ? filemtime($style_file) : false;
    $script_version = file_exists($script_file) ? filemtime($script_file) : false;

    // Подключаем стили и скрипты с уникальной версией
    wp_enqueue_style('nout-style', get_template_directory_uri() . '/dist/css/app.css', array(), $style_version);
    wp_enqueue_script('nout-js', get_template_directory_uri() . '/dist/js/app.js', array(), $script_version, true);

    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wc-blocks-vendors-style');
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('font-awesome');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('ct_public_css');
    wp_dequeue_style('contact-form-7');
}
add_action('wp_enqueue_scripts', 'nout_scripts');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if (class_exists('WooCommerce')) {
    require get_template_directory() . '/inc/woocommerce.php';
}

// ––––––––––––––––––––
// Custom Functions
// ––––––––––––––––––––

// Get image link from theme
function nout_image_directory()
{
    echo get_template_directory_uri() . '/dist/images/';
}

if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title'    => 'Общие',
        'menu_title'    => 'Общие',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}

// Menu And Navigation
function nout_menu_top_level()
{
    $cats = get_field('kategorii', 'option');
    foreach ($cats as $cat) {
?>
        <li class="menu__list-item">
            <a href="<?php echo get_category_link($cat->term_id); ?>" class="menu__list-item-btn text text-sm text--semibold btn js-menu-level-1">
                <?php echo $cat->name; ?>
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.25 2.91683L9.33333 7.00016L5.25 11.0835" stroke="#DEDEDE" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </li>
    <?php
    }
}

function nout_menu_second_level()
{
    $cats = get_field('kategorii', 'option');
    foreach ($cats as $cat) {
    ?>
        <div class="menu__sub js-menu-sub-level-1">
            <div class="menu__sub-title-wrap">
                <button class="btn btn-icon js-menu-title-1">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.70711 14.7071C9.31658 15.0976 8.68342 15.0976 8.29289 14.7071L4.29289 10.7071C3.90237 10.3166 3.90237 9.68342 4.29289 9.29289L8.29289 5.29289C8.68342 4.90237 9.31658 4.90237 9.70711 5.29289C10.0976 5.68342 10.0976 6.31658 9.70711 6.70711L7.41421 9L15 9C15.5523 9 16 9.44772 16 10C16 10.5523 15.5523 11 15 11H7.41421L9.70711 13.2929C10.0976 13.6834 10.0976 14.3166 9.70711 14.7071Z" fill="#21201F" />
                    </svg>
                </button>
                <a href="<?php echo get_category_link($cat->term_id); ?>" class="menu__sub-title text text-sm text--semibold">
                    <?php echo $cat->name; ?>
                </a>
            </div>
            <ul class="menu__sub-list">
                <?php
                $args = array(
                    'taxonomy'     => 'product_cat',
                    'child_of'     => 0,
                    'parent'       => $cat->term_id,
                    'orderby'      => 'name',
                    'order'        => 'ASC',
                    'hide_empty'   => 1,
                    'hierarchical' => 1,
                    // 'number'       => 0, // сколько выводить?
                    // полный список параметров смотрите в описании функции http://wp-kama.ru/function/get_terms
                );

                $categories = get_categories($args);
                if ($categories) {
                    foreach ($categories as $sub_cat) {
                ?>
                        <li class="menu__sub-list-item">
                            <a href="<?php echo get_category_link($sub_cat->term_id); ?>" class="menu__list-item-btn text text-sm text--semibold btn js-menu-level-2">
                                <?php echo $sub_cat->name; ?>
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.25 2.91683L9.33333 7.00016L5.25 11.0835" stroke="#DEDEDE" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </li>
                <?php
                    }
                }
                ?>
            </ul>
        </div>
        <?php
    }
}

function nout_menu_third_level()
{
    $cats = get_field('kategorii', 'option');
    foreach ($cats as $cat) {
        $args = array(
            'taxonomy'     => 'product_cat',
            'child_of'     => 0,
            'parent'       => $cat->term_id,
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 1,
            'hierarchical' => 1,
        );

        $categories = get_categories($args);
        if ($categories) {
            foreach ($categories as $sub_cat) {
        ?>
                <div class="menu__sub js-menu-sub-level-2">
                    <div class="menu__sub-title-wrap">
                        <button class="btn btn-icon js-menu-title-2">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.70711 14.7071C9.31658 15.0976 8.68342 15.0976 8.29289 14.7071L4.29289 10.7071C3.90237 10.3166 3.90237 9.68342 4.29289 9.29289L8.29289 5.29289C8.68342 4.90237 9.31658 4.90237 9.70711 5.29289C10.0976 5.68342 10.0976 6.31658 9.70711 6.70711L7.41421 9L15 9C15.5523 9 16 9.44772 16 10C16 10.5523 15.5523 11 15 11H7.41421L9.70711 13.2929C10.0976 13.6834 10.0976 14.3166 9.70711 14.7071Z" fill="#21201F" />
                            </svg>
                        </button>
                        <a href="<?php echo get_category_link($sub_cat->term_id); ?>" class="menu__sub-title text text-sm text--semibold">
                            <?php echo $sub_cat->name; ?>
                        </a>
                    </div>
                    <ul class="menu__sub-list">
                        <?php
                        $sub_args = array(
                            'taxonomy'     => 'product_cat',
                            'child_of'     => 0,
                            'parent'       => $sub_cat->term_id,
                            'orderby'      => 'name',
                            'order'        => 'ASC',
                            'hide_empty'   => 1,
                            'hierarchical' => 1,
                        );

                        $sub_categories = get_categories($sub_args);
                        if ($sub_categories) {
                            foreach ($sub_categories as $sub_2_cat) {
                        ?>
                                <li class="menu__sub-list-item">
                                    <a href="<?php echo get_category_link($sub_2_cat->term_id); ?>" class="menu__list-item-btn text text-sm text--semibold btn">
                                        <?php echo $sub_2_cat->name; ?>
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.25 2.91683L9.33333 7.00016L5.25 11.0835" stroke="#DEDEDE" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </li>
                        <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
        <?php
            }
        }
    }
}

// Смена валюты на BYN
add_filter('woocommerce_currencies', 'add_my_currency');

function add_my_currency($currencies)
{
    $currencies['BYN'] = __('Бел. руб.', 'woocommerce');
    return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_my_currency_symbol', 10, 2);

function add_my_currency_symbol($currency_symbol, $currency)
{
    switch ($currency) {
        case 'BYN':
            $currency_symbol = 'BYN';
            break;
    }
    return $currency_symbol;
}

// Вывод товаров по ID категори
function nout_show_products_by_id($cat_id)
{
    $args = array(
        'posts_per_page' => 8,
        'post_type' => 'product',
        'orderby' => 'rand',
        'order'   => 'DESC',
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'id',
                'terms'    => $cat_id,
            ),
        ),
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '='
            ),
            //если есть фото
            array(
                'key' => '_thumbnail_id'
            ),
        )
    );
    $wc_query = new WP_Query($args);
    if ($wc_query->have_posts()) {
        while ($wc_query->have_posts()) {
            $wc_query->the_post();

            wc_get_template_part('content', 'product-prs');
        }
    }
    wp_reset_postdata();
}

// Вывод товаров по ID категории в Мини коризне
function nout_show_products_by_id_mini_cart($cat_id)
{
    $args = array(
        'posts_per_page' => 3,
        'post_type' => 'product',
        'orderby' => 'rand',
        'order'   => 'DESC',
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'id',
                'terms'    => $cat_id,
            ),
        ),
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '='
            ),
            //если есть фото
            array(
                'key' => '_thumbnail_id'
            ),
        )
    );
    $wc_query = new WP_Query($args);
    if ($wc_query->have_posts()) {
        while ($wc_query->have_posts()) {
            $wc_query->the_post();

            wc_get_template_part('content', 'product-prs-mini-cart');
        }
    }
    wp_reset_postdata();
}

// Вывод новых товаров
function nout_show_products_new()
{
    $args = array(
        'posts_per_page' => 4,
        'post_type' => 'product',
        'orderby' => 'rand',
        'order'   => 'DESC',
        'post_status' => 'publish',
        'date_query'  => array(
            array(
                'after' => '10 days ago',
            ),
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'id',
                'terms'    => 2334,
                'operator' => 'NOT IN'
            ),
        ),
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '='
            ),
            //если есть фото
            array(
                'key' => '_thumbnail_id'
            ),
        )
    );
    $wc_query = new WP_Query($args);
    if ($wc_query->have_posts()) {
        ?>
        <section class="prs layout">
            <h2 class="prs__title text-4xl">
                Новинки
            </h2>
            <ul class="prs__list prs__list-2334 hide-scroll">
                <?php
                while ($wc_query->have_posts()) {
                    $wc_query->the_post();

                    wc_get_template_part('content', 'product-prs');
                }
                ?>
            </ul>
        </section>
    <?php
    }
    wp_reset_postdata();
}

// Вывод рекомендуемых товаров
function nout_show_products_related()
{
    $args = array(
        'posts_per_page' => 8,
        'post_type' => 'product',
        'orderby' => 'rand',
        'order'   => 'DESC',
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
                'operator' => 'IN'
            ),
        ),
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '='
            ),
            //если есть фото
            array(
                'key' => '_thumbnail_id'
            ),
        )
    );
    $wc_query = new WP_Query($args);
    if ($wc_query->have_posts()) {
        while ($wc_query->have_posts()) {
            $wc_query->the_post();

            wc_get_template_part('content', 'product-prs');
        }
    }
    wp_reset_postdata();
}

// Переподключение скриптов для Ajax кнопки доабвить/удалить товар из корзины
function custom_wp_enqueue_scripts()
{
    wp_deregister_script('wc-add-to-cart');
    wp_register_script('wc-add-to-cart', get_template_directory_uri() . '/js/woocommerce/add-to-cart.js', array('jquery'), WC_VERSION, TRUE);
    wp_enqueue_script('wc-add-to-cart');
}
add_action('wp_enqueue_scripts', 'custom_wp_enqueue_scripts', 99);


// Remove product in the cart using ajax
function warp_ajax_product_remove()
{
    // Get mini cart
    ob_start();

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['product_id'] == $_POST['product_id'] && $cart_item_key == $_POST['cart_item_key']) {
            WC()->cart->remove_cart_item($cart_item_key);
        }
    }

    WC()->cart->calculate_totals();
    WC()->cart->maybe_set_cart_cookies();

    woocommerce_mini_cart();

    $mini_cart = ob_get_clean();

    // Fragments and mini cart are returned
    $data = array(
        'fragments' => apply_filters(
            'woocommerce_add_to_cart_fragments',
            array(
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
            )
        ),
        'cart_hash' => apply_filters('woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5(json_encode(WC()->cart->get_cart_for_session())) : '', WC()->cart->get_cart_for_session())
    );

    wp_send_json($data);

    die();
}

add_action('wp_ajax_product_remove', 'warp_ajax_product_remove');
add_action('wp_ajax_nopriv_product_remove', 'warp_ajax_product_remove');

function get_custom_cart_total()
{
    // Получаем текущую корзину
    $cart = WC()->cart;
    $cart_total = 0;

    if (!$cart) {
        return $cart_total;
    }

    // Получаем выбранный способ оплаты из сессии
    $payment_method = WC()->session->get('custom_payment');
    $specific_payment_method = '3'; // ID нужного способа оплаты
    $specific_payment_method_2 = '1';

    // Проверяем способ оплаты
    if ($payment_method === $specific_payment_method || $payment_method === $specific_payment_method_2) {
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            $quantity = $cart_item['quantity'];

            // Проверяем, относится ли продукт к категории, которую нужно исключить
            if (has_term(2992, 'product_cat', $product->get_id())) {
                // Пропускаем этот товар, как будто он удалён
                continue;
            }

            // Убираем скидочную цену для остальных товаров
            if ($product->is_on_sale()) {
                $product_price = $product->get_regular_price();
            } else {
                $product_price = $product->get_price();
            }

            // Увеличиваем итоговую сумму
            $cart_total += $product_price * $quantity;
        }
    } else {
        // Если способ оплаты не специфический, просто считаем обычную сумму корзины
        foreach ($cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            $quantity = $cart_item['quantity'];
            $cart_total += $product->get_price() * $quantity;
        }
    }

    // Возвращаем форматированное значение
    return number_format($cart_total, 0, '', ' ');
}



add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    ob_start();
    ?>
    <?php
    global $woocommerce;
    $c_total = get_custom_cart_total();
    ?>
    <a href="<?php if ($c_total == 0) {
                    echo '##';
                } else {
                    echo '/cart';
                } ?>" class="header__to-cart btn <?php if ($c_total == 0) {
                                                        echo 'btn-secondary';
                                                    } else {
                                                        echo 'btn-primary';
                                                    } ?> btn_icon-left">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 1C2.44772 1 2 1.44772 2 2C2 2.55228 2.44772 3 3 3H4.21922L4.52478 4.22224C4.52799 4.23637 4.5315 4.25039 4.5353 4.26429L5.89253 9.69321L4.99995 10.5858C3.74002 11.8457 4.63235 14 6.41416 14H15C15.5522 14 16 13.5523 16 13C16 12.4477 15.5522 12 15 12L6.41417 12L7.41416 11H14C14.3788 11 14.725 10.786 14.8944 10.4472L17.8944 4.44721C18.0494 4.13723 18.0329 3.76909 17.8507 3.47427C17.6684 3.17945 17.3466 3 17 3H6.28078L5.97014 1.75746C5.85885 1.3123 5.45887 1 5 1H3Z" fill="<?php if ($c_total == 0) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        echo '#21201F';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        echo '#fff';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    } ?>" />
            <path d="M16 16.5C16 17.3284 15.3284 18 14.5 18C13.6716 18 13 17.3284 13 16.5C13 15.6716 13.6716 15 14.5 15C15.3284 15 16 15.6716 16 16.5Z" fill="<?php if ($c_total == 0) {
                                                                                                                                                                    echo '#21201F';
                                                                                                                                                                } else {
                                                                                                                                                                    echo '#fff';
                                                                                                                                                                } ?>" />
            <path d="M6.5 18C7.32843 18 8 17.3284 8 16.5C8 15.6716 7.32843 15 6.5 15C5.67157 15 5 15.6716 5 16.5C5 17.3284 5.67157 18 6.5 18Z" fill="<?php if ($c_total == 0) {
                                                                                                                                                            echo '#21201F';
                                                                                                                                                        } else {
                                                                                                                                                            echo '#fff';
                                                                                                                                                        } ?>" />
        </svg>
        <?php
        if ($c_total == 0) {
        ?>
            <span>
                Корзина
            </span>
            <span class="to-cart-empty">
                <svg width="110" height="74" viewBox="0 0 110 74" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M94.5687 1H15.4317C11.4378 1 8.2002 4.23765 8.2002 8.2315V62.1685C8.2002 66.1623 11.4378 69.4 15.4317 69.4H94.5687C98.5625 69.4 101.8 66.1623 101.8 62.1685V8.2315C101.8 4.23765 98.5625 1 94.5687 1Z" stroke="#A3A3A3" stroke-width="2" stroke-linejoin="round" />
                    <path d="M1 73H109" stroke="#A3A3A3" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" />
                    <g clip-path="url(#clip0_2764_3815)">
                        <path d="M66.9567 52.3615C67.0888 51.0495 66.9178 49.7269 66.4545 48.4778C65.9913 47.2288 65.2459 46.0806 64.2659 45.1061C63.2858 44.1317 62.0924 43.3524 60.7616 42.8176C59.4307 42.2829 57.9915 42.0045 56.5353 42.0001C55.0792 41.9956 53.6379 42.2653 52.3031 42.7919C50.9683 43.3185 49.7692 44.0905 48.7819 45.059C47.7946 46.0274 47.0407 47.1711 46.5682 48.4173C46.0957 49.6635 45.9148 50.985 46.0371 52.2978L48.489 52.1109C48.3954 51.1057 48.5338 50.0938 48.8956 49.1397C49.2574 48.1855 49.8346 47.3099 50.5906 46.5684C51.3465 45.8269 52.2646 45.2358 53.2866 44.8326C54.3086 44.4294 55.4121 44.2229 56.5271 44.2263C57.642 44.2297 58.7439 44.4429 59.7629 44.8523C60.7819 45.2617 61.6956 45.8584 62.446 46.6045C63.1964 47.3506 63.7671 48.2297 64.1218 49.1861C64.4764 50.1424 64.6074 51.155 64.5063 52.1596L66.9567 52.3615Z" fill="#A3A3A3" />
                        <path d="M32.0182 22.6373C31.9634 23.1897 32.0344 23.7466 32.227 24.2725C32.4195 24.7984 32.7292 25.2819 33.1365 25.6922C33.5438 26.1024 34.0398 26.4306 34.5928 26.6557C35.1459 26.8809 35.7441 26.9981 36.3492 27C36.9544 27.0018 37.5533 26.8883 38.1081 26.6666C38.6628 26.4448 39.1611 26.1198 39.5714 25.712C39.9817 25.3042 40.295 24.8227 40.4914 24.298C40.6878 23.7733 40.7629 23.2169 40.7121 22.6641L39.6932 22.7428C39.7321 23.166 39.6745 23.5921 39.5242 23.9938C39.3738 24.3956 39.1339 24.7643 38.8198 25.0765C38.5056 25.3887 38.1241 25.6376 37.6993 25.8073C37.2746 25.9771 36.816 26.064 36.3527 26.0626C35.8893 26.0612 35.4314 25.9714 35.0079 25.799C34.5844 25.6266 34.2047 25.3754 33.8928 25.0613C33.581 24.7471 33.3438 24.377 33.1964 23.9743C33.049 23.5716 32.9946 23.1452 33.0366 22.7223L32.0182 22.6373Z" fill="#A3A3A3" />
                        <path d="M69.0182 22.6373C68.9634 23.1897 69.0344 23.7466 69.227 24.2725C69.4195 24.7984 69.7292 25.2819 70.1365 25.6922C70.5438 26.1024 71.0398 26.4306 71.5928 26.6557C72.1459 26.8809 72.7441 26.9981 73.3492 27C73.9544 27.0018 74.5533 26.8883 75.1081 26.6666C75.6628 26.4448 76.1611 26.1198 76.5714 25.712C76.9817 25.3042 77.295 24.8227 77.4914 24.298C77.6878 23.7733 77.7629 23.2169 77.7121 22.6641L76.6932 22.7428C76.7321 23.166 76.6745 23.5921 76.5242 23.9938C76.3738 24.3956 76.1339 24.7643 75.8198 25.0765C75.5056 25.3887 75.1241 25.6376 74.6993 25.8073C74.2746 25.9771 73.816 26.064 73.3527 26.0626C72.8893 26.0612 72.4314 25.9714 72.0079 25.799C71.5844 25.6266 71.2047 25.3754 70.8928 25.0613C70.581 24.7471 70.3438 24.377 70.1964 23.9743C70.049 23.5716 69.9946 23.1452 70.0366 22.7223L69.0182 22.6373Z" fill="#A3A3A3" />
                    </g>
                    <defs>
                        <clipPath id="clip0_2764_3815">
                            <rect width="46" height="31" fill="white" transform="translate(32 22)" />
                        </clipPath>
                    </defs>
                </svg>
                В вашей корзине пока пусто
            </span>
        <?php
        } else {
        ?>
            <span>
                <?php echo $c_total; ?>
            </span>
        <?php
        }
        ?>
    </a>

<?php $fragments['a.header__to-cart'] = ob_get_clean();

    return $fragments;
});

add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    ob_start();
?>
    <?php
    global $woocommerce;
    $c_total = number_format($woocommerce->cart->subtotal, 0, '', ' ');
    ?>

    <a href="<?php if ($c_total == 0) {
                    echo '##';
                } else {
                    echo '/cart';
                } ?>" class="bar__item-link bar__item-link-to-cart btn <?php if ($c_total == 0) {
                                                                            echo 'btn-secondary';
                                                                        } else {
                                                                            echo 'btn-primary';
                                                                        } ?> btn-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3.60039 1.2002C2.93765 1.2002 2.40039 1.73745 2.40039 2.4002C2.40039 3.06294 2.93765 3.6002 3.60039 3.6002H5.06346L5.43013 5.06689C5.43398 5.08384 5.43819 5.10067 5.44275 5.11734L7.07142 11.632L6.00033 12.7031C4.48842 14.215 5.55921 16.8002 7.69739 16.8002H18.0003C18.6631 16.8002 19.2003 16.2629 19.2003 15.6002C19.2003 14.9375 18.6631 14.4002 18.0003 14.4002L7.69739 14.4002L8.89739 13.2002H16.8004C17.2549 13.2002 17.6704 12.9434 17.8737 12.5368L21.4737 5.33685C21.6597 4.96487 21.6398 4.5231 21.4212 4.16932C21.2025 3.81554 20.8163 3.6002 20.4004 3.6002H7.53732L7.16456 2.10915C7.03101 1.57495 6.55103 1.2002 6.00039 1.2002H3.60039Z" fill="<?php if ($c_total == 0) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                echo '#21201F';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                echo '#fff';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            } ?>" />
            <path d="M19.2004 19.8002C19.2004 20.7943 18.3945 21.6002 17.4004 21.6002C16.4063 21.6002 15.6004 20.7943 15.6004 19.8002C15.6004 18.8061 16.4063 18.0002 17.4004 18.0002C18.3945 18.0002 19.2004 18.8061 19.2004 19.8002Z" fill="<?php if ($c_total == 0) {
                                                                                                                                                                                                                                                    echo '#21201F';
                                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                                    echo '#fff';
                                                                                                                                                                                                                                                } ?>" />
            <path d="M7.80039 21.6002C8.7945 21.6002 9.60039 20.7943 9.60039 19.8002C9.60039 18.8061 8.7945 18.0002 7.80039 18.0002C6.80628 18.0002 6.00039 18.8061 6.00039 19.8002C6.00039 20.7943 6.80628 21.6002 7.80039 21.6002Z" fill="<?php if ($c_total == 0) {
                                                                                                                                                                                                                                                echo '#21201F';
                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                echo '#fff';
                                                                                                                                                                                                                                            } ?>" />
        </svg>
        <?php
        if ($c_total == 0) {
        ?>
            <span class="to-cart-empty">
                <svg width="110" height="74" viewBox="0 0 110 74" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M94.5687 1H15.4317C11.4378 1 8.2002 4.23765 8.2002 8.2315V62.1685C8.2002 66.1623 11.4378 69.4 15.4317 69.4H94.5687C98.5625 69.4 101.8 66.1623 101.8 62.1685V8.2315C101.8 4.23765 98.5625 1 94.5687 1Z" stroke="#A3A3A3" stroke-width="2" stroke-linejoin="round"></path>
                    <path d="M1 73H109" stroke="#A3A3A3" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round"></path>
                    <g clip-path="url(#clip0_2764_3815)">
                        <path d="M66.9567 52.3615C67.0888 51.0495 66.9178 49.7269 66.4545 48.4778C65.9913 47.2288 65.2459 46.0806 64.2659 45.1061C63.2858 44.1317 62.0924 43.3524 60.7616 42.8176C59.4307 42.2829 57.9915 42.0045 56.5353 42.0001C55.0792 41.9956 53.6379 42.2653 52.3031 42.7919C50.9683 43.3185 49.7692 44.0905 48.7819 45.059C47.7946 46.0274 47.0407 47.1711 46.5682 48.4173C46.0957 49.6635 45.9148 50.985 46.0371 52.2978L48.489 52.1109C48.3954 51.1057 48.5338 50.0938 48.8956 49.1397C49.2574 48.1855 49.8346 47.3099 50.5906 46.5684C51.3465 45.8269 52.2646 45.2358 53.2866 44.8326C54.3086 44.4294 55.4121 44.2229 56.5271 44.2263C57.642 44.2297 58.7439 44.4429 59.7629 44.8523C60.7819 45.2617 61.6956 45.8584 62.446 46.6045C63.1964 47.3506 63.7671 48.2297 64.1218 49.1861C64.4764 50.1424 64.6074 51.155 64.5063 52.1596L66.9567 52.3615Z" fill="#A3A3A3"></path>
                        <path d="M32.0182 22.6373C31.9634 23.1897 32.0344 23.7466 32.227 24.2725C32.4195 24.7984 32.7292 25.2819 33.1365 25.6922C33.5438 26.1024 34.0398 26.4306 34.5928 26.6557C35.1459 26.8809 35.7441 26.9981 36.3492 27C36.9544 27.0018 37.5533 26.8883 38.1081 26.6666C38.6628 26.4448 39.1611 26.1198 39.5714 25.712C39.9817 25.3042 40.295 24.8227 40.4914 24.298C40.6878 23.7733 40.7629 23.2169 40.7121 22.6641L39.6932 22.7428C39.7321 23.166 39.6745 23.5921 39.5242 23.9938C39.3738 24.3956 39.1339 24.7643 38.8198 25.0765C38.5056 25.3887 38.1241 25.6376 37.6993 25.8073C37.2746 25.9771 36.816 26.064 36.3527 26.0626C35.8893 26.0612 35.4314 25.9714 35.0079 25.799C34.5844 25.6266 34.2047 25.3754 33.8928 25.0613C33.581 24.7471 33.3438 24.377 33.1964 23.9743C33.049 23.5716 32.9946 23.1452 33.0366 22.7223L32.0182 22.6373Z" fill="#A3A3A3"></path>
                        <path d="M69.0182 22.6373C68.9634 23.1897 69.0344 23.7466 69.227 24.2725C69.4195 24.7984 69.7292 25.2819 70.1365 25.6922C70.5438 26.1024 71.0398 26.4306 71.5928 26.6557C72.1459 26.8809 72.7441 26.9981 73.3492 27C73.9544 27.0018 74.5533 26.8883 75.1081 26.6666C75.6628 26.4448 76.1611 26.1198 76.5714 25.712C76.9817 25.3042 77.295 24.8227 77.4914 24.298C77.6878 23.7733 77.7629 23.2169 77.7121 22.6641L76.6932 22.7428C76.7321 23.166 76.6745 23.5921 76.5242 23.9938C76.3738 24.3956 76.1339 24.7643 75.8198 25.0765C75.5056 25.3887 75.1241 25.6376 74.6993 25.8073C74.2746 25.9771 73.816 26.064 73.3527 26.0626C72.8893 26.0612 72.4314 25.9714 72.0079 25.799C71.5844 25.6266 71.2047 25.3754 70.8928 25.0613C70.581 24.7471 70.3438 24.377 70.1964 23.9743C70.049 23.5716 69.9946 23.1452 70.0366 22.7223L69.0182 22.6373Z" fill="#A3A3A3"></path>
                    </g>
                    <defs>
                        <clipPath id="clip0_2764_3815">
                            <rect width="46" height="31" fill="white" transform="translate(32 22)"></rect>
                        </clipPath>
                    </defs>
                </svg>
                В вашей корзине пока пусто
            </span>
        <?php
        }
        ?>
    </a>


<?php $fragments['a.bar__item-link-to-cart'] = ob_get_clean();

    return $fragments;
});

add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    ob_start();
?>
    <?php
    global $woocommerce;
    $c_total = number_format($woocommerce->cart->subtotal, 0, '', ' ');
    ?>

    <div class="p-check-cart <?php
                                global $woocommerce;
                                $c_total = $woocommerce->cart->subtotal;

                                if ($c_total == 0) {
                                    echo 'p-cart-empty';
                                }
                                ?>"></div>


<?php $fragments['div.p-check-cart'] = ob_get_clean();

    return $fragments;
});

add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    ob_start();
?>
    <?php
    global $woocommerce;
    $c_total = number_format($woocommerce->cart->subtotal, 0, '', ' ');
    ?>

    <div class="js-remove-links">
        <?php
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {
        ?>
                <span class="js-remove-link" data-id="<?php echo $_product->get_id(); ?>" data-url="<?php
                                                                                                    echo esc_url(wc_get_cart_remove_url($cart_item_key));
                                                                                                    ?>" data-item-key="<?php echo $cart_item_key; ?>"></span>
        <?php
            }
        }
        ?>
    </div>


<?php $fragments['div.js-remove-links'] = ob_get_clean();

    return $fragments;
});

add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    ob_start();
?>
    <?php
    global $woocommerce;
    $c_total = number_format($woocommerce->cart->subtotal, 0, '', ' ');
    ?>

    <div class="cartP js-cartP" onclick="onCartCloseBack(event)">
        <div class="cartP__inner">
            <div class="cartP__container hide-scroll">
                <button class="cartP__btn btn-icon" onclick="onCartClose()">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 13L13 1M1 1L13 13" stroke="#8F8F8F" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <h3 class="cartP__title text-3xl">
                    Товар добавлен в корзину
                </h3>
                <?php woocommerce_mini_cart(); ?>
            </div>
        </div>
    </div>

<?php $fragments['div.cartP'] = ob_get_clean();

    return $fragments;
});

// Обновление корзины
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    ob_start();
?>
    <div class="ordering__card php-cart">
        <h2 class="ordering__card-title text-2xl">
            Товары в заказе
        </h2>
        <?php echo do_shortcode('[woocommerce_cart]') ?>
    </div>

<?php $fragments['div.php-cart'] = ob_get_clean();

    return $fragments;
});

add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    ob_start();
?>
    <div class="ordering__subtotal-content">
        <p class="ordering__subtotal-row">
            <span class="text text-lg">Товары:</span>
            <span class="text text-lg text--semibold"><?php echo number_format(WC()->cart->subtotal - nout_get_dso_cost(), 0, '', ' '); ?> BYN</span>
        </p>
        <?php
        if (nout_get_dso_cost() !== 0) {
        ?>
            <p class="ordering__subtotal-row">
                <span class="text text-lg">Доп. гарантия:</span>
                <span class="text text-lg text--semibold"><?php echo nout_get_dso_cost(); ?> BYN</span>
            </p>
        <?php
        }
        ?>
    </div>

    <?php $fragments['div.ordering__subtotal-content'] = ob_get_clean();

    return $fragments;
});

// К-во товаров
add_filter("loop_shop_per_page", create_function("$cols", "return 15;"), 20);

add_filter("loop_shop_per_page", function ($cols) {

    return 15;
}, 20);

add_filter('woocommerce_before_main_content', 'remove_breadcrumbs');
function remove_breadcrumbs()
{
    if (is_product()) {
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
    }
}

add_action('template_redirect', 'nout_recently_viewed_product_cookie', 20);

function nout_recently_viewed_product_cookie()
{

    // если находимся не на странице товара, ничего не делаем
    if (!is_product()) {
        return;
    }


    if (empty($_COOKIE['woocommerce_recently_viewed_2'])) {
        $viewed_products = array();
    } else {
        $viewed_products = (array) explode('|', $_COOKIE['woocommerce_recently_viewed_2']);
    }

    // добавляем в массив текущий товар
    if (!in_array(get_the_ID(), $viewed_products)) {
        $viewed_products[] = get_the_ID();
    }

    // нет смысла хранить там бесконечное количество товаров
    if (sizeof($viewed_products) > 15) {
        array_shift($viewed_products); // выкидываем первый элемент
    }

    // устанавливаем в куки
    wc_setcookie('woocommerce_recently_viewed_2', join('|', $viewed_products));
}

function nout_recently_viewed_products()
{

    if (empty($_COOKIE['woocommerce_recently_viewed_2'])) {
        $viewed_products = array();
    } else {
        $viewed_products = (array) explode('|', $_COOKIE['woocommerce_recently_viewed_2']);
    }

    if (empty($viewed_products)) {
        return;
    }

    // надо ведь сначала отображать последние просмотренные
    $viewed_products = array_reverse(array_map('absint', $viewed_products));

    $product_ids = join(",", $viewed_products);

    // return do_shortcode( "[products ids='$product_ids']" );

    $args = array(
        'posts_per_page' => 8,
        'post_type' => 'product',
        'orderby' => 'rand',
        'order'   => 'DESC',
        'post__in'  => $viewed_products,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '='
            ),
            //если есть фото
            array(
                'key' => '_thumbnail_id'
            ),
        )
    );
    $wc_query = new WP_Query($args);
    if ($wc_query->have_posts()) {
    ?>
        <div class="prs prp__cats">
            <h2 class="prs__title text-3xl" style="margin: 24px 0;">
                Вы недавно смотрели
            </h2>
            <ul class="prs__list prs__list-2334 hide-scroll">
                <?php
                while ($wc_query->have_posts()) {
                    $wc_query->the_post();

                    wc_get_template_part('content', 'product-prs');
                }
                ?>
            </ul>
        </div><?php
            }
            wp_reset_postdata();
        }

        add_filter('woocommerce_checkout_fields', 'truemisha_required_fields', 25);

        function truemisha_required_fields($fields)
        {

            // print_r( $fields ); exit // если хотите узнать названия полей
            $fields['billing']['billing_first_name']['required'] = false; // необязательно
            $fields['billing']['billing_last_name']['required'] = false; // необязательно
            $fields['billing']['billing_company']['required'] = false; // обязательно
            $fields['billing']['billing_address_1']['required'] = false; // обязательно
            $fields['billing']['billing_city']['required'] = false; // обязательно
            $fields['billing']['billing_postcode']['required'] = false; // обязательно

            return $fields;
        }

        add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');

        function custom_override_checkout_fields($fields)
        {
            //unset($fields['billing']['billing_first_name']);// имя
            //unset($fields['billing']['billing_last_name']);// фамилия
            unset($fields['billing']['billing_company']); // компания
            //   unset($fields['billing']['billing_address_1']);//
            unset($fields['billing']['billing_address_2']); //
            unset($fields['billing']['billing_city']);
            unset($fields['billing']['billing_postcode']);
            //   unset($fields['billing']['billing_country']);
            unset($fields['billing']['billing_state']);
            //unset($fields['billing']['billing_phone']);
            //unset($fields['order']['order_comments']);
            //unset($fields['billing']['billing_email']);
            unset($fields['account']['account_username']);
            unset($fields['account']['account_password']);
            unset($fields['account']['account_password-2']);


            unset($fields['billing']['billing_company']); // компания
            unset($fields['billing']['billing_postcode']); // индекс 

            //   unset($fields['billing']['billing_country']);  //удаляем! тут хранится значение страны оплаты
            unset($fields['shipping']['shipping_country']); ////удаляем! тут хранится значение страны доставки
            return $fields;
        }

        function set_checked_wc_terms($terms_is_checked)
        {
            return true;
        }
        add_filter('woocommerce_terms_is_checked_default', 'set_checked_wc_terms', 10);

        // Редирект на страницу спасибо
        add_action('template_redirect', 'truemisha_redirect_to_thank_you');

        function truemisha_redirect_to_thank_you()
        {

            // если не страница "Заказ принят", то ничего не делаем
            if (!is_order_received_page()) {
                return;
            }

            // неплохо бы проверить статус заказа, не редиректим зафейленные заказы
            if (isset($_GET['key'])) {
                $order_id = wc_get_order_id_by_order_key($_GET['key']);
                $order = wc_get_order($order_id);
                if ($order->has_status('failed')) {
                    return;
                }
            }


            wp_redirect(site_url('thank-you?order_id=' . $_GET['key'] . ''));
            exit;
        }

        // Очистка корзины
        add_action('init', 'woocommerce_clear_cart_url');
        function woocommerce_clear_cart_url()
        {
            global $woocommerce;
            if (isset($_REQUEST['clear-cart'])) {
                $woocommerce->cart->empty_cart();
            }
        }

        // Получение стоимости доп гарантии в корзине
        function nout_get_dso_cost()
        {
            $dso_cost = 0;

            $s_args_usl = array(
                'status' => 'publish',
                'limit' => 2000,
                'tax_query' => array(array(
                    'taxonomy' => 'product_cat',
                    'field' => 'id',
                    'terms' => array(2992),
                    'operator' => 'IN',
                )),
            );

            $s_usl_products = wc_get_products($s_args_usl);

            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

                foreach ($s_usl_products as $s_product) {
                    if ($s_product->get_id() == $_product->get_id()) {
                        $dso_cost = $dso_cost + intval($_product->get_price());
                    }
                }
            }

            return $dso_cost;
        }

        // Прячем товары с 0 ценой
        // add_action( 'woocommerce_product_query', 'themelocation_product_query' );
        // function themelocation_product_query( $q ){
        // $meta_query = $q->get( 'meta_query' );
        //     $meta_query[] = array(
        //                 'key'       => '_price',
        //                 'value'     => 0,
        //                 'compare'   => '>'
        //             );
        // $q->set( 'meta_query', $meta_query );
        // }

        // Отправка сообщения в тг о новом заказе
        add_action('woocommerce_order_status_changed', 'new_order_send_tg',  10, 3);
        function new_order_send_tg($order_id)
        {
            // $order = new WC_Order( $order_id );

            $order = wc_get_order($order_id);
            $data = $order->get_data();

            if ($order->get_status() == 'processing') {
                $msg = 'Магазин 100nout.by – Новый заказ!';

                $msg .= '

Новый заказ: WP-' . $order_id;

                // Get and Loop Over Order Items
                foreach ($order->get_items() as $item_id => $item) {
                    $product_name = $item->get_name();
                    $msg .= '

Название продукта: ' . $product_name;
                    // $msg .= 'Артикул продукта: '.$product->get_sku();
                    // $msg .= 'Артикул продукта: '.$total;
                }
                $msg .= '

Стоимость: ' . $order->get_total() . ' BYN';

                $msg .= '

Доставка: ' . $order->get_shipping_method();

                $msg .= '

Клиент: ' . $order->get_billing_first_name();

                $msg .= '

Телефон: ' . $order->get_billing_phone();

                $msg .= '

Email: ' . $order->get_billing_email();

                $msg .= '

Детали заказа: ' . $data['customer_note'];

                $new_msg = $msg;
                $userId = '-1001933568755'; // 100NOUT группы id в телеграм
                $myId = '192150244'; // Мой id в телеграм
                $token = '5360345876:AAGggqTq3e1yLvT7SLnQb9oo5qAEgS2jvEQ'; // Token бота

                file_get_contents('https://api.telegram.org/bot' . $token . '/sendMessage?chat_id=' . $userId . '&text=' . urlencode($new_msg) . ''); // Отправляем сообщение

                file_get_contents('https://api.telegram.org/bot' . $token . '/sendMessage?chat_id=' . $myId . '&text=' . urlencode($new_msg) . ''); // Отправляем сообщение
            }
        }

        # Отменим `-scaled` размер - ограничение максимального размера картинки
        add_filter('big_image_size_threshold', '__return_zero');


        add_filter('woocommerce_attribute', 'etx_rmv_attr_lnk');
        function etx_rmv_attr_lnk($att)
        {
            return strip_tags($att);
        }

        add_filter('paginate_links', function ($link) {
            $pos = strpos($link, 'page/1/');
            if ($pos !== false) {
                $link = substr($link, 0, $pos);
            }
            return $link;
        });


        /**
         * UPDATE SCHEMA.ORG FOR PRODUCT AND CATEGORY PAGES
         */

        function add_aggregate_rating_schema($webpage_data)
        {
            if (
                function_exists('is_product') && is_product() ||
                function_exists('is_product_category') && is_product_category()
            ) {
                if (!is_array($webpage_data['@type'])) {
                    $webpage_data['@type'] = [$webpage_data['@type']];
                }

                if (is_product()) {
                    $webpage_data['@type'] = ['Product', 'ItemPage'];
                } else {
                    $webpage_data['@type'] = ['Product'];
                }

                $webpage_data['aggregateRating'] = [
                    "@type" => "AggregateRating",
                    "ratingValue" => "5",
                    "reviewCount" => "54"
                ];
            }

            return $webpage_data;
        }

        add_filter('wpseo_schema_webpage', 'add_aggregate_rating_schema', 10, 2);


        function add_offers_to_product_schema($data)
        {
            if (function_exists('is_product') && is_product()) {
                $product = wc_get_product(get_the_id());

                if (!isset($data['offers'])) {
                    $data['offers'] = [
                        "@type" => "AggregateOffer",
                        "availability" => "http://schema.org/InStock",
                    ];
                }

                $product_price = $product->get_price();

                if (class_exists('WPSEO_WooCommerce_Utils')) {
                    $product_price = WPSEO_WooCommerce_Utils::get_product_display_price($product);
                }

                if ($product instanceof WC_Product_Variable) {
                    $decimals = wc_get_price_decimals();
                    $lowest   = $product->get_variation_price('min', true);
                    $highest  = $product->get_variation_price('max', true);

                    $lowPrice  = wc_format_decimal($lowest, $decimals);
                    $highPrice = wc_format_decimal($highest, $decimals);
                } else {
                    $lowPrice  = $product_price;
                    $highPrice = $product_price;
                }

                if (!isset($data['offers']['highPrice'])) {
                    $data['offers']['highPrice'] = $highPrice;
                }

                if (!isset($data['offers']['lowPrice'])) {
                    $data['offers']['lowPrice'] = $lowPrice;
                }

                if (!isset($data['offers']['priceCurrency'])) {
                    $data['offers']['priceCurrency'] = 'BYN';
                }
            }

            return $data;
        }

        add_filter('wpseo_schema_webpage', 'add_offers_to_product_schema', 10, 2);


        // Функция для получения всех подкатегорий рекурсивно
        function get_all_child_categories($parent_category_id)
        {
            $child_categories = get_terms('product_cat', array(
                'parent' => $parent_category_id,
                'hide_empty' => false,
            ));

            $all_child_categories = array();

            foreach ($child_categories as $child_category) {
                $all_child_categories[] = $child_category;
                $all_child_categories = array_merge($all_child_categories, get_all_child_categories($child_category->term_id));
            }

            return $all_child_categories;
        }

        function add_offers_to_product_category_schema($data)
        {
            if (function_exists('is_product_category') && is_product_category()) {
                // Получить текущую категорию
                $current_category = get_queried_object();

                // Получить все дочерние категории, включая подкатегории
                $all_child_categories = get_all_child_categories($current_category->term_id);

                // Собрать ID всех найденных категорий
                $category_ids = array($current_category->term_id);
                foreach ($all_child_categories as $child_category) {
                    $category_ids[] = $child_category->term_id;
                }

                $args = array(
                    'post_type' => 'product', // Тип поста "product" для товаров WooCommerce
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_cat', // Таксономия категорий товаров
                            'field' => 'id',
                            'terms' => $category_ids, // Замените этот массив на свой список ID категорий
                            'operator' => 'IN', // Используйте 'IN' для получения товаров из списка категорий
                        ),
                    ),
                    'orderby' => 'meta_value_num', // Сортировка по числовому значению мета-поля
                    'meta_key' => '_price', // Мета-ключ для цены товара
                    'posts_per_page' => 1,
                );

                $max_args = array_merge($args, [
                    'order' => 'DESC', // Сортировка по убыванию (начиная с наивысшей цены)
                ]);

                $min_args = array_merge($args, [
                    'order' => 'ASC', // Сортировка по убыванию (начиная с наивысшей цены)
                ]);

                $max_query = new WP_Query($max_args);
                $min_query = new WP_Query($min_args);

                $max_products = $max_query->get_posts();
                $min_products = $min_query->get_posts();

                if (is_array($max_products) && count($max_products) && is_array($min_products) && count($min_products)) {
                    $min_price = PHP_INT_MAX; // Устанавливаем начальное значение минимальной цены как максимальное целое число
                    $max_price = 0; // Устанавливаем начальное значение максимальной цены как 0

                    $min_product_obj = wc_get_product($min_products[0]->ID); // Получите объект товара
                    $max_product_obj = wc_get_product($max_products[0]->ID); // Получите объект товара

                    if ($min_product_obj && $max_product_obj) {
                        $min_price = $min_product_obj->get_price(); // Обновляем минимальную цену
                        $max_price = $max_product_obj->get_price(); // Обновляем максимальную цену
                    }

                    if (!isset($data['offers'])) {
                        $data['offers'] = [
                            "@type" => "AggregateOffer",
                            "availability" => "http://schema.org/InStock",
                        ];
                    }

                    if (!isset($data['offers']['highPrice'])) {
                        $data['offers']['highPrice'] = $max_price;
                    }

                    if (!isset($data['offers']['lowPrice'])) {
                        $data['offers']['lowPrice'] = $min_price;
                    }

                    if (!isset($data['offers']['priceCurrency'])) {
                        $data['offers']['priceCurrency'] = 'BYN';
                    }
                }
            }

            return $data;
        }

        add_filter('wpseo_schema_webpage', 'add_offers_to_product_category_schema', 10, 2);





        //обработчик смена способа оплаты
        add_action('wp_footer', 'custom_payment_method_script');

        function custom_payment_method_script()
        {
                ?>
    <script type="text/javascript">
        jQuery(function($) {
            // Предположим, что ваши радиокнопки для выбора способа оплаты имеют класс .custom-payment-method и атрибут data-payment-method
            $("input[name='orderPayment']").on('change', function() {
                var payment_method = $(this).val();
                console.log(payment_method);
                // Отправляем AJAX запрос для обновления выбранного способа оплаты в сессии и обновления корзины
                $.ajax({
                    type: 'POST',
                    url: wc_checkout_params.ajax_url,
                    data: {
                        action: 'update_custom_payment_method_and_cart',
                        payment_method: payment_method
                    },
                    success: function(response) {
                        console.log(response);
                        $(document.body).trigger('update_checkout');
                        $(document.body).trigger('wc_fragment_refresh');
                        if (payment_method == 3) {
                            $('.ordering__cart-item').each(
                                function(index) {
                                    localStorage.setItem($(this).attr("data-product_id"), "");
                                }
                            );
                        }
                    }
                });
            });
        });
    </script>
<?php
        }



        add_action('wp_ajax_update_custom_payment_method_and_cart', 'update_custom_payment_method_and_cart');
        add_action('wp_ajax_nopriv_update_custom_payment_method_and_cart', 'update_custom_payment_method_and_cart');

        function update_custom_payment_method_and_cart()
        {
            if (isset($_POST['payment_method'])) {
                $payment_method = sanitize_text_field($_POST['payment_method']);

                // Обновляем выбранный способ оплаты в пользовательской сессии
                WC()->session->set('custom_payment', $payment_method);

                /* // Проверяем, является ли выбранный способ оплаты нужным нам
                $specific_payment_method = '3'; // Замените на ID нужного вам способа оплаты


                $products_data = array();

                // Проходимся по всем товарам в корзине
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $product = $cart_item['data'];

                    // Отключаем sale price
                    if ($product->is_on_sale()) {
                        if (has_term(2992, 'product_cat', $product->get_id())) {
                            // Обнуляем цену для продуктов из категории 2992
                            if ($payment_method === $specific_payment_method) {
                            $product->set_price(0);
            }
                        } else {
                            if ($payment_method === $specific_payment_method) {
                                $product->set_price($product->get_regular_price());
                            }
                        }
                    }
                    $products_data[] = array(
                        'id' => $product->get_id(),
                        'name' => $product->get_name(),
                        'price' => $product->get_price(),
                        // Добавьте другие необходимые поля товара
                    );
                }

                WC()->cart->calculate_totals();

                // Отправляем успешный ответ
                wp_send_json($products_data); */
                wp_send_json_success();
            } else {
                // Отправляем ошибочный ответ
                wp_send_json_error();
            }

            wp_die();
        }

        add_action('woocommerce_before_calculate_totals', 'custom_before_calculate_totals');

        function custom_before_calculate_totals($cart)
        {
            if (is_admin() && !defined('DOING_AJAX')) {
                return;
            }

            // Получаем выбранный способ оплаты из сессии
            $payment_method = WC()->session->get('custom_payment');
            $specific_payment_method = '3'; // Замените на ID нужного вам способа оплаты
            $specific_payment_method_2 = '1';

            // Проходимся по всем товарам в корзине
            $keys_to_remove = [];
            if ($payment_method === $specific_payment_method || $payment_method === $specific_payment_method_2) {
                foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                    $product = $cart_item['data'];
                    // Проверяем, имеет ли продукт скидочную цену
                    if (has_term(2992, 'product_cat', $product->get_id())) {
                        // запоминаем товар, чтобы потом удалить
                        $keys_to_remove[] = $cart_item_key;
                    } else {
                        // убираем скидочную цену
                        if ($product->is_on_sale()) {
                            $product->set_price($product->get_regular_price());
                        }
                    }
                }
                foreach ($keys_to_remove as $key) {
                    $cart->remove_cart_item($key);
                }
            }
        }



        // Функция, которая регистрирует событие cron
        function schedule_monthly_product_removal()
        {
            if (!wp_next_scheduled('monthly_product_removal_hook')) {
                wp_schedule_event(time(), 'monthly', 'monthly_product_removal_hook');
            }
        }
        add_action('wp', 'schedule_monthly_product_removal');

        add_action('monthly_product_removal_hook', 'remove_old_products');


        function delete_product_and_images($product_id)
        {
            // Получаем все изображения товара
            $product = wc_get_product($product_id);
            $attachment_ids = $product->get_gallery_image_ids();

            // Удаляем каждое изображение
            foreach ($attachment_ids as $attachment_id) {
                wp_delete_attachment($attachment_id, true);
            }

            // Удаляем главное изображение товара
            $featured_image_id = get_post_thumbnail_id($product_id);
            if ($featured_image_id) {
                wp_delete_attachment($featured_image_id, true);
            }

            // Удаляем товар
            wp_delete_post($product_id, true);
        }

        function remove_old_products()
        {
            // Задаем категории
            $categories = array('noutbuki', 'smartfony', 'zapchasti-dlya-noutbukov', 'komplektuyushhie-dlya-noutbukov', 'periferiya-i-aksessuary', 'avto-i-moto');

            // Задаем дату для сравнения (3 месяца назад)
            $date_compare = date('Y-m-d H:i:s', strtotime('-2 months'));

            // WP_Query для получения всех товаров из заданных категорий
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'post_status' => 'draft',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => $categories,
                        'include_children' => true
                    ),
                ),
                'date_query' => array(
                    array(
                        'column' => 'post_modified_gmt',
                        'before' => $date_compare,
                    ),
                ),
                'fields' => 'ids',
            );

            $query = new WP_Query($args);

            // Проходим по каждому товару и удаляем его, если он не изменялся более 3 месяцев
            if ($query->have_posts()) {
                foreach ($query->posts as $product_id) {
                    delete_product_and_images($product_id);
                }
            }
        }


        add_action('webp_express_scheduled_conversion', 'run_webp_express_bulk_convert');
        function run_webp_express_bulk_convert() {
            if (class_exists('WebPExpress\Convert')) {
                $convert = new WebPExpress\Convert();
                $convert->convert();
            }
        }

        /**
         * Установка расписания при загрузке темы
         */
        function setup_webp_express_schedule() {
            if (!wp_next_scheduled('webp_express_scheduled_conversion')) {
                // Установка времени 4:00 по Москве (UTC+3)
                $time = strtotime('04:00:00 +3 hours');
                
                wp_schedule_event(
                    $time,
                    'daily',
                    'webp_express_scheduled_conversion'
                );
            }
        }
        add_action('init', 'setup_webp_express_schedule');


        // Скрываем кнопку "Обновить" для конкретных плагинов
        add_filter('plugin_row_meta', 'hide_update_notice_on_plugins_page', 10, 2);
        function hide_update_notice_on_plugins_page($plugin_meta, $plugin_file) {
            $blocked_plugins = [
                'woo-product-filter/woo-product-filter.php',
                'products-compare-for-woocommerce/products-compare.php',
                'load-more-products-for-woocommerce/load-more-products.php',
                'advanced-custom-fields-pro/acf.php'
            ];
            
            if (in_array($plugin_file, $blocked_plugins)) {
                // Удаляем строку "Доступна новая версия..."
                foreach ($plugin_meta as $key => $meta) {
                    if (strpos($meta, 'update-now') !== false) {
                        unset($plugin_meta[$key]);
                        break;
                    }
                }
            }
            return $plugin_meta;
        }

        add_filter('site_transient_update_plugins', 'disable_plugin_updates');
        function disable_plugin_updates($value) {
            // Список плагинов для блокировки (укажите свои)
            $plugins_to_disable = [
                'woo-product-filter/woo-product-filter.php',          
                'products-compare-for-woocommerce/products-compare.php',
                'load-more-products-for-woocommerce/load-more-products.php',
                'advanced-custom-fields-pro/acf.php'
            ];
            
            if (!empty($value->response)) {
                foreach ($plugins_to_disable as $plugin) {
                    if (isset($value->response[$plugin])) {
                        unset($value->response[$plugin]);
                    }
                }
            }
            return $value;
        }