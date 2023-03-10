<?php
/**
 * nout functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package nout
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function nout_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on nout, use a find and replace
		* to change 'nout' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'nout', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'nout' ),
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
	add_theme_support( 'customize-selective-refresh-widgets' );

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
add_action( 'after_setup_theme', 'nout_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function nout_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'nout_content_width', 640 );
}
add_action( 'after_setup_theme', 'nout_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function nout_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'nout' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'nout' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'nout_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function nout_scripts() {
	wp_enqueue_style( 'nout-style', get_template_directory_uri() . '/dist/css/app.css', array(), time() );

	wp_enqueue_script( 'nout-js', get_template_directory_uri() . '/dist/js/app.js', array(), time(), true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'nout_scripts' );

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
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

// ––––––––––––––––––––
// Custom Functions
// ––––––––––––––––––––

// Get image link from theme
function nout_image_directory() {
    echo get_template_directory_uri() . '/dist/images/';
}

if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title'    => 'Общие',
        'menu_title'    => 'Общие',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
    
}

// Menu And Navigation


function nout_menu_top_level() {
    $cats = get_field('kategorii', 'option');
    foreach ($cats as $cat) {
        ?>
            <li class="menu__list-item">
                <a href="<?php echo get_category_link( $cat->term_id );?>"
                    class="menu__list-item-btn text text-sm text--semibold btn js-menu-level-1">
                    <?php echo $cat->name;?>
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.25 2.91683L9.33333 7.00016L5.25 11.0835" stroke="#DEDEDE"
                            stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </li>
        <?php
    }
}

function nout_menu_second_level() {
    $cats = get_field('kategorii', 'option');
    foreach ($cats as $cat) {
        ?>
         <div class="menu__sub js-menu-sub-level-1">
            <div class="menu__sub-title-wrap">
                <button class="btn btn-icon js-menu-title-1">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.70711 14.7071C9.31658 15.0976 8.68342 15.0976 8.29289 14.7071L4.29289 10.7071C3.90237 10.3166 3.90237 9.68342 4.29289 9.29289L8.29289 5.29289C8.68342 4.90237 9.31658 4.90237 9.70711 5.29289C10.0976 5.68342 10.0976 6.31658 9.70711 6.70711L7.41421 9L15 9C15.5523 9 16 9.44772 16 10C16 10.5523 15.5523 11 15 11H7.41421L9.70711 13.2929C10.0976 13.6834 10.0976 14.3166 9.70711 14.7071Z"
                            fill="#21201F" />
                    </svg>
                </button>
                <a href="<?php echo get_category_link( $cat->term_id );?>" class="menu__sub-title text text-sm text--semibold">
                <?php echo $cat->name;?>
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
                
                    $categories = get_categories( $args );
                    if( $categories ){
                        foreach( $categories as $sub_cat ){
                            ?>
                            <li class="menu__sub-list-item">
                                <a href="<?php echo get_category_link( $sub_cat->term_id );?>"
                                    class="menu__list-item-btn text text-sm text--semibold btn js-menu-level-2">
                                    <?php echo $sub_cat->name;?>
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.25 2.91683L9.33333 7.00016L5.25 11.0835" stroke="#DEDEDE"
                                            stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
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

function nout_menu_third_level() {
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
    
        $categories = get_categories( $args );
        if( $categories ){
            foreach( $categories as $sub_cat ){
                ?>
                     <div class="menu__sub js-menu-sub-level-2">
                                <div class="menu__sub-title-wrap">
                                    <button class="btn btn-icon js-menu-title-2">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M9.70711 14.7071C9.31658 15.0976 8.68342 15.0976 8.29289 14.7071L4.29289 10.7071C3.90237 10.3166 3.90237 9.68342 4.29289 9.29289L8.29289 5.29289C8.68342 4.90237 9.31658 4.90237 9.70711 5.29289C10.0976 5.68342 10.0976 6.31658 9.70711 6.70711L7.41421 9L15 9C15.5523 9 16 9.44772 16 10C16 10.5523 15.5523 11 15 11H7.41421L9.70711 13.2929C10.0976 13.6834 10.0976 14.3166 9.70711 14.7071Z"
                                                fill="#21201F" />
                                        </svg>
                                    </button>
                                    <a href="<?php echo get_category_link( $sub_cat->term_id );?>" class="menu__sub-title text text-sm text--semibold">
                                    <?php echo $sub_cat->name;?>
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
                        
                                        $sub_categories = get_categories( $sub_args );
                                        if( $sub_categories ){
                                            foreach( $sub_categories as $sub_2_cat ){
                                                ?>
                                                <li class="menu__sub-list-item">
                                                    <a href="<?php echo get_category_link( $sub_2_cat->term_id );?>" class="menu__list-item-btn text text-sm text--semibold btn">
                                                        <?php echo $sub_2_cat->name;?>
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M5.25 2.91683L9.33333 7.00016L5.25 11.0835" stroke="#DEDEDE"
                                                                stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
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
add_filter( 'woocommerce_currencies', 'add_my_currency' );

function add_my_currency( $currencies ) {
     $currencies['BYN'] = __( 'Бел. руб.', 'woocommerce' );
     return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_my_currency_symbol', 10, 2);

function add_my_currency_symbol( $currency_symbol, $currency ) {
     switch( $currency ) {
          case 'BYN': $currency_symbol = 'BYN'; break;
     }
     return $currency_symbol;
}

// Вывод товаров по ID категори
function nout_show_products_by_id($cat_id) {
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
    $wc_query = new WP_Query( $args );
    if ($wc_query->have_posts()) {
        while ($wc_query->have_posts()) {
            $wc_query->the_post();
    
            wc_get_template_part( 'content', 'product-prs' );
           
        }
    }
    wp_reset_postdata();
}

// Вывод новых товаров
function nout_show_products_new() {
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
    $wc_query = new WP_Query( $args );
    if ($wc_query->have_posts()) {
        while ($wc_query->have_posts()) {
            $wc_query->the_post();
    
            wc_get_template_part( 'content', 'product-prs' );
           
        }
    }
    wp_reset_postdata();
}

// Вывод рекомендуемых товаров
function nout_show_products_related() {
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
    $wc_query = new WP_Query( $args );
    if ($wc_query->have_posts()) {
        while ($wc_query->have_posts()) {
            $wc_query->the_post();
    
            wc_get_template_part( 'content', 'product-prs' );
           
        }
    }
    wp_reset_postdata();
}

// Товар в корзине уже
add_filter( 'woocommerce_is_purchasable', 'disable_add_to_cart_if_product_is_in_cart', 10, 2 );
function disable_add_to_cart_if_product_is_in_cart ( 
   $is_purchasable, $product ){
  
    if(WC()->cart->find_product_in_cart( WC()->cart->generate_cart_id( $product->get_id() ) )) {
        return false;
    }
    
    return $is_purchasable;
}

// Переподключение скриптов для Ajax кнопки доабвить/удалить товар из корзины
function custom_wp_enqueue_scripts() {
    wp_deregister_script('wc-add-to-cart');
    wp_register_script('wc-add-to-cart', get_template_directory_uri() . '/js/woocommerce/add-to-cart.js', array( 'jquery' ), WC_VERSION, TRUE);
    wp_enqueue_script('wc-add-to-cart');
  }
add_action( 'wp_enqueue_scripts', 'custom_wp_enqueue_scripts', 99 );
  
  
// Remove product in the cart using ajax
function warp_ajax_product_remove()
{
    // Get mini cart
    ob_start();

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item)
    {
        if($cart_item['product_id'] == $_POST['product_id'] && $cart_item_key == $_POST['cart_item_key'] )
        {
            WC()->cart->remove_cart_item($cart_item_key);
        }
    }

    WC()->cart->calculate_totals();
    WC()->cart->maybe_set_cart_cookies();

    woocommerce_mini_cart();

    $mini_cart = ob_get_clean();

    // Fragments and mini cart are returned
    $data = array(
        'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
            )
        ),
        'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
    );

    wp_send_json( $data );

    die();
}

add_action( 'wp_ajax_product_remove', 'warp_ajax_product_remove' );
add_action( 'wp_ajax_nopriv_product_remove', 'warp_ajax_product_remove' );

// Мини корзина
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

    ob_start();
?>
    <?php 
        global $woocommerce;
        $c_total = $woocommerce->cart->total;
    ?>
    <a href="/cart" class="header__to-cart btn <?php if($c_total == 0) {echo 'btn-secondary';} else {echo 'btn-primary';}?> btn_icon-left">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M3 1C2.44772 1 2 1.44772 2 2C2 2.55228 2.44772 3 3 3H4.21922L4.52478 4.22224C4.52799 4.23637 4.5315 4.25039 4.5353 4.26429L5.89253 9.69321L4.99995 10.5858C3.74002 11.8457 4.63235 14 6.41416 14H15C15.5522 14 16 13.5523 16 13C16 12.4477 15.5522 12 15 12L6.41417 12L7.41416 11H14C14.3788 11 14.725 10.786 14.8944 10.4472L17.8944 4.44721C18.0494 4.13723 18.0329 3.76909 17.8507 3.47427C17.6684 3.17945 17.3466 3 17 3H6.28078L5.97014 1.75746C5.85885 1.3123 5.45887 1 5 1H3Z"
                fill="<?php if($c_total == 0) {echo '#21201F';} else {echo '#fff';}?>" />
            <path
                d="M16 16.5C16 17.3284 15.3284 18 14.5 18C13.6716 18 13 17.3284 13 16.5C13 15.6716 13.6716 15 14.5 15C15.3284 15 16 15.6716 16 16.5Z"
                fill="<?php if($c_total == 0) {echo '#21201F';} else {echo '#fff';}?>" />
            <path
                d="M6.5 18C7.32843 18 8 17.3284 8 16.5C8 15.6716 7.32843 15 6.5 15C5.67157 15 5 15.6716 5 16.5C5 17.3284 5.67157 18 6.5 18Z"
                fill="<?php if($c_total == 0) {echo '#21201F';} else {echo '#fff';}?>" />
        </svg>
        <?php
            if ($c_total == 0) {
                ?>
                <span>
                    Корзина
                </span>
                <span class="to-cart-empty">
                    <svg width="110" height="74" viewBox="0 0 110 74" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M94.5687 1H15.4317C11.4378 1 8.2002 4.23765 8.2002 8.2315V62.1685C8.2002 66.1623 11.4378 69.4 15.4317 69.4H94.5687C98.5625 69.4 101.8 66.1623 101.8 62.1685V8.2315C101.8 4.23765 98.5625 1 94.5687 1Z"
                            stroke="#A3A3A3" stroke-width="2" stroke-linejoin="round" />
                        <path d="M1 73H109" stroke="#A3A3A3" stroke-width="2" stroke-miterlimit="10"
                            stroke-linecap="round" />
                        <g clip-path="url(#clip0_2764_3815)">
                            <path
                                d="M66.9567 52.3615C67.0888 51.0495 66.9178 49.7269 66.4545 48.4778C65.9913 47.2288 65.2459 46.0806 64.2659 45.1061C63.2858 44.1317 62.0924 43.3524 60.7616 42.8176C59.4307 42.2829 57.9915 42.0045 56.5353 42.0001C55.0792 41.9956 53.6379 42.2653 52.3031 42.7919C50.9683 43.3185 49.7692 44.0905 48.7819 45.059C47.7946 46.0274 47.0407 47.1711 46.5682 48.4173C46.0957 49.6635 45.9148 50.985 46.0371 52.2978L48.489 52.1109C48.3954 51.1057 48.5338 50.0938 48.8956 49.1397C49.2574 48.1855 49.8346 47.3099 50.5906 46.5684C51.3465 45.8269 52.2646 45.2358 53.2866 44.8326C54.3086 44.4294 55.4121 44.2229 56.5271 44.2263C57.642 44.2297 58.7439 44.4429 59.7629 44.8523C60.7819 45.2617 61.6956 45.8584 62.446 46.6045C63.1964 47.3506 63.7671 48.2297 64.1218 49.1861C64.4764 50.1424 64.6074 51.155 64.5063 52.1596L66.9567 52.3615Z"
                                fill="#A3A3A3" />
                            <path
                                d="M32.0182 22.6373C31.9634 23.1897 32.0344 23.7466 32.227 24.2725C32.4195 24.7984 32.7292 25.2819 33.1365 25.6922C33.5438 26.1024 34.0398 26.4306 34.5928 26.6557C35.1459 26.8809 35.7441 26.9981 36.3492 27C36.9544 27.0018 37.5533 26.8883 38.1081 26.6666C38.6628 26.4448 39.1611 26.1198 39.5714 25.712C39.9817 25.3042 40.295 24.8227 40.4914 24.298C40.6878 23.7733 40.7629 23.2169 40.7121 22.6641L39.6932 22.7428C39.7321 23.166 39.6745 23.5921 39.5242 23.9938C39.3738 24.3956 39.1339 24.7643 38.8198 25.0765C38.5056 25.3887 38.1241 25.6376 37.6993 25.8073C37.2746 25.9771 36.816 26.064 36.3527 26.0626C35.8893 26.0612 35.4314 25.9714 35.0079 25.799C34.5844 25.6266 34.2047 25.3754 33.8928 25.0613C33.581 24.7471 33.3438 24.377 33.1964 23.9743C33.049 23.5716 32.9946 23.1452 33.0366 22.7223L32.0182 22.6373Z"
                                fill="#A3A3A3" />
                            <path
                                d="M69.0182 22.6373C68.9634 23.1897 69.0344 23.7466 69.227 24.2725C69.4195 24.7984 69.7292 25.2819 70.1365 25.6922C70.5438 26.1024 71.0398 26.4306 71.5928 26.6557C72.1459 26.8809 72.7441 26.9981 73.3492 27C73.9544 27.0018 74.5533 26.8883 75.1081 26.6666C75.6628 26.4448 76.1611 26.1198 76.5714 25.712C76.9817 25.3042 77.295 24.8227 77.4914 24.298C77.6878 23.7733 77.7629 23.2169 77.7121 22.6641L76.6932 22.7428C76.7321 23.166 76.6745 23.5921 76.5242 23.9938C76.3738 24.3956 76.1339 24.7643 75.8198 25.0765C75.5056 25.3887 75.1241 25.6376 74.6993 25.8073C74.2746 25.9771 73.816 26.064 73.3527 26.0626C72.8893 26.0612 72.4314 25.9714 72.0079 25.799C71.5844 25.6266 71.2047 25.3754 70.8928 25.0613C70.581 24.7471 70.3438 24.377 70.1964 23.9743C70.049 23.5716 69.9946 23.1452 70.0366 22.7223L69.0182 22.6373Z"
                                fill="#A3A3A3" />
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
                    <?php echo $c_total;?>
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
        $c_total = $woocommerce->cart->total;
    ?>
    
    <a href="<?php if($c_total == 0) {echo '##';} else {echo '/cart';}?>" class="bar__item-link bar__item-link-to-cart btn <?php if($c_total == 0) {echo 'btn-secondary';} else {echo 'btn-primary';}?> btn-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3.60039 1.2002C2.93765 1.2002 2.40039 1.73745 2.40039 2.4002C2.40039 3.06294 2.93765 3.6002 3.60039 3.6002H5.06346L5.43013 5.06689C5.43398 5.08384 5.43819 5.10067 5.44275 5.11734L7.07142 11.632L6.00033 12.7031C4.48842 14.215 5.55921 16.8002 7.69739 16.8002H18.0003C18.6631 16.8002 19.2003 16.2629 19.2003 15.6002C19.2003 14.9375 18.6631 14.4002 18.0003 14.4002L7.69739 14.4002L8.89739 13.2002H16.8004C17.2549 13.2002 17.6704 12.9434 17.8737 12.5368L21.4737 5.33685C21.6597 4.96487 21.6398 4.5231 21.4212 4.16932C21.2025 3.81554 20.8163 3.6002 20.4004 3.6002H7.53732L7.16456 2.10915C7.03101 1.57495 6.55103 1.2002 6.00039 1.2002H3.60039Z"
                            fill="<?php if($c_total == 0) {echo '#21201F';} else {echo '#fff';}?>" />
                        <path
                            d="M19.2004 19.8002C19.2004 20.7943 18.3945 21.6002 17.4004 21.6002C16.4063 21.6002 15.6004 20.7943 15.6004 19.8002C15.6004 18.8061 16.4063 18.0002 17.4004 18.0002C18.3945 18.0002 19.2004 18.8061 19.2004 19.8002Z"
                            fill="<?php if($c_total == 0) {echo '#21201F';} else {echo '#fff';}?>" />
                        <path
                            d="M7.80039 21.6002C8.7945 21.6002 9.60039 20.7943 9.60039 19.8002C9.60039 18.8061 8.7945 18.0002 7.80039 18.0002C6.80628 18.0002 6.00039 18.8061 6.00039 19.8002C6.00039 20.7943 6.80628 21.6002 7.80039 21.6002Z"
                            fill="<?php if($c_total == 0) {echo '#21201F';} else {echo '#fff';}?>" />
                    </svg>
                    <?php 
                        if ($c_total == 0) {
                            ?>
                                 <span class="to-cart-empty">
                        <svg width="110" height="74" viewBox="0 0 110 74" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M94.5687 1H15.4317C11.4378 1 8.2002 4.23765 8.2002 8.2315V62.1685C8.2002 66.1623 11.4378 69.4 15.4317 69.4H94.5687C98.5625 69.4 101.8 66.1623 101.8 62.1685V8.2315C101.8 4.23765 98.5625 1 94.5687 1Z"
                                stroke="#A3A3A3" stroke-width="2" stroke-linejoin="round"></path>
                            <path d="M1 73H109" stroke="#A3A3A3" stroke-width="2" stroke-miterlimit="10"
                                stroke-linecap="round"></path>
                            <g clip-path="url(#clip0_2764_3815)">
                                <path
                                    d="M66.9567 52.3615C67.0888 51.0495 66.9178 49.7269 66.4545 48.4778C65.9913 47.2288 65.2459 46.0806 64.2659 45.1061C63.2858 44.1317 62.0924 43.3524 60.7616 42.8176C59.4307 42.2829 57.9915 42.0045 56.5353 42.0001C55.0792 41.9956 53.6379 42.2653 52.3031 42.7919C50.9683 43.3185 49.7692 44.0905 48.7819 45.059C47.7946 46.0274 47.0407 47.1711 46.5682 48.4173C46.0957 49.6635 45.9148 50.985 46.0371 52.2978L48.489 52.1109C48.3954 51.1057 48.5338 50.0938 48.8956 49.1397C49.2574 48.1855 49.8346 47.3099 50.5906 46.5684C51.3465 45.8269 52.2646 45.2358 53.2866 44.8326C54.3086 44.4294 55.4121 44.2229 56.5271 44.2263C57.642 44.2297 58.7439 44.4429 59.7629 44.8523C60.7819 45.2617 61.6956 45.8584 62.446 46.6045C63.1964 47.3506 63.7671 48.2297 64.1218 49.1861C64.4764 50.1424 64.6074 51.155 64.5063 52.1596L66.9567 52.3615Z"
                                    fill="#A3A3A3"></path>
                                <path
                                    d="M32.0182 22.6373C31.9634 23.1897 32.0344 23.7466 32.227 24.2725C32.4195 24.7984 32.7292 25.2819 33.1365 25.6922C33.5438 26.1024 34.0398 26.4306 34.5928 26.6557C35.1459 26.8809 35.7441 26.9981 36.3492 27C36.9544 27.0018 37.5533 26.8883 38.1081 26.6666C38.6628 26.4448 39.1611 26.1198 39.5714 25.712C39.9817 25.3042 40.295 24.8227 40.4914 24.298C40.6878 23.7733 40.7629 23.2169 40.7121 22.6641L39.6932 22.7428C39.7321 23.166 39.6745 23.5921 39.5242 23.9938C39.3738 24.3956 39.1339 24.7643 38.8198 25.0765C38.5056 25.3887 38.1241 25.6376 37.6993 25.8073C37.2746 25.9771 36.816 26.064 36.3527 26.0626C35.8893 26.0612 35.4314 25.9714 35.0079 25.799C34.5844 25.6266 34.2047 25.3754 33.8928 25.0613C33.581 24.7471 33.3438 24.377 33.1964 23.9743C33.049 23.5716 32.9946 23.1452 33.0366 22.7223L32.0182 22.6373Z"
                                    fill="#A3A3A3"></path>
                                <path
                                    d="M69.0182 22.6373C68.9634 23.1897 69.0344 23.7466 69.227 24.2725C69.4195 24.7984 69.7292 25.2819 70.1365 25.6922C70.5438 26.1024 71.0398 26.4306 71.5928 26.6557C72.1459 26.8809 72.7441 26.9981 73.3492 27C73.9544 27.0018 74.5533 26.8883 75.1081 26.6666C75.6628 26.4448 76.1611 26.1198 76.5714 25.712C76.9817 25.3042 77.295 24.8227 77.4914 24.298C77.6878 23.7733 77.7629 23.2169 77.7121 22.6641L76.6932 22.7428C76.7321 23.166 76.6745 23.5921 76.5242 23.9938C76.3738 24.3956 76.1339 24.7643 75.8198 25.0765C75.5056 25.3887 75.1241 25.6376 74.6993 25.8073C74.2746 25.9771 73.816 26.064 73.3527 26.0626C72.8893 26.0612 72.4314 25.9714 72.0079 25.799C71.5844 25.6266 71.2047 25.3754 70.8928 25.0613C70.581 24.7471 70.3438 24.377 70.1964 23.9743C70.049 23.5716 69.9946 23.1452 70.0366 22.7223L69.0182 22.6373Z"
                                    fill="#A3A3A3"></path>
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


