<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package nout
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="date=no">
    <meta name="format-detection" content="address=no">
    <meta name="format-detection" content="email=no">


    <?php
    // Скрытие архивов атрибутов от индексации
    if (is_tax()) {
        $taxonomy_slug = get_query_var('taxonomy');
        if (mb_substr($taxonomy_slug, 0, 3) === 'pa_') {
            echo '<meta name="robot" content="noindex, nofollow">';
        }
    }

    $showCounters = true;
    if (isset($_COOKIE['cookieconsent_status']) && $_COOKIE['cookieconsent_status']=="deny") {
        $showCounters = false;
    } 


    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php if($showCounters){ ?>
        <!-- Google Tag Manager -->
        <!--
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-5FSCB3G');</script>
        -->
        <!-- End Google Tag Manager -->

        <!-- Meta Pixel Code -->
        <script>
            ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '1248920185697062');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=1248920185697062&ev=PageView&noscript=1" /></noscript>
        <!-- End Meta Pixel Code -->
    <?php } ?>

    <?php wp_head(); ?>
</head>

<?php if($showCounters){ ?>
    <!-- HEAD SCRIPTS FOR SITE -->
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m, e, t, r, i, k, a) {
            m[i] = m[i] || function() {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            for (var j = 0; j < document.scripts.length; j++) {
                if (document.scripts[j].src === r) {
                    return;
                }
            }
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(89452498, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: true,
            ecommerce: "dataLayer"
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/89452498" style="position:absolute; left:-9999px;" alt="" /></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-5FSCB3G');
    </script>

    <!-- End Google Tag Manager -->

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5FSCB3G"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10908582157"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'AW-10908582157');
    </script>
    <!-- Event snippet for put_cart conversion page -->
    <script>
        gtag('event', 'conversion', {
            'send_to': 'AW-10908582157/gU96CNXSjcIDEI2Cz9Eo',
            'value': 2.0,
            'currency': 'USD'
        });
    </script>
    <!-- Top.Mail.Ru counter -->
    <script type="text/javascript">
        var _tmr = window._tmr || (window._tmr = []);
        _tmr.push({
            id: "3485157",
            type: "pageView",
            start: (new Date()).getTime()
        });
        (function(d, w, id) {
            if (d.getElementById(id)) return;
            var ts = d.createElement("script");
            ts.type = "text/javascript";
            ts.async = true;
            ts.id = id;
            ts.src = "https://top-fwz1.mail.ru/js/code.js";
            var f = function() {
                var s = d.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(ts, s);
            };
            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else {
                f();
            }
        })(document, window, "tmr-code");
    </script>
    <noscript>
        <div><img src="https://top-fwz1.mail.ru/counter?id=3485157;js=na" style="position:absolute;left:-9999px;" alt="Top.Mail.Ru" /></div>
    </noscript>
    <!-- /Top.Mail.Ru counter -->
<?php } ?>

<script src="<?php echo get_template_directory_uri(); ?>/dist/swiper/swiper-bundle.min.js"></script>

<script>
    const onInitProductByTouch = (e, productClass) => {
        const product = document.querySelector(`.${productClass}`)

        if (product.dataset.swiper == 'false') {
            product.dataset.swiper = "true"
            const productGalery = new Swiper(`.${productClass} .js-product-swiper`, {
                effect: "fade",
                lazy: true,
                pagination: {
                    el: `.${productClass} .swiper-pagination`,
                    clickable: true,
                },
            })

            // const dots = document.querySelectorAll(`.${productClass} .swiper-pagination-bullet`)
            // setTimeout(() => {
            //     dots[1].click()
            // }, 500);
        }
    }

    const onInitProductByMouse = (e, productClass) => {
        const product = document.querySelector(`.${productClass}`)

        if (product.dataset.swiper == 'false') {
            product.dataset.swiper = "true"
            const productGalery = new Swiper(`.${productClass} .js-product-swiper`, {
                effect: "fade",
                lazy: true,
                pagination: {
                    el: `.${productClass} .swiper-pagination`,
                    clickable: true,
                },

            })
        } else {
            const slides = document.querySelectorAll(`.${productClass} .swiper-slide`)

            const currentArray = slides
            const currentWidth = e.target.offsetWidth
            const currentCursorPosition = e.layerX
            const currentLayers = currentArray.length
            const currentLayerWidth = currentWidth / currentArray.length
            const dots = document.querySelectorAll(`.${productClass} .swiper-pagination-bullet`)
            const currentDot = dots[Math.ceil(currentCursorPosition / currentLayerWidth) - 1]

            if (currentDot !== undefined && !currentDot.classList.contains("swiper-pagination-bullet-active")) {
                currentDot.click()
            }
        }
    }

    const onLeaveProductPhoto = (e, productClass) => {
        if (window.innerWidth > 1160) {
            const dots = document.querySelectorAll(`.${productClass} .swiper-pagination-bullet`)
            dots[0].click()
        }
    }

    const onProductClick = (link) => {
        if (window.innerWidth > 1160) {
            location.href = link
        }
    }

    // Favorite
    const checkFavorite = () => {
        document.querySelectorAll(".header__favorite").forEach(btn => {
            if (document.querySelector(".wishlist_products_counter").classList.contains("wishlist-counter-with-products")) {
                btn.classList.add("active")
                btn.setAttribute("href", "/wishlist")
            } else {
                btn.classList.remove("active")
                btn.setAttribute("href", "")
            }
        })
    }

    const onProductFavorite = (e) => {
        const favoriteBtn = e.target.previousElementSibling.previousElementSibling
        favoriteBtn.click()
        favoriteBtn.classList.add("loading")
        setTimeout(() => {
            favoriteBtn.classList.remove("loading")
        }, 300)
        setTimeout(() => {
            checkFavorite()
        }, 500);
    }

    window.onload = () => {
        setTimeout(() => {
            checkFavorite()
        }, 1000);
    }
</script>


<!-- TI WISHLIST -->
<style>
    .tinvwl-product-in-list+.tinv-wishlist-clear+.btn path {
        stroke: #D72F09;
        fill: #D72F09;
    }

    .tinvwl_add_to_wishlist_button+.tinv-wishlist-clear+.btn svg {
        transition: transform .2s cubic-bezier(0.71, 0.34, 0.08, 1.02);
    }

    .tinvwl_add_to_wishlist_button.loading+.tinv-wishlist-clear+.btn svg {
        transform: scale(.8);
    }

    .tinvwl-product-in-list+.tinv-wishlist-clear+.btn::before {
        content: "Убрать из избранного";
    }

    .tinvwl_add_to_wishlist_button {
        display: none;
    }
</style>

<!-- Mini Cart Script -->
<script>
    const onCartClose = () => {
        const cartP = document.querySelector(".js-cartP")
        const body = document.querySelector("body")
        cartP.classList.add("quit")
        setTimeout(() => {
            cartP.classList.remove("quit")
            cartP.classList.remove("active")
            body.classList.remove("scroll-disabled")
        }, 500);
    }

    const onCartCloseBack = (e) => {
        if (e.target.classList.contains("active")) {
            onCartClose()
        }
    }
</script>

<!-- Search Styles -->
<style>
    .dgwt-wcas-sf-wrapp input[type="search"].dgwt-wcas-search-input {
        border: 1px solid #50B83C;
        border-radius: 8px;
        height: 44px;
        color: #21201F;
    }

    .dgwt-wcas-search-wrapp .dgwt-wcas-sf-wrapp .dgwt-wcas-search-submit::before {
        display: none;
    }

    .dgwt-wcas-search-wrapp .dgwt-wcas-sf-wrapp .dgwt-wcas-search-submit,
    .dgwt-wcas-om-bar .dgwt-wcas-om-return {
        border-radius: 8px;
    }

    .dgwt-wcas-search-wrapp .dgwt-wcas-sf-wrapp:hover .dgwt-wcas-search-submit:hover,
    .dgwt-wcas-om-bar .dgwt-wcas-om-return:hover {
        background-color: #439a32;
        opacity: 1;
    }

    .dgwt-wcas-sf-wrapp input[type="search"].dgwt-wcas-search-input::placeholder {
        font-style: normal;
        color: #707070;
        opacity: 1;
    }

    .dgwt-wcas-open .dgwt-wcas-sf-wrapp input[type=search].dgwt-wcas-search-input {
        border-radius: 8px;
    }

    .dgwt-wcas-search-wrapp .dgwt-wcas-sf-wrapp input[type="search"].dgwt-wcas-search-input:hover,
    .dgwt-wcas-search-wrapp .dgwt-wcas-sf-wrapp input[type="search"].dgwt-wcas-search-input:focus {
        box-shadow: none;
        border-color: #50B83C;
    }

    .dgwt-wcas-suggestions-wrapp {
        border-radius: 8px;
        border: none;
    }

    /* .dgwt-wcas-suggestion-cat .dgwt-wcas-st-breadcrumbs {
        display: none;
    } */
    .dgwt-wcas-has-headings .dgwt-wcas-suggestion-headline .dgwt-wcas-st {
        font-weight: 400;
        font-size: 14px;
        line-height: 20px;
        color: #A3A3A3;
        text-transform: none;
        border: none;
        margin-top: 14px;
    }

    .dgwt-wcas-suggestion {
        padding: 6px 10px;
    }

    .dgwt-wcas-st {
        font-weight: 400;
        font-size: 16px;
        line-height: 20px;
        color: #21201F;
        position: relative;
    }

    .dgwt-wcas-st-title {
        height: 20px;
        overflow: hidden;
    }

    .dgwt-wcas-suggestion strong {
        font-weight: 600;
    }

    .dgwt-wcas-sku {
        font-weight: 400;
        font-size: 10px;
        line-height: 16px;
        color: #A3A3A3;
    }

    .dgwt-wcas-si {
        min-width: 40px;
        height: 40px;
        width: 40px;
    }

    .dgwt-wcas-si img {
        max-height: 100%;
        max-width: 100%;
        height: 100%;
        object-fit: cover;
        padding: 0;
    }

    .dgwt-wcas-sp {
        padding: 0;
        display: flex;
        flex-direction: column-reverse;
    }

    .dgwt-wcas-st::after {
        content: "";
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.3) 0%, #ffffff 86.21%);
        position: absolute;
        right: 0;
        bottom: 0;
        width: 40px;
        height: 100%;
        display: block;
    }

    .dgwt-wcas-suggestion-product.dgwt-wcas-suggestion-selected .dgwt-wcas-st::after {
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.3) 0%, #eee 86.21%);
    }

    .dgwt-wcas-sp ins {
        text-decoration: none;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        color: #21201F;
        text-decoration: none;
    }

    .dgwt-wcas-sp del {
        font-weight: 400;
        font-size: 14px;
        line-height: 16px;
        color: #CCCCCC;
        opacity: 1;
    }

    .dgwt-wcas-has-headings .dgwt-wcas-suggestion.dgwt-wcas-suggestion-tax.dgwt-wcas-suggestion-selected,
    .dgwt-wcas-has-headings .dgwt-wcas-suggestion.dgwt-wcas-suggestion-tax:hover {
        text-decoration: none;
    }

    .dgwt-wcas-sf-wrapp button.dgwt-wcas-search-submit {
        min-width: 44px;
    }

    .dgwt-wcas-darkened-overlay>div {
        top: 74px !important;
    }

    .dgwt-wcas-search-wrapp.dgwt-wcas-search-darkoverl-on .dgwt-wcas-search-form {
        background-color: transparent;
    }

    @media screen and (max-width: 960px) {
        .dgwt-wcas-suggestions-wrapp {
            left: 20px !important;
            width: calc(100vw - 40px) !important;
        }

        .dgwt-wcas-darkened-overlay>div {
            top: 143px !important;
        }
    }

    @media screen and (max-width: 480px) {
        .dgwt-wcas-darkened-overlay>div {
            top: 54px !important;
        }

        .dgwt-wcas-search-input {
            display: none !important;
        }

        .dgwt-wcas-sf-wrapp button.dgwt-wcas-search-submit {
            width: 44px;
            min-width: 44px;
            padding: 0 0px;
            height: 44px;
            position: relative;
        }

        /* .dgwt-wcas-suggestions-wrapp {
            min-width: 44px;
            max-width: 44px;
        } */
        .dgwt-wcas-overlay-mobile .dgwt-wcas-search-input {
            display: block !important;
        }

        .dgwt-wcas-overlay-mobile-on .dgwt-wcas-suggestions-wrapp {
            top: 85px !important;
        }

        .dgwt-wcas-suggestion {
            padding: 6px 20px;
        }

        .dgwt-wcas-om-bar {
            flex-direction: row-reverse;
            padding: 10px 20px;
            height: 65px;
        }

        .dgwt-wcas-om-return {
            background: #50B83C !important;
            border-radius: 8px !important;
            overflow: hidden;
            position: relative !important;
            margin-left: 6px !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .dgwt-wcas-om-return svg {
            display: none;
        }

        .dgwt-wcas-om-return::before {
            content: "";
            background-image: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 18L18 6M6 6L18 18' stroke='white' stroke-width='1.2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
            background-repeat: no-repeat;
            background-size: 100%;
            width: 24px;
            height: 24px;
            display: block;
        }

    }
</style>

<!-- Cookie Styles -->
<style>
    .cc-floating.cc-theme-classic {
        padding: 15px;
        border-radius: 8px;
    }

    .cc-theme-classic .cc-btn:last-child {
        min-width: 100%;
        font-weight: 400;
        font-size: 14px;
        line-height: 20px;
        color: #FFFFFF;
        transition: all .3s ease;
        border-radius: 8px;
    }

    .cc-theme-classic .cc-btn:last-child:hover {
        background-color: #439A32;
    }

    .cc-animate.cc-revoke.cc-bottom {
        display: none;
    }

    .cc-floating .cc-message {
        font-weight: 400;
        font-size: 14px;
        line-height: 20px;
        text-align: center;
        color: #FFFFFF;
    }

    .cc-link {
        display: none;
    }

    .cc-revoke {
        display: none;
    }

    @media screen and (max-width: 480px) and (orientation: portrait),
    screen and (max-width: 736px) and (orientation: landscape) {
        .cc-window.cc-floating {
            max-width: 285px;
            left: 20px;
            bottom: 70px;
        }
    }
</style>

<!-- WooBeeWoo Product Filter -->
<style>
    div.wpfMainWrapper {
        height: 100%;
        overflow-y: scroll;
        padding-bottom: 0;
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }

    div.wpfMainWrapper::-webkit-scrollbar {
        width: 0 !important;
        height: 0 !important;
        display: none;
    }

    div.wpfMainWrapper .wpfFilterWrapper {
        margin: 0 !important;
        padding: 0 !important;
        margin-bottom: 24px !important;
    }

    .wfpTitle {
        font-weight: 600 !important;
        font-size: 14px !important;
        line-height: 20px !important;
        color: #161D25 !important;
    }

    .ui-slider.ui-widget-content span.ui-slider-handle {
        background: #FFFFFF;
        box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1), 0px 1px 2px rgba(0, 0, 0, 0.06);
        border: none !important;
        border-radius: 100px;
        width: 24px;
        height: 24px;
        top: calc(50% - 12px);
        outline: none;
    }

    .ui-slider.ui-widget-content .ui-slider-handle:hover,
    .ui-slider.ui-widget-content span.ui-slider-handle.ui-state-hover,
    .ui-slider.ui-widget-content span.ui-slider-handle.ui-state-active,
    .ui-slider.ui-widget-content span.ui-slider-handle.ui-state-focus {
        border: none !important;
        outline: none;
    }

    div.ui-slider.ui-widget-content:not(.iris-slider-offset) {
        background: #DEDEDE;
        border-radius: 10px;
        border: none;
        height: 4px;
    }

    .ui-slider-horizontal div.ui-slider-range {
        background: #69C856 !important;
        border-radius: 10px;
    }

    .wpfCurrencySymbol {
        display: none;
    }

    .wpfFilterDelimeter {
        display: none;
    }

    .wpfPriceRangeField {
        width: 100% !important;
    }

    .wpfFilterWrapper[data-filter-type="wpfPrice"] div.wpfFilterContent {
        padding-left: 0;
        padding-right: 0;
    }

    .wpfFilterWrapper div.ui-slider-horizontal {
        width: calc(100% - 20px);
    }

    .wpfFilterWrapper[data-filter-type="wpfPrice"] .wpfTitleToggle {
        display: none;
    }

    .wpfFilterWrapper[data-filter-type="wpfPrice"] .wpfFilterTitle {
        pointer-events: none;
    }

    .wpfFilterWrapper[data-filter-type="wpfSortBy"] {
        display: none;
    }

    input.wpfPriceRangeField#wpfMinPrice,
    input.wpfPriceRangeField#wpfMaxPrice {
        font-weight: 400;
        font-size: 14px;
        line-height: 20px;
        color: rgba(33, 32, 31, 1);
        padding: 7px;
        height: auto;
        border: 1px solid #B8B8B8;
        border-radius: 8px;
        background: #fff;
    }

    div.wpfFilterContent div.ui-slider.ui-widget-content:not(.iris-slider-offset) {
        width: 84%;
        margin-left: 14px;
    }

    input.wpfPriceRangeField#wpfMinPrice:hover,
    input.wpfPriceRangeField#wpfMaxPrice:hover,
    input.wpfPriceRangeField#wpfMinPrice:focus,
    input.wpfPriceRangeField#wpfMaxPrice:focus,
    {
    border-color: #50B83C !important;
    }

    input.wpfPriceRangeField#wpfMinPrice {
        margin-right: 4px;
    }

    .wpfFilterWrapper div.wpfPriceInputs {
        margin-top: 24px;
    }

    .wpfFilterWrapper div.wpfFilterTaxNameWrapper {
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        color: #474747;
    }

    .wpfFilterVerScroll li label {
        margin-bottom: 8px;
    }

    div.wpfFilterWrapper span.wpfCheckbox label {
        width: 20px !important;
        height: 20px !important;
    }

    .wpfFilterWrapper .wpfCheckbox label::before {
        border: 1px solid #A3A3A3;
        border-radius: 6px;
    }

    ul.wpfFilterVerScroll {
        margin-top: 14px;
    }

    ul.wpfFilterVerScroll::-webkit-scrollbar-track {
        background-color: transparent;
        border: none;
    }

    ul.wpfFilterVerScroll::-webkit-scrollbar-thumb {
        background-color: #DEDEDE;
        border: none;
        box-shadow: none;
        border-radius: 10px;
    }

    ul.wpfFilterVerScroll::-webkit-scrollbar {
        width: 4px;
    }

    i.wpfTitleToggle::before {
        display: none;
    }

    .wpfFilterWrapper i.wpfTitleToggle {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    i.wpfTitleToggle::after {
        content: "";
        background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M0.292893 0.292893C0.683417 -0.097631 1.31658 -0.097631 1.70711 0.292893L5 3.58579L8.29289 0.292893C8.68342 -0.0976311 9.31658 -0.0976311 9.70711 0.292893C10.0976 0.683417 10.0976 1.31658 9.70711 1.70711L5.70711 5.70711C5.31658 6.09763 4.68342 6.09763 4.29289 5.70711L0.292893 1.70711C-0.0976311 1.31658 -0.0976311 0.683418 0.292893 0.292893Z' fill='%23A3A3A3'/%3E%3C/svg%3E%0A");
        width: 10px;
        height: 6px;
        display: block;
        transition: transform .3s ease;
    }

    i.wpfTitleToggle.fa-minus::after {
        transform: rotate(-180deg);
    }

    button.wpfFilterButton.wpfButton {
        margin: 0;
        width: 100%;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        color: #fff;
        background-color: #50B83C;
        border-radius: 8px;
        text-transform: none;
        padding: 0;
        min-height: 44px;
    }

    button.wpfFilterButton.wpfButton:hover {
        background-color: #439A32;
    }

    button.wpfClearButton.wpfButton {
        margin: 0;
        width: 100%;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        color: #474747;
        background-color: #EFEEED;
        border-radius: 8px;
        text-transform: none;
        padding: 0;
        min-height: 44px;
    }

    button.wpfClearButton.wpfButton:hover {
        background-color: #DEDEDE;
    }

    .wpfSearchWrapper input.wpfSearchFieldsFilter::placeholder {
        color: #B8B8B8;
    }

    .wpfSearchWrapper input.wpfSearchFieldsFilter {
        background: #FFFFFF;
        border: 1px solid #B8B8B8;
        border-radius: 8px;
        font-weight: 400;
        font-size: 14px;
        line-height: 20px;
        color: #474747;
        padding-left: 38px;
        padding-right: 20px;
        height: 36px;
    }

    .wpfSearchWrapper input.wpfSearchFieldsFilter:focus,
    .wpfSearchWrapper input.wpfSearchFieldsFilter:hover {
        border-color: #50B83C;
    }

    .wpfSearchWrapper {
        position: relative;
    }

    .wpfSearchWrapper::before {
        content: "";
        display: block;
        background-image: url("data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 18 18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M7.33464 2.33317C4.57321 2.33317 2.33464 4.57175 2.33464 7.33317C2.33464 10.0946 4.57321 12.3332 7.33464 12.3332C10.0961 12.3332 12.3346 10.0946 12.3346 7.33317C12.3346 4.57175 10.0961 2.33317 7.33464 2.33317ZM0.667969 7.33317C0.667969 3.65127 3.65274 0.666504 7.33464 0.666504C11.0165 0.666504 14.0013 3.65127 14.0013 7.33317C14.0013 8.87376 13.4787 10.2923 12.6012 11.4212L17.0906 15.9106C17.416 16.236 17.416 16.7637 17.0906 17.0891C16.7651 17.4145 16.2375 17.4145 15.912 17.0891L11.4227 12.5997C10.2938 13.4773 8.87523 13.9998 7.33464 13.9998C3.65274 13.9998 0.667969 11.0151 0.667969 7.33317Z' fill='%23B8B8B8'/%3E%3C/svg%3E%0A");
        background-size: 100% 100%;
        width: 16px;
        height: 16px;
        position: absolute;
        left: 11.5px;
        top: calc(50% - 8px);
        pointer-events: none;
    }

    .wpfValue,
    span.wpfFilterWrapper div.wpfFilterTaxNameWrapper {
        display: block;
    }

    ul.wpfFilterVerScroll li label {
        display: flex;
    }

    ul.wpfFilterVerScroll li label .wpfCheckbox {
        margin-top: 2.5px;
    }

    .wpfFilterWrapper div.wpfFilterTaxNameWrapper {
        display: block;
    }

    @-webkit-keyframes rotate-center {
        0% {
            -webkit-transform: rotate(0);
            transform: rotate(0);
        }

        100% {
            -webkit-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    @keyframes rotate-center {
        0% {
            -webkit-transform: rotate(0);
            transform: rotate(0);
        }

        100% {
            -webkit-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    ul.products .spinner,
    .la-spinner,
    .wpfIconPreview .spinner,
    .wpfLoaderIconTemplate .spinner,
    .woobewoo-filter-loader.spinner {
        background-image: url("data:image/svg+xml,%3Csvg width='222' height='223' viewBox='0 0 222 223' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath opacity='0.15' fill-rule='evenodd' clip-rule='evenodd' d='M37 111.501C37 152.37 70.1309 185.501 111 185.501C151.869 185.501 185 152.37 185 111.501C185 70.6319 151.869 37.501 111 37.501C70.1309 37.501 37 70.6319 37 111.501ZM18.5 111.501C18.5 162.587 59.9137 204.001 111 204.001C162.086 204.001 203.5 162.587 203.5 111.501C203.5 60.4146 162.086 19.001 111 19.001C59.9137 19.001 18.5 60.4146 18.5 111.501Z' fill='%2369C856'/%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M185 111.501C185 70.6319 151.869 37.501 111 37.501C70.1309 37.501 37 70.6319 37 111.501C37 116.61 32.8586 120.751 27.75 120.751C22.6414 120.751 18.5 116.61 18.5 111.501C18.5 60.4146 59.9137 19.001 111 19.001C162.086 19.001 203.5 60.4146 203.5 111.501C203.5 162.587 162.086 204.001 111 204.001C105.891 204.001 101.75 199.86 101.75 194.751C101.75 189.642 105.891 185.501 111 185.501C151.869 185.501 185 152.37 185 111.501Z' fill='url(%23paint0_linear_5584_27935)'/%3E%3Cdefs%3E%3ClinearGradient id='paint0_linear_5584_27935' x1='106.375' y1='111.501' x2='37' y2='111.501' gradientUnits='userSpaceOnUse'%3E%3Cstop stop-color='%236366F1'/%3E%3Cstop offset='1' stop-color='%236366F1' stop-opacity='0'/%3E%3C/linearGradient%3E%3C/defs%3E%3C/svg%3E%0A") !important;
        background-size: 100% 100% !important;
        -webkit-animation: rotate-center 1s linear infinite both !important;
        animation: rotate-center 1s linear infinite both !important;
    }

    div.wpfFilterWrapper span.wpfCheckbox input[type="checkbox"]:checked+label::before {
        background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg clip-path='url(%23clip0_5605_10415)'%3E%3Crect width='20' height='20' rx='6' fill='%2369C856'/%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M15.0611 5.42238C15.5183 5.73229 15.6376 6.35411 15.3277 6.81124L10.2432 14.3112C10.0771 14.5562 9.8111 14.715 9.51671 14.745C9.22232 14.7749 8.92977 14.673 8.71777 14.4665L4.80234 10.6536C4.40667 10.2683 4.39827 9.6352 4.78358 9.23953C5.16888 8.84386 5.80199 8.83546 6.19766 9.22077L9.25771 12.2007L13.6723 5.68895C13.9822 5.23182 14.604 5.11247 15.0611 5.42238Z' fill='white'/%3E%3C/g%3E%3Cdefs%3E%3CclipPath id='clip0_5605_10415'%3E%3Crect width='20' height='20' rx='6' fill='white'/%3E%3C/clipPath%3E%3C/defs%3E%3C/svg%3E%0A") !important;
        background-size: 110% !important;
        border: 1px solid #69C856 !important;
        background-position: -1px -1px !important;
    }

    .wpfFilterButtons {
        display: none;
    }

    .wpfFilterWrapper~.wpfFilterButtons {
        display: block;
    }

    @media screen and (max-width: 1200px) {
        div.wpfMainWrapper {
            position: static;
            top: 0;
            max-height: 100%;
        }

        div.wpfFilterContent div.ui-slider.ui-widget-content:not(.iris-slider-offset) {
            width: calc(100% - 30px);
        }

        div.wpfFilterButtons {
            display: block;
            position: fixed;
            right: 20px;
            top: 28px;
            z-index: 11;
            min-width: auto;
        }

        .wpfFilterButtons button.wpfClearButton.wpfButton {
            padding: 0;
            min-height: auto;
            min-width: auto;
            background: none;
            width: auto;
            font-size: 14px;
            line-height: 20px;
            color: #50B83C;
        }

        .wpfFilterButtons button.wpfClearButton.wpfButton:hover {
            color: #439A32;
        }


        .wpfFilterWrapper~.wpfFilterButtons {
            display: none;
        }

        ul.wpfFilterVerScroll {
            max-height: 100% !important;
        }

        .wpfCheckboxHier ul {
            display: flex;
            flex-wrap: wrap;
        }

        .wpfFilterVerScroll li {
            position: relative;
        }

        div.wpfFilterWrapper span.wpfCheckbox label {
            position: absolute !important;
            left: 0;
            top: 0;
            width: 100% !important;
            height: 100% !important;
        }

        .wpfFilterVerScroll li label {
            margin-bottom: 0 !important;
        }

        .wpfDisplay {
            position: relative;
            z-index: 1;
        }

        span.wpfValue {
            padding: 8px 12px !important;
        }

        .wpfFilterWrapper div.wpfFilterTaxNameWrapper {
            padding: 0 !important;
        }

        div.wpfFilterWrapper .wpfCheckbox label::before {
            border: none !important;
            background-color: #EFEEED !important;
        }

        .wpfFilterWrapper ul:not(.wpfButtonsFilter) li {
            margin: 4px !important;
            margin-left: 0 !important;
            margin-right: 8px !important;
        }

        div.wpfFilterWrapper span.wpfCheckbox input[type="checkbox"]:checked+label::before {
            background-image: none !important;
            background-color: #69C856 !important;
            border: none !important;
        }

        div.wpfFilterWrapper span.wpfCheckbox input[type="checkbox"]:checked~label::after {
            content: "" !important;
            position: absolute !important;
            z-index: 10;
            right: 8px;
            left: auto !important;
            top: 50% !important;
            background-image: url("data:image/svg+xml,%3Csvg width='12' height='12' viewBox='0 0 12 12' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 11L11 1M1 1L11 11' stroke='white' stroke-width='1.2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
            background-size: 100% 100%;
            width: 10px !important;
            height: 10px !important;
            display: block !important;
        }

        ul.wpfFilterVerScroll li label:has(input[type="checkbox"]:checked) .wpfFilterTaxNameWrapper {
            color: #fff;
        }

        ul.wpfFilterVerScroll li label:has(input[type="checkbox"]:checked) .wpfValue {
            padding-right: 38px !important;
        }
    }
</style>

<!-- LMP PLUGIN Styles -->
<style>
    .lmp_products_loading {
        background-image: url("data:image/svg+xml,%3Csvg width='222' height='223' viewBox='0 0 222 223' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath opacity='0.15' fill-rule='evenodd' clip-rule='evenodd' d='M37 111.501C37 152.37 70.1309 185.501 111 185.501C151.869 185.501 185 152.37 185 111.501C185 70.6319 151.869 37.501 111 37.501C70.1309 37.501 37 70.6319 37 111.501ZM18.5 111.501C18.5 162.587 59.9137 204.001 111 204.001C162.086 204.001 203.5 162.587 203.5 111.501C203.5 60.4146 162.086 19.001 111 19.001C59.9137 19.001 18.5 60.4146 18.5 111.501Z' fill='%2369C856'/%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M185 111.501C185 70.6319 151.869 37.501 111 37.501C70.1309 37.501 37 70.6319 37 111.501C37 116.61 32.8586 120.751 27.75 120.751C22.6414 120.751 18.5 116.61 18.5 111.501C18.5 60.4146 59.9137 19.001 111 19.001C162.086 19.001 203.5 60.4146 203.5 111.501C203.5 162.587 162.086 204.001 111 204.001C105.891 204.001 101.75 199.86 101.75 194.751C101.75 189.642 105.891 185.501 111 185.501C151.869 185.501 185 152.37 185 111.501Z' fill='url(%23paint0_linear_5584_27935)'/%3E%3Cdefs%3E%3ClinearGradient id='paint0_linear_5584_27935' x1='106.375' y1='111.501' x2='37' y2='111.501' gradientUnits='userSpaceOnUse'%3E%3Cstop stop-color='%236366F1'/%3E%3Cstop offset='1' stop-color='%236366F1' stop-opacity='0'/%3E%3C/linearGradient%3E%3C/defs%3E%3C/svg%3E%0A") !important;
        background-size: 100% 100% !important;
        -webkit-animation: rotate-center 1s linear infinite both !important;
        animation: rotate-center 1s linear infinite both !important;
        width: 50px;
        height: 80px;
        margin-top: 80px;
        margin-bottom: 80px;
    }

    .lmp_products_loading i.fa {
        display: none;
    }

    .catalog__list .lmp_products_loading {
        display: none;
    }
</style>

<!-- Woocommerce Styles -->
<style>
    .woocommerce-pagination {
        display: none !important;
    }
</style>

<!-- Attribute Groups -->
<style>
    .shop_attributes tr,
    .shop_attributes tr td {
        display: flex;
        align-items: flex-start;
    }

    .woocommerce-group-attributes-layout-1 {
        background: none;
    }

    .woocommerce-group-attributes-layout-1 .attribute_group_row {
        border: none;
    }

    .shop_attributes tr.alt,
    .shop_attributes tr.alt td {
        background-color: transparent !important;
    }

    .woocommerce table.shop_attributes.woocommerce-group-attributes-layout-1 th,
    .woocommerce table.shop_attributes.woocommerce-group-attributes-layout-2 th,
    .woocommerce table.shop_attributes.woocommerce-group-attributes-layout-3 th {
        padding: 0;
    }

    .attribute_group_name {
        font-style: normal;
        font-weight: 600 !important;
        font-size: 18px;
        line-height: 28px !important;
        color: #21201F;
    }

    /* .woocommerce table.shop_attributes th {
        width: auto;
    } */
    .attribute_group_name {
        width: 100% !important;
    }

    th.attribute_name {
        font-style: normal;
        font-weight: 400 !important;
        font-size: 14px;
        line-height: 20px !important;
        color: #5C5C5C;
    }

    .attribute_group_row_other,
    .attribute_row_other {
        display: none !important;
    }

    .woocommerce table.shop_attributes {
        width: 100%;
    }

    .woocommerce table.shop_attributes.woocommerce-group-attributes-layout-1 .attribute_row>td {
        width: 100%;
    }

    .attribute_value a {
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 20px;
        color: #21201F;
        margin-left: 4px;
        display: flex;
    }

    .attribute_group_row {
        margin-bottom: 10px;
    }

    .shop_attributes tr {
        margin-bottom: 6px;
    }
</style>

<!-- Контактная форма -->
<style>
    .hidden-fields-container{
        display: none;
    }
    .wpcf7-spinner {
        display: none;
    }

    .wpcf7-form .btn+br {
        display: none;
    }

    .wpcf7 form .wpcf7-response-output {
        border: none;
        font-size: 12px;
        line-height: 14px;
        margin: 0;
        color: #000;
        padding: 0;
        margin-top: 2px;
    }
</style>

<!-- Compare -->
<style>
    .br_compare_added svg path {
        stroke: #439A32;
    }

    a.br_compare_button {
        padding: 6px;
        margin-top: 0 !important;
    }

    .br_compare_added:before {
        content: "Перейти в сравнение";
    }
</style>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <style>
        @media screen and (max-width: 480px) {
            .b24-widget-button-position-bottom-right {
                right: 15px;
                bottom: 70px;
            }

            .b24-widget-button-pulse-animate {
                display: none;
            }

            .b24-widget-button-inner-mask {
                display: none;
            }
        }
    </style>
    <!-- Битрикс форма чата и поддержки -->
    <?php
    if (is_page_template('templates/skupka.php')) {
    ?>
        <script>
            (function(w, d, u) {
                var s = d.createElement('script');
                s.async = true;
                s.src = u + '?' + (Date.now() / 60000 | 0);
                var h = d.getElementsByTagName('script')[0];
                h.parentNode.insertBefore(s, h);
            })(window, document, 'https://cdn-ru.bitrix24.ru/b16077710/crm/site_button/loader_3_990vyx.js');
        </script>
    <?php
    } else {
    ?>
        <script>
            (function(w, d, u) {
                var s = d.createElement('script');
                s.async = true;
                s.src = u + '?' + (Date.now() / 60000 | 0);
                var h = d.getElementsByTagName('script')[0];
                h.parentNode.insertBefore(s, h);
            })(window, document, 'https://cdn-ru.bitrix24.ru/b16077710/crm/site_button/loader_5_r5xxwz.js');
        </script>
    <?php

    }
    ?>

    <!-- Google Tag Manager (noscript) -->
    <!--
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5FSCB3G"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
 -->
    <!-- End Google Tag Manager (noscript) -->

    <!-- Ajax удаление товаров из корзины с кнопки из карточки товара -->
    <style>
        .js-remove-links {
            display: none;
        }
    </style>
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

    <div id="page">
        <div class="page" id="page">
            <header class="header header--fixed">
                <div class="layout">
                    <div class="header__top">
                        <a href="/" class="header__logo">
                            <?php
                            /*<svg width="151" height="44" viewBox="0 0 151 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M61.072 30C60.9253 30 60.8007 29.956 60.698 29.868C60.61 29.7653 60.566 29.6407 60.566 29.494V17.174L56.914 19.99C56.7967 20.078 56.672 20.1147 56.54 20.1C56.408 20.0853 56.298 20.0193 56.21 19.902L55.638 19.176C55.55 19.044 55.5133 18.912 55.528 18.78C55.5573 18.648 55.6307 18.538 55.748 18.45L60.544 14.754C60.6467 14.6807 60.742 14.6367 60.83 14.622C60.918 14.6073 61.0133 14.6 61.116 14.6H62.238C62.3847 14.6 62.502 14.6513 62.59 14.754C62.678 14.842 62.722 14.9593 62.722 15.106V29.494C62.722 29.6407 62.678 29.7653 62.59 29.868C62.502 29.956 62.3847 30 62.238 30H61.072ZM71.3842 30.22C70.3722 30.22 69.5142 30.066 68.8102 29.758C68.1208 29.4353 67.5562 28.9953 67.1162 28.438C66.6762 27.8807 66.3535 27.2427 66.1482 26.524C65.9575 25.8053 65.8475 25.0427 65.8182 24.236C65.8035 23.84 65.7888 23.422 65.7742 22.982C65.7742 22.542 65.7742 22.102 65.7742 21.662C65.7888 21.2073 65.8035 20.7747 65.8182 20.364C65.8328 19.5573 65.9428 18.7947 66.1482 18.076C66.3682 17.3427 66.6908 16.7047 67.1162 16.162C67.5562 15.6047 68.1282 15.172 68.8322 14.864C69.5362 14.5413 70.3868 14.38 71.3842 14.38C72.3962 14.38 73.2468 14.5413 73.9362 14.864C74.6402 15.172 75.2122 15.6047 75.6522 16.162C76.0922 16.7047 76.4148 17.3427 76.6202 18.076C76.8402 18.7947 76.9575 19.5573 76.9722 20.364C76.9868 20.7747 76.9942 21.2073 76.9942 21.662C77.0088 22.102 77.0088 22.542 76.9942 22.982C76.9942 23.422 76.9868 23.84 76.9722 24.236C76.9575 25.0427 76.8402 25.8053 76.6202 26.524C76.4148 27.2427 76.0922 27.8807 75.6522 28.438C75.2268 28.9953 74.6622 29.4353 73.9582 29.758C73.2688 30.066 72.4108 30.22 71.3842 30.22ZM71.3842 28.35C72.5282 28.35 73.3715 27.976 73.9142 27.228C74.4715 26.4653 74.7575 25.4313 74.7722 24.126C74.8015 23.7007 74.8162 23.29 74.8162 22.894C74.8162 22.4833 74.8162 22.08 74.8162 21.684C74.8162 21.2733 74.8015 20.87 74.7722 20.474C74.7575 19.198 74.4715 18.1787 73.9142 17.416C73.3715 16.6387 72.5282 16.25 71.3842 16.25C70.2548 16.25 69.4115 16.6387 68.8542 17.416C68.3115 18.1787 68.0255 19.198 67.9962 20.474C67.9962 20.87 67.9888 21.2733 67.9742 21.684C67.9742 22.08 67.9742 22.4833 67.9742 22.894C67.9888 23.29 67.9962 23.7007 67.9962 24.126C68.0255 25.4313 68.3188 26.4653 68.8762 27.228C69.4335 27.976 70.2695 28.35 71.3842 28.35ZM85.349 30.22C84.337 30.22 83.479 30.066 82.775 29.758C82.0857 29.4353 81.521 28.9953 81.081 28.438C80.641 27.8807 80.3183 27.2427 80.113 26.524C79.9223 25.8053 79.8123 25.0427 79.783 24.236C79.7683 23.84 79.7537 23.422 79.739 22.982C79.739 22.542 79.739 22.102 79.739 21.662C79.7537 21.2073 79.7683 20.7747 79.783 20.364C79.7977 19.5573 79.9077 18.7947 80.113 18.076C80.333 17.3427 80.6557 16.7047 81.081 16.162C81.521 15.6047 82.093 15.172 82.797 14.864C83.501 14.5413 84.3517 14.38 85.349 14.38C86.361 14.38 87.2117 14.5413 87.901 14.864C88.605 15.172 89.177 15.6047 89.617 16.162C90.057 16.7047 90.3797 17.3427 90.585 18.076C90.805 18.7947 90.9223 19.5573 90.937 20.364C90.9517 20.7747 90.959 21.2073 90.959 21.662C90.9737 22.102 90.9737 22.542 90.959 22.982C90.959 23.422 90.9517 23.84 90.937 24.236C90.9223 25.0427 90.805 25.8053 90.585 26.524C90.3797 27.2427 90.057 27.8807 89.617 28.438C89.1917 28.9953 88.627 29.4353 87.923 29.758C87.2337 30.066 86.3757 30.22 85.349 30.22ZM85.349 28.35C86.493 28.35 87.3363 27.976 87.879 27.228C88.4363 26.4653 88.7223 25.4313 88.737 24.126C88.7663 23.7007 88.781 23.29 88.781 22.894C88.781 22.4833 88.781 22.08 88.781 21.684C88.781 21.2733 88.7663 20.87 88.737 20.474C88.7223 19.198 88.4363 18.1787 87.879 17.416C87.3363 16.6387 86.493 16.25 85.349 16.25C84.2197 16.25 83.3763 16.6387 82.819 17.416C82.2763 18.1787 81.9903 19.198 81.961 20.474C81.961 20.87 81.9537 21.2733 81.939 21.684C81.939 22.08 81.939 22.4833 81.939 22.894C81.9537 23.29 81.961 23.7007 81.961 24.126C81.9903 25.4313 82.2837 26.4653 82.841 27.228C83.3983 27.976 84.2343 28.35 85.349 28.35ZM94.7598 30C94.5985 30 94.4738 29.956 94.3858 29.868C94.2978 29.7653 94.2538 29.6407 94.2538 29.494V15.128C94.2538 14.9667 94.2978 14.842 94.3858 14.754C94.4738 14.6513 94.5985 14.6 94.7598 14.6H95.7718C95.9478 14.6 96.0798 14.644 96.1678 14.732C96.2558 14.8053 96.3072 14.864 96.3218 14.908L103.626 26.128V15.128C103.626 14.9667 103.67 14.842 103.758 14.754C103.846 14.6513 103.971 14.6 104.132 14.6H105.188C105.349 14.6 105.474 14.6513 105.562 14.754C105.665 14.842 105.716 14.9667 105.716 15.128V29.472C105.716 29.6187 105.665 29.7433 105.562 29.846C105.474 29.9487 105.357 30 105.21 30H104.154C103.993 30 103.868 29.956 103.78 29.868C103.707 29.78 103.655 29.7213 103.626 29.692L96.3438 18.582V29.494C96.3438 29.6407 96.2925 29.7653 96.1898 29.868C96.1018 29.956 95.9772 30 95.8158 30H94.7598ZM115.051 30.22C113.805 30.22 112.734 30.0073 111.839 29.582C110.945 29.142 110.255 28.4747 109.771 27.58C109.287 26.6707 109.016 25.534 108.957 24.17C108.943 23.5247 108.935 22.9087 108.935 22.322C108.935 21.7207 108.943 21.0973 108.957 20.452C109.016 19.1027 109.295 17.9807 109.793 17.086C110.307 16.1767 111.011 15.502 111.905 15.062C112.815 14.6073 113.863 14.38 115.051 14.38C116.254 14.38 117.303 14.6073 118.197 15.062C119.107 15.502 119.818 16.1767 120.331 17.086C120.845 17.9807 121.123 19.1027 121.167 20.452C121.197 21.0973 121.211 21.7207 121.211 22.322C121.211 22.9087 121.197 23.5247 121.167 24.17C121.123 25.534 120.852 26.6707 120.353 27.58C119.869 28.4747 119.18 29.142 118.285 29.582C117.391 30.0073 116.313 30.22 115.051 30.22ZM115.051 28.35C116.181 28.35 117.097 28.0127 117.801 27.338C118.52 26.6633 118.909 25.5707 118.967 24.06C118.997 23.4 119.011 22.8133 119.011 22.3C119.011 21.772 118.997 21.1853 118.967 20.54C118.938 19.528 118.747 18.7067 118.395 18.076C118.058 17.4453 117.603 16.9833 117.031 16.69C116.459 16.3967 115.799 16.25 115.051 16.25C114.333 16.25 113.687 16.3967 113.115 16.69C112.543 16.9833 112.081 17.4453 111.729 18.076C111.392 18.7067 111.201 19.528 111.157 20.54C111.143 21.1853 111.135 21.772 111.135 22.3C111.135 22.8133 111.143 23.4 111.157 24.06C111.216 25.5707 111.605 26.6633 112.323 27.338C113.042 28.0127 113.951 28.35 115.051 28.35ZM130.226 30.22C128.994 30.22 127.931 30 127.036 29.56C126.156 29.12 125.474 28.4527 124.99 27.558C124.521 26.6487 124.286 25.4827 124.286 24.06V15.128C124.286 14.9667 124.33 14.842 124.418 14.754C124.506 14.6513 124.631 14.6 124.792 14.6H125.914C126.075 14.6 126.2 14.6513 126.288 14.754C126.391 14.842 126.442 14.9667 126.442 15.128V24.104C126.442 25.5413 126.779 26.6047 127.454 27.294C128.129 27.9833 129.053 28.328 130.226 28.328C131.385 28.328 132.301 27.9833 132.976 27.294C133.665 26.6047 134.01 25.5413 134.01 24.104V15.128C134.01 14.9667 134.054 14.842 134.142 14.754C134.245 14.6513 134.369 14.6 134.516 14.6H135.66C135.807 14.6 135.924 14.6513 136.012 14.754C136.115 14.842 136.166 14.9667 136.166 15.128V24.06C136.166 25.4827 135.924 26.6487 135.44 27.558C134.971 28.4527 134.296 29.12 133.416 29.56C132.536 30 131.473 30.22 130.226 30.22ZM143.716 30C143.569 30 143.444 29.956 143.342 29.868C143.254 29.7653 143.21 29.6407 143.21 29.494V16.558H138.986C138.839 16.558 138.714 16.514 138.612 16.426C138.524 16.3233 138.48 16.1987 138.48 16.052V15.128C138.48 14.9667 138.524 14.842 138.612 14.754C138.714 14.6513 138.839 14.6 138.986 14.6H149.59C149.751 14.6 149.876 14.6513 149.964 14.754C150.066 14.842 150.118 14.9667 150.118 15.128V16.052C150.118 16.1987 150.066 16.3233 149.964 16.426C149.876 16.514 149.751 16.558 149.59 16.558H145.388V29.494C145.388 29.6407 145.336 29.7653 145.234 29.868C145.146 29.956 145.021 30 144.86 30H143.716Z"
                                    fill="#21201F" />
                                <rect width="14.9434" height="14.9433" rx="1"
                                    transform="matrix(0.705932 -0.70828 0.708096 0.706116 3 17.5859)" fill="#58B741" />
                                <rect width="14.9434" height="14.9433" rx="1"
                                    transform="matrix(0.705932 -0.70828 0.708096 0.706116 26.8701 17.5859)" fill="#398025" />
                                <rect width="11.9547" height="11.9546" rx="1"
                                    transform="matrix(0.705932 -0.70828 0.708096 0.706116 16.9238 29.5586)" fill="#67D34D" />
                            </svg>*/
                            ?>
                            <svg width="60" height="49" viewBox="0 0 60 49" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_7055_30678)">
                                    <path d="M28.436 15.9619H25.2334V19.1343H28.436V15.9619Z" fill="#50B83C" />
                                    <path d="M26.835 16.8681C26.835 16.8681 30.0903 12.4843 29.58 5.10254" stroke="#50B83C" stroke-width="0.865166" stroke-miterlimit="10" />
                                    <path d="M53.2163 16.5503H50.0137V19.7226H53.2163V16.5503Z" fill="#50B83C" />
                                    <path d="M51.6191 17.4521C51.6191 17.4521 54.8745 13.0683 54.3642 5.68652" stroke="#50B83C" stroke-width="0.865166" stroke-miterlimit="10" />
                                    <path d="M0.303272 11.2251C-0.281815 10.8111 -0.198231 9.40363 2.14212 7.25097C4.48246 5.0983 8.07657 1.1198 8.07657 1.1198C8.07657 1.1198 9.41391 -0.453297 10.4169 0.126266C11.4199 0.705829 13.5095 2.89771 12.9244 5.60815C12.3393 8.32294 8.16015 21.9013 7.32431 28.442C6.48848 34.9828 6.9064 34.4033 7.32431 35.3968C7.74223 36.3903 7.9094 37.8806 7.32431 37.9634C6.73923 38.0462 3.10113 37.7368 1.38986 35.3968C-0.572159 32.7125 1.95295 19.6266 4.64963 13.0379L6.77442 7.85232C6.77442 7.85232 2.13332 11.5781 1.2139 11.5781C0.694796 11.5781 0.303272 11.2251 0.303272 11.2251Z" fill="#50B83C" />
                                    <path d="M29.5356 2.25272C28.6074 0.67962 27.5252 0.832136 26.3199 1.39863C25.1145 1.96512 20.3502 6.34888 17.8207 11.2381C15.2956 16.1274 12.2514 24.6029 14.8337 30.4639C17.416 36.3206 22.8137 39.2227 27.4065 35.2965C31.9992 31.3703 35.5009 24.8862 35.9628 18.4064C36.4203 11.9223 36.3631 7.31627 33.4949 3.44671C31.6824 1.00644 31.1765 2.84535 31.0445 4.53611C30.9698 5.49914 31.1589 6.45782 31.5329 7.35113C32.0212 8.52333 32.5931 10.9941 31.8848 15.5739C31.0445 20.9905 25.0441 31.7233 20.8649 32.0544C19.4308 32.1677 18.111 26.0845 20.0599 20.7377C22.0571 15.2646 26.0867 9.53429 27.9827 7.99606C29.8788 6.46217 30.9126 4.58404 29.5312 2.25272H29.5356Z" fill="#50B83C" />
                                    <path d="M54.9896 2.989C54.0262 1.81244 53.6303 0.967065 51.5187 1.43769C49.4071 1.90831 40.5604 6.89778 38.5192 20.1798C36.478 33.4618 42.9448 36.8956 45.4611 37.3706C47.9774 37.8412 50.2913 37.5057 51.4483 36.1592C52.6053 34.8127 57.7787 28.5421 58.9357 23.4175C60.0926 18.2929 60.8405 10.0701 58.2538 5.95653C55.6671 1.84295 53.8986 4.61003 54.035 5.41619C54.1714 6.22235 56.5513 8.6539 55.8035 14.6543C54.7917 22.7726 48.9408 32.7166 45.7514 32.4857C43.3539 32.3114 43.2835 28.0627 44.1017 21.3912C44.92 14.7197 51.246 7.97847 52.4733 7.43812C53.7007 6.89778 56.2346 4.51416 54.9896 2.989Z" fill="#50B83C" />
                                    <path d="M0 44.6524C0 46.4041 1.31095 48.1646 3.77007 47.5545C6.2292 46.9445 18.7536 44.7526 29.1048 45.0576C39.456 45.3626 54.1095 48.1036 54.831 48.1036C55.5525 48.1036 55.856 46.8835 55.5481 46.378C55.2401 45.8725 54.4219 45.1578 52.2663 44.6524C50.1107 44.1469 34.8941 38.5996 17.6274 40.0856C3.30376 41.3144 0 40.9963 0 44.6524Z" fill="#50B83C" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_7055_30678">
                                        <rect width="60" height="49" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </a>
                        <button class="header__menu-toggle btn btn-primary btn_icon-left js-menu-toggle">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3 5C3 4.44772 3.44772 4 4 4H16C16.5523 4 17 4.44772 17 5C17 5.55228 16.5523 6 16 6H4C3.44772 6 3 5.55228 3 5Z"
                                    fill="white" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3 10C3 9.44772 3.44772 9 4 9H16C16.5523 9 17 9.44772 17 10C17 10.5523 16.5523 11 16 11H4C3.44772 11 3 10.5523 3 10Z"
                                    fill="white" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3 15C3 14.4477 3.44772 14 4 14H16C16.5523 14 17 14.4477 17 15C17 15.5523 16.5523 16 16 16H4C3.44772 16 3 15.5523 3 15Z"
                                    fill="white" />
                            </svg>
                            <span>
                                Каталог
                            </span>
                        </button>
                        <div class="header__search">
                            <?php echo do_shortcode('[fibosearch]'); ?>
                        </div>
                        <button class="header__to-call-toggle btn btn-secondary btn_icon-left js-popup-btn" data-popup="1">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M7.93919 9.29682L10.73 12.0945C11.0325 12.3978 11.5805 12.4411 12.191 12.1553C13.7447 11.5434 14.2616 11.3942 14.5567 11.6901L17.7647 14.906C18.1439 15.2862 18.0427 15.9194 17.6187 16.3445C15.4168 18.5518 11.8378 18.5518 9.63592 16.3445L3.64316 10.3369C1.45228 8.1406 1.45228 4.58739 3.64316 2.39108C4.07617 1.95701 4.74112 1.85021 5.13462 2.24468L8.34266 5.46065C8.62071 5.73939 8.47162 6.25857 7.86454 7.80549C7.58232 8.40933 7.62754 8.9844 7.93919 9.29682Z"
                                    fill="#21201F" />
                            </svg>
                            <span>
                                Заказать звонок
                            </span>
                        </button>
                        <?php echo do_shortcode('[ti_wishlist_products_counter]'); ?>
                        <?php
                        global $woocommerce;
                        $c_total = number_format($woocommerce->cart->subtotal, 0, '', ' ');
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
                                <path
                                    d="M3 1C2.44772 1 2 1.44772 2 2C2 2.55228 2.44772 3 3 3H4.21922L4.52478 4.22224C4.52799 4.23637 4.5315 4.25039 4.5353 4.26429L5.89253 9.69321L4.99995 10.5858C3.74002 11.8457 4.63235 14 6.41416 14H15C15.5522 14 16 13.5523 16 13C16 12.4477 15.5522 12 15 12L6.41417 12L7.41416 11H14C14.3788 11 14.725 10.786 14.8944 10.4472L17.8944 4.44721C18.0494 4.13723 18.0329 3.76909 17.8507 3.47427C17.6684 3.17945 17.3466 3 17 3H6.28078L5.97014 1.75746C5.85885 1.3123 5.45887 1 5 1H3Z"
                                    fill="<?php if ($c_total == 0) {
                                                echo '#21201F';
                                            } else {
                                                echo '#fff';
                                            } ?>" />
                                <path
                                    d="M16 16.5C16 17.3284 15.3284 18 14.5 18C13.6716 18 13 17.3284 13 16.5C13 15.6716 13.6716 15 14.5 15C15.3284 15 16 15.6716 16 16.5Z"
                                    fill="<?php if ($c_total == 0) {
                                                echo '#21201F';
                                            } else {
                                                echo '#fff';
                                            } ?>" />
                                <path
                                    d="M6.5 18C7.32843 18 8 17.3284 8 16.5C8 15.6716 7.32843 15 6.5 15C5.67157 15 5 15.6716 5 16.5C5 17.3284 5.67157 18 6.5 18Z"
                                    fill="<?php if ($c_total == 0) {
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
                                    <?php echo $c_total; ?>
                                </span>
                            <?php
                            }
                            ?>
                        </a>
                        <button class="header__search-toggle btn btn-icon btn-primary">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z"
                                    stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="header__bottom">
                        <nav class="header__nav">
                            <ul class="header__nav-list">
                                <li class="header__nav-list-item">
                                    <a class="link link_big link-secondary text" href="/oplata/">
                                        Оплата
                                    </a>
                                </li>
                                <li class="header__nav-list-item">
                                    <a class="link link_big link-secondary text" href="/dostavka/">
                                        Доставка
                                    </a>
                                </li>
                                <li class="header__nav-list-item">
                                    <a class="link link_big link-secondary text" href="/otzyvy/">
                                        Отзывы
                                    </a>
                                </li>
                                <li class="header__nav-list-item">
                                    <a class="link link_big link-secondary text" href="/blog/">
                                        Блог
                                    </a>
                                </li>
                                <li class="header__nav-list-item">
                                    <a class="link link_big link-secondary text" href="/kontakty/">
                                        Контакты
                                    </a>
                                </li>
                            </ul>
                            <ul class="header__nav-list">
                                <li class="header__nav-list-item">
                                    <a class="link link_big link-secondary text" href="/yur-liczam/">
                                        Покупайте как юрлицо
                                    </a>
                                </li>
                                <li class="header__nav-list-item header__nav-list-item--has-sub-nav">
                                    <a class="link link_big link-secondary text" href="/skupka-tehniki/">
                                        Скупка техники
                                    </a>
                                    <ul class="header__sub-nav">
                                        <li>
                                            <a class="link link_big link-secondary text" href="/skupka-smartfonov/">
                                                Скупка смартфонов
                                            </a>
                                            <a class="link link_big link-secondary text" href="/skupka-bytovoj-tehniki/">
                                                Скупка бытовой техники
                                            </a>
                                            <a class="link link_big link-secondary text" href="/skupka-kompyuternoi-tehniki-i-komplektuyushhih/">
                                                Скупка компьютерной техники и комплектующих
                                            </a>
                                            <a class="link link_big link-secondary text" href="/skupka-noutbukov/">
                                                Скупка ноутбуков
                                            </a>
                                            <a class="link link_big link-secondary text" href="/skupka-iphone/">
                                                Скупка iPhone
                                            </a>
                                            <a class="link link_big link-secondary text" href="/skupka-b-u-televizorov/">
                                                Скупка б/у телевизоров
                                            </a>
                                            <a class="link link_big link-secondary text" href="/skupka-tehniki-apple/">
                                                Скупка техники Apple
                                            </a>
                                            <a class="link link_big link-secondary text" href="/skupka-monitorov-v-minske/">
                                                Скупка мониторов в Минске
                                            </a>
                                            <a class="link link_big link-secondary text" href="/skupka-planshetov-v-minske/">
                                                Скупка планшетов в Минске
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="header__nav-list-item">
                                    <a class="link link_big link-secondary text" href="/trade-in/">
                                        Trade-in
                                    </a>
                                </li>
                                <li class="header__nav-list-item">
                                    <div class="dropdown js-dropdown">
                                        <button type="button" class="dropdown__btn">
                                            <span class="text-base text">+375 44 415 74 00</span>
                                            <svg class="dropdown__arrow" width="20" height="20" viewBox="0 0 20 20"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M5.29289 7.29289C5.68342 6.90237 6.31658 6.90237 6.70711 7.29289L10 10.5858L13.2929 7.29289C13.6834 6.90237 14.3166 6.90237 14.7071 7.29289C15.0976 7.68342 15.0976 8.31658 14.7071 8.70711L10.7071 12.7071C10.3166 13.0976 9.68342 13.0976 9.29289 12.7071L5.29289 8.70711C4.90237 8.31658 4.90237 7.68342 5.29289 7.29289Z"
                                                    fill="#B8B8B8" />
                                            </svg>
                                        </button>
                                        <ul class="dropdown__list">
                                            <li class="dropdown__list-item">
                                                <a href="tel:+375 44 415 74 00" class="dropdown__list-item-btn text-sm text">
                                                    +375 44 415 74 00
                                                </a>
                                            </li>
                                            <li class="dropdown__list-item">
                                                <a href="tel:+375 33 375 74 00" class="dropdown__list-item-btn text-sm text">
                                                    +375 33 375 74 00
                                                </a>
                                            </li>
                                            <li class="dropdown__list-item">
                                                <a href="tel:+375 25 977 74 00" class="dropdown__list-item-btn text-sm text">
                                                    +375 25 977 74 00
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </header>
            <ul class="bar">
                <li class="bar__item">
                    <a href="/" class="bar__item-link btn btn-secondary btn-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12.8489 2.75137C12.3803 2.28275 11.6205 2.28275 11.1519 2.75137L2.75186 11.1514C2.28323 11.62 2.28323 12.3798 2.75186 12.8484C3.22049 13.3171 3.98029 13.3171 4.44892 12.8484L4.80039 12.497V20.3999C4.80039 21.0626 5.33765 21.5999 6.00039 21.5999H8.40039C9.06313 21.5999 9.60039 21.0626 9.60039 20.3999V17.9999C9.60039 17.3372 10.1376 16.7999 10.8004 16.7999H13.2004C13.8631 16.7999 14.4004 17.3372 14.4004 17.9999V20.3999C14.4004 21.0626 14.9376 21.5999 15.6004 21.5999H18.0004C18.6631 21.5999 19.2004 21.0626 19.2004 20.3999V12.497L19.5519 12.8484C20.0205 13.3171 20.7803 13.3171 21.2489 12.8484C21.7175 12.3798 21.7175 11.62 21.2489 11.1514L12.8489 2.75137Z"
                                fill="#21201F" />
                        </svg>
                    </a>
                </li>
                <li class="bar__item">
                    <button type="button"
                        class="bar__item-link btn btn-secondary btn-icon js-menu-toggle bar__menu-toggle">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M3.59961 5.9998C3.59961 5.33706 4.13687 4.7998 4.79961 4.7998H19.1996C19.8624 4.7998 20.3996 5.33706 20.3996 5.9998C20.3996 6.66255 19.8624 7.1998 19.1996 7.1998H4.79961C4.13687 7.1998 3.59961 6.66255 3.59961 5.9998Z"
                                fill="#21201F" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M3.59961 11.9998C3.59961 11.3371 4.13687 10.7998 4.79961 10.7998H19.1996C19.8624 10.7998 20.3996 11.3371 20.3996 11.9998C20.3996 12.6625 19.8624 13.1998 19.1996 13.1998H4.79961C4.13687 13.1998 3.59961 12.6625 3.59961 11.9998Z"
                                fill="#21201F" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M3.59961 17.9998C3.59961 17.3371 4.13687 16.7998 4.79961 16.7998H19.1996C19.8624 16.7998 20.3996 17.3371 20.3996 17.9998C20.3996 18.6625 19.8624 19.1998 19.1996 19.1998H4.79961C4.13687 19.1998 3.59961 18.6625 3.59961 17.9998Z"
                                fill="#21201F" />
                        </svg>
                    </button>
                </li>
                <li class="bar__item">
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
                            <path
                                d="M3.60039 1.2002C2.93765 1.2002 2.40039 1.73745 2.40039 2.4002C2.40039 3.06294 2.93765 3.6002 3.60039 3.6002H5.06346L5.43013 5.06689C5.43398 5.08384 5.43819 5.10067 5.44275 5.11734L7.07142 11.632L6.00033 12.7031C4.48842 14.215 5.55921 16.8002 7.69739 16.8002H18.0003C18.6631 16.8002 19.2003 16.2629 19.2003 15.6002C19.2003 14.9375 18.6631 14.4002 18.0003 14.4002L7.69739 14.4002L8.89739 13.2002H16.8004C17.2549 13.2002 17.6704 12.9434 17.8737 12.5368L21.4737 5.33685C21.6597 4.96487 21.6398 4.5231 21.4212 4.16932C21.2025 3.81554 20.8163 3.6002 20.4004 3.6002H7.53732L7.16456 2.10915C7.03101 1.57495 6.55103 1.2002 6.00039 1.2002H3.60039Z"
                                fill="<?php if ($c_total == 0) {
                                            echo '#21201F';
                                        } else {
                                            echo '#fff';
                                        } ?>" />
                            <path
                                d="M19.2004 19.8002C19.2004 20.7943 18.3945 21.6002 17.4004 21.6002C16.4063 21.6002 15.6004 20.7943 15.6004 19.8002C15.6004 18.8061 16.4063 18.0002 17.4004 18.0002C18.3945 18.0002 19.2004 18.8061 19.2004 19.8002Z"
                                fill="<?php if ($c_total == 0) {
                                            echo '#21201F';
                                        } else {
                                            echo '#fff';
                                        } ?>" />
                            <path
                                d="M7.80039 21.6002C8.7945 21.6002 9.60039 20.7943 9.60039 19.8002C9.60039 18.8061 8.7945 18.0002 7.80039 18.0002C6.80628 18.0002 6.00039 18.8061 6.00039 19.8002C6.00039 20.7943 6.80628 21.6002 7.80039 21.6002Z"
                                fill="<?php if ($c_total == 0) {
                                            echo '#21201F';
                                        } else {
                                            echo '#fff';
                                        } ?>" />
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
                </li>
                <li class="bar__item">
                    <button type="button" class="bar__item-link btn btn-secondary btn-icon js-popup-btn" data-popup="1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M9.52742 11.1561L12.8764 14.5133C13.2394 14.8773 13.897 14.9293 14.6296 14.5863C16.494 13.852 17.1143 13.673 17.4684 14.028L21.3181 17.8871C21.7731 18.3433 21.6517 19.1032 21.1428 19.6133C18.5005 22.2621 14.2058 22.2621 11.5635 19.6133L4.37219 12.4042C1.74312 9.76863 1.74312 5.50477 4.37219 2.8692C4.89179 2.34832 5.68974 2.22015 6.16194 2.69352L10.0116 6.55268C10.3452 6.88717 10.1663 7.51019 9.43784 9.36649C9.09918 10.0911 9.15344 10.7812 9.52742 11.1561Z"
                                fill="#21201F" />
                        </svg>
                    </button>
                </li>
                <li class="bar__item">
                    <?php echo do_shortcode('[ti_wishlist_products_counter]'); ?>
                </li>
            </ul>
            <div class="cartP js-cartP" onclick="onCartCloseBack(event)">
                <div class="cartP__inner">
                    <div class="cartP__container hide-scroll">
                        <button class="cartP__btn btn-icon" onclick="onCartClose()">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 13L13 1M1 1L13 13" stroke="#8F8F8F" stroke-width="1.2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <h3 class="cartP__title text-3xl">
                            Товар добавлен в корзину
                        </h3>
                        <?php woocommerce_mini_cart(); ?>
                    </div>
                </div>
            </div>
            <div class="menu js-menu">
                <div class="menu__inner layout">
                    <div class="menu__content">
                        <div class="menu__banner js-menu-banner">
                            <img class="menu__banner-img menu__banner-img--base" src="<?php nout_image_directory() ?>menu-b-base.jpg"
                                alt="Баннер">
                            <img class="menu__banner-img menu__banner-img--long" src="<?php nout_image_directory() ?>menu-b-long.jpg"
                                alt="Баннер">
                        </div>
                        <div class="menu__mobile">
                            <ul class="menu__mobile-list">
                                <li class="menu__mobile-list-item">
                                    <button type="button" class="btn btn_icon-left text text-sm js-mobile-menu-btn">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3.33325 5H16.6666M3.33325 10H16.6666M3.33325 15H16.6666"
                                                stroke="#21201F" stroke-width="1.2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>

                                        Каталог
                                    </button>
                                </li>
                                <li class="menu__mobile-list-item">
                                    <a href="/oplata/" class="btn btn_icon-left text-sm text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M14.1667 7.49984V5.83317C14.1667 4.9127 13.4205 4.1665 12.5 4.1665H4.16667C3.24619 4.1665 2.5 4.9127 2.5 5.83317V10.8332C2.5 11.7536 3.24619 12.4998 4.16667 12.4998H5.83333M7.5 15.8332H15.8333C16.7538 15.8332 17.5 15.087 17.5 14.1665V9.1665C17.5 8.24603 16.7538 7.49984 15.8333 7.49984H7.5C6.57953 7.49984 5.83333 8.24603 5.83333 9.1665V14.1665C5.83333 15.087 6.57953 15.8332 7.5 15.8332ZM13.3333 11.6665C13.3333 12.587 12.5871 13.3332 11.6667 13.3332C10.7462 13.3332 10 12.587 10 11.6665C10 10.746 10.7462 9.99984 11.6667 9.99984C12.5871 9.99984 13.3333 10.746 13.3333 11.6665Z"
                                                stroke="#21201F" stroke-width="1.2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        Оплата
                                    </a>
                                </li>
                                <li class="menu__mobile-list-item">
                                    <a href="/dostavka/" class="btn btn_icon-left text-sm text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M10.8333 13.3332V4.99984C10.8333 4.5396 10.4602 4.1665 10 4.1665H3.33333C2.8731 4.1665 2.5 4.5396 2.5 4.99984V13.3332C2.5 13.7934 2.8731 14.1665 3.33333 14.1665H4.16667M10.8333 13.3332C10.8333 13.7934 10.4602 14.1665 10 14.1665H7.5M10.8333 13.3332L10.8333 6.6665C10.8333 6.20627 11.2064 5.83317 11.6667 5.83317H13.8215C14.0425 5.83317 14.2545 5.92097 14.4107 6.07725L17.2559 8.92243C17.4122 9.07871 17.5 9.29067 17.5 9.51168V13.3332C17.5 13.7934 17.1269 14.1665 16.6667 14.1665H15.8333M10.8333 13.3332C10.8333 13.7934 11.2064 14.1665 11.6667 14.1665H12.5M4.16667 14.1665C4.16667 15.087 4.91286 15.8332 5.83333 15.8332C6.75381 15.8332 7.5 15.087 7.5 14.1665M4.16667 14.1665C4.16667 13.246 4.91286 12.4998 5.83333 12.4998C6.75381 12.4998 7.5 13.246 7.5 14.1665M12.5 14.1665C12.5 15.087 13.2462 15.8332 14.1667 15.8332C15.0871 15.8332 15.8333 15.087 15.8333 14.1665M12.5 14.1665C12.5 13.246 13.2462 12.4998 14.1667 12.4998C15.0871 12.4998 15.8333 13.246 15.8333 14.1665"
                                                stroke="#21201F" stroke-width="1.2" />
                                        </svg>
                                        Доставка
                                    </a>
                                </li>
                                <li class="menu__mobile-list-item">
                                    <a href="/otzyvy/" class="btn btn_icon-left text-sm text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M5.83333 6.66683H14.1667M5.83333 10.0002H9.16667M10 16.6668L6.66667 13.3335H4.16667C3.24619 13.3335 2.5 12.5873 2.5 11.6668V5.00016C2.5 4.07969 3.24619 3.3335 4.16667 3.3335H15.8333C16.7538 3.3335 17.5 4.07969 17.5 5.00016V11.6668C17.5 12.5873 16.7538 13.3335 15.8333 13.3335H13.3333L10 16.6668Z"
                                                stroke="#21201F" stroke-width="1.2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        Отзывы
                                    </a>
                                </li>
                                <li class="menu__mobile-list-item">
                                    <a href="/blog/" class="btn btn_icon-left text-sm text">
                                        <svg fill="#000000" width="20" height="20" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <g>
                                                    <g>
                                                        <g>
                                                            <path d="M93.238,220.968h172.463c4.466,0,8.084-3.619,8.084-8.084s-3.618-8.084-8.084-8.084H93.238 c-4.466,0-8.084,3.619-8.084,8.084S88.772,220.968,93.238,220.968z"></path>
                                                            <path d="M93.238,177.853h97.01c4.466,0,8.084-3.619,8.084-8.084s-3.618-8.084-8.084-8.084h-97.01 c-4.466,0-8.084,3.619-8.084,8.084S88.772,177.853,93.238,177.853z"></path>
                                                            <path d="M114.796,398.282h280.253c16.344,0,29.642-13.298,29.642-29.642v-91.082c0-5.206-1.371-10.333-3.968-14.822 c-2.233-3.865-7.179-5.189-11.044-2.953c-3.866,2.233-5.188,7.179-2.953,11.044c1.176,2.034,1.797,4.362,1.797,6.732v91.082 c0,7.43-6.044,13.474-13.474,13.474H114.796c-7.43,0-13.474-6.044-13.474-13.474v-91.082c0-7.43,6.044-13.474,13.474-13.474 h150.905c4.466,0,8.084-3.619,8.084-8.084c0-4.466-3.618-8.084-8.084-8.084H114.796c-16.344,0-29.642,13.298-29.642,29.642 v91.082C85.154,384.984,98.452,398.282,114.796,398.282z"></path>
                                                            <path d="M477.068,412.237c1.684-5.067,2.596-10.484,2.596-16.11V158.989c0-28.231-22.969-51.2-51.2-51.2h-45.81V29.642 C382.653,13.298,369.355,0,353.01,0h-21.558c-16.344,0-29.642,13.298-29.642,29.642v78.147H83.537 c-28.231,0-51.2,22.969-51.2,51.2v237.137c0,5.627,0.912,11.043,2.596,16.11C14.43,419.065,0,438.368,0,460.8 C0,489.031,22.969,512,51.2,512h409.6c28.231,0,51.2-22.969,51.2-51.2C512,438.368,497.57,419.065,477.068,412.237z M317.979,29.642c0-7.43,6.044-13.474,13.474-13.474h21.558c7.43,0,13.474,6.044,13.474,13.474v13.474h-48.505V29.642z M366.484,59.284v188.632h-16.168V59.284H366.484z M317.979,59.284h16.168v188.632h-16.168V59.284z M317.979,264.084h48.505 v20.66c0,0.579-0.192,1.153-0.539,1.617l-21.558,28.743c-0.704,0.938-1.652,1.078-2.156,1.078c-0.503,0-1.452-0.14-2.156-1.078 l-21.557-28.741c-0.348-0.466-0.54-1.04-0.54-1.619V264.084z M48.505,158.989c0-19.317,15.715-35.032,35.032-35.032h218.274 v37.726h-57.667c-4.466,0-8.084,3.619-8.084,8.084s3.618,8.084,8.084,8.084h57.667v106.892c0,4.053,1.339,8.071,3.773,11.318 l21.558,28.743c3.596,4.796,9.096,7.545,15.091,7.545c5.994,0,11.495-2.75,15.091-7.545l21.559-28.745 c2.433-3.244,3.772-7.263,3.772-11.316v-63.776h33.954c4.466,0,8.084-3.619,8.084-8.084s-3.619-8.084-8.084-8.084h-33.954 v-26.947h33.954c4.466,0,8.084-3.619,8.084-8.084s-3.619-8.084-8.084-8.084h-33.954v-37.726h45.81 c19.317,0,35.032,15.715,35.032,35.032v237.137c0,19.317-15.715,35.032-35.032,35.032H83.537 c-19.317,0-35.032-15.715-35.032-35.032V158.989z M460.8,495.832H51.2c-19.317,0-35.032-15.715-35.032-35.032 c0-16.257,11.071-30.113,26.426-33.963c9.349,12.434,24.222,20.49,40.943,20.49h344.926c16.72,0,31.594-8.056,40.943-20.49 c15.355,3.85,26.426,17.707,26.426,33.963C495.832,480.117,480.117,495.832,460.8,495.832z"></path>
                                                            <path d="M235.52,463.495h-1.078c-4.466,0-8.084,3.618-8.084,8.084c0,4.466,3.618,8.084,8.084,8.084h1.078 c4.466,0,8.084-3.618,8.084-8.084C243.604,467.113,239.986,463.495,235.52,463.495z"></path>
                                                            <path d="M433.853,463.495H272.168c-4.466,0-8.084,3.618-8.084,8.084c0,4.466,3.618,8.084,8.084,8.084h161.684 c4.466,0,8.084-3.618,8.084-8.084C441.937,467.113,438.318,463.495,433.853,463.495z"></path>
                                                            <path d="M202.105,463.495h-1.078c-4.466,0-8.084,3.618-8.084,8.084c0,4.466,3.618,8.084,8.084,8.084h1.078 c4.466,0,8.084-3.618,8.084-8.084C210.189,467.113,206.571,463.495,202.105,463.495z"></path>
                                                            <path d="M164.379,463.495H78.147c-4.466,0-8.084,3.618-8.084,8.084c0,4.466,3.618,8.084,8.084,8.084h86.232 c4.466,0,8.084-3.618,8.084-8.084C172.463,467.113,168.845,463.495,164.379,463.495z"></path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                        Блог
                                    </a>
                                </li>
                                <li class="menu__mobile-list-item">
                                    <a href="/kontakty/" class="btn btn_icon-left text-sm text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M2.5 4.16667C2.5 3.24619 3.24619 2.5 4.16667 2.5H6.89937C7.25806 2.5 7.57651 2.72953 7.68994 3.06981L8.93811 6.81434C9.06926 7.20777 8.89115 7.63776 8.52022 7.82322L6.63917 8.76375C7.55771 10.801 9.19898 12.4423 11.2363 13.3608L12.1768 11.4798C12.3622 11.1088 12.7922 10.9307 13.1857 11.0619L16.9302 12.3101C17.2705 12.4235 17.5 12.7419 17.5 13.1006V15.8333C17.5 16.7538 16.7538 17.5 15.8333 17.5H15C8.09644 17.5 2.5 11.9036 2.5 5V4.16667Z"
                                                stroke="#21201F" stroke-width="1.2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>

                                        Контакты
                                    </a>
                                </li>
                                <li class="menu__mobile-list-item">
                                    <a href="/yur-liczam/" class="btn btn_icon-left text-sm text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M13.3334 5.83333C13.3334 7.67428 11.841 9.16667 10.0001 9.16667C8.15913 9.16667 6.66675 7.67428 6.66675 5.83333C6.66675 3.99238 8.15913 2.5 10.0001 2.5C11.841 2.5 13.3334 3.99238 13.3334 5.83333Z"
                                                stroke="#21201F" stroke-width="1.2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M10.0001 11.6667C6.77842 11.6667 4.16675 14.2783 4.16675 17.5H15.8334C15.8334 14.2783 13.2217 11.6667 10.0001 11.6667Z"
                                                stroke="#21201F" stroke-width="1.2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        Покупайте как юрлицо
                                    </a>
                                </li>
                                <li class="menu__mobile-list-item mobile-menu-accordion">
                                    <a href="/skupka-tehniki/" class="btn btn_icon-left text-sm text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M11.7124 7.89323C11.9295 8.14351 12.3085 8.17035 12.5588 7.95318C12.8091 7.73601 12.8359 7.35706 12.6187 7.10677L11.7124 7.89323ZM8.28765 12.1068C8.07048 11.8565 7.69153 11.8296 7.44125 12.0468C7.19096 12.264 7.16412 12.6429 7.38129 12.8932L8.28765 12.1068ZM10.6 5.83333C10.6 5.50196 10.3314 5.23333 10 5.23333C9.66863 5.23333 9.4 5.50196 9.4 5.83333H10.6ZM9.4 14.1667C9.39999 14.498 9.66862 14.7667 9.99999 14.7667C10.3314 14.7667 10.6 14.498 10.6 14.1667L9.4 14.1667ZM16.9 10C16.9 13.8108 13.8108 16.9 10 16.9V18.1C14.4735 18.1 18.1 14.4735 18.1 10H16.9ZM10 16.9C6.18923 16.9 3.1 13.8108 3.1 10H1.9C1.9 14.4735 5.52649 18.1 10 18.1V16.9ZM3.1 10C3.1 6.18923 6.18923 3.1 10 3.1V1.9C5.52649 1.9 1.9 5.52649 1.9 10H3.1ZM10 3.1C13.8108 3.1 16.9 6.18923 16.9 10H18.1C18.1 5.52649 14.4735 1.9 10 1.9V3.1ZM10 9.4C9.41158 9.4 8.906 9.23991 8.56505 9.01261C8.21991 8.78252 8.1 8.53054 8.1 8.33333H6.9C6.9 9.0566 7.33973 9.63795 7.89941 10.0111C8.46329 10.387 9.20771 10.6 10 10.6V9.4ZM8.1 8.33333C8.1 8.13613 8.21991 7.88415 8.56505 7.65405C8.906 7.42675 9.41158 7.26667 10 7.26667V6.06667C9.20771 6.06667 8.46329 6.27968 7.89941 6.65559C7.33973 7.02871 6.9 7.61007 6.9 8.33333H8.1ZM10 7.26667C10.8014 7.26667 11.4231 7.5599 11.7124 7.89323L12.6187 7.10677C12.0434 6.44376 11.0493 6.06667 10 6.06667V7.26667ZM10 10.6C10.5884 10.6 11.094 10.7601 11.435 10.9874C11.7801 11.2175 11.9 11.4695 11.9 11.6667H13.1C13.1 10.9434 12.6603 10.362 12.1006 9.98892C11.5367 9.61301 10.7923 9.4 10 9.4V10.6ZM9.4 5.83333V6.66667H10.6V5.83333H9.4ZM9.40002 13.3333L9.4 14.1667L10.6 14.1667L10.6 13.3333L9.40002 13.3333ZM10 12.7333C9.19866 12.7333 8.57688 12.4401 8.28765 12.1068L7.38129 12.8932C7.95658 13.5562 8.95069 13.9333 10 13.9333L10 12.7333ZM11.9 11.6667C11.9 11.8639 11.7801 12.1159 11.435 12.3459C11.094 12.5732 10.5884 12.7333 10 12.7333V13.9333C10.7923 13.9333 11.5367 13.7203 12.1006 13.3444C12.6603 12.9713 13.1 12.3899 13.1 11.6667H11.9ZM9.4 6.66667L9.40002 13.3333L10.6 13.3333L10.6 6.66667L9.4 6.66667Z"
                                                fill="#21201F" />
                                        </svg>
                                        Скупка техники

                                        <button class="mobile-menu-accordion__toggle">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11.082 5.25L6.9987 9.33333L2.91536 5.25" stroke="#DEDEDE" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </a>
                                    <ul class="mobile-menu-accordion__subnav">
                                        <li>
                                            <a href="/skupka-smartfonov/" class="btn btn_icon-left text-sm text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.7124 7.89323C11.9295 8.14351 12.3085 8.17035 12.5588 7.95318C12.8091 7.73601 12.8359 7.35706 12.6187 7.10677L11.7124 7.89323ZM8.28765 12.1068C8.07048 11.8565 7.69153 11.8296 7.44125 12.0468C7.19096 12.264 7.16412 12.6429 7.38129 12.8932L8.28765 12.1068ZM10.6 5.83333C10.6 5.50196 10.3314 5.23333 10 5.23333C9.66863 5.23333 9.4 5.50196 9.4 5.83333H10.6ZM9.4 14.1667C9.39999 14.498 9.66862 14.7667 9.99999 14.7667C10.3314 14.7667 10.6 14.498 10.6 14.1667L9.4 14.1667ZM16.9 10C16.9 13.8108 13.8108 16.9 10 16.9V18.1C14.4735 18.1 18.1 14.4735 18.1 10H16.9ZM10 16.9C6.18923 16.9 3.1 13.8108 3.1 10H1.9C1.9 14.4735 5.52649 18.1 10 18.1V16.9ZM3.1 10C3.1 6.18923 6.18923 3.1 10 3.1V1.9C5.52649 1.9 1.9 5.52649 1.9 10H3.1ZM10 3.1C13.8108 3.1 16.9 6.18923 16.9 10H18.1C18.1 5.52649 14.4735 1.9 10 1.9V3.1ZM10 9.4C9.41158 9.4 8.906 9.23991 8.56505 9.01261C8.21991 8.78252 8.1 8.53054 8.1 8.33333H6.9C6.9 9.0566 7.33973 9.63795 7.89941 10.0111C8.46329 10.387 9.20771 10.6 10 10.6V9.4ZM8.1 8.33333C8.1 8.13613 8.21991 7.88415 8.56505 7.65405C8.906 7.42675 9.41158 7.26667 10 7.26667V6.06667C9.20771 6.06667 8.46329 6.27968 7.89941 6.65559C7.33973 7.02871 6.9 7.61007 6.9 8.33333H8.1ZM10 7.26667C10.8014 7.26667 11.4231 7.5599 11.7124 7.89323L12.6187 7.10677C12.0434 6.44376 11.0493 6.06667 10 6.06667V7.26667ZM10 10.6C10.5884 10.6 11.094 10.7601 11.435 10.9874C11.7801 11.2175 11.9 11.4695 11.9 11.6667H13.1C13.1 10.9434 12.6603 10.362 12.1006 9.98892C11.5367 9.61301 10.7923 9.4 10 9.4V10.6ZM9.4 5.83333V6.66667H10.6V5.83333H9.4ZM9.40002 13.3333L9.4 14.1667L10.6 14.1667L10.6 13.3333L9.40002 13.3333ZM10 12.7333C9.19866 12.7333 8.57688 12.4401 8.28765 12.1068L7.38129 12.8932C7.95658 13.5562 8.95069 13.9333 10 13.9333L10 12.7333ZM11.9 11.6667C11.9 11.8639 11.7801 12.1159 11.435 12.3459C11.094 12.5732 10.5884 12.7333 10 12.7333V13.9333C10.7923 13.9333 11.5367 13.7203 12.1006 13.3444C12.6603 12.9713 13.1 12.3899 13.1 11.6667H11.9ZM9.4 6.66667L9.40002 13.3333L10.6 13.3333L10.6 6.66667L9.4 6.66667Z"
                                                        fill="#21201F" />
                                                </svg>
                                                Скупка смартфонов
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/skupka-bytovoj-tehniki/" class="btn btn_icon-left text-sm text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.7124 7.89323C11.9295 8.14351 12.3085 8.17035 12.5588 7.95318C12.8091 7.73601 12.8359 7.35706 12.6187 7.10677L11.7124 7.89323ZM8.28765 12.1068C8.07048 11.8565 7.69153 11.8296 7.44125 12.0468C7.19096 12.264 7.16412 12.6429 7.38129 12.8932L8.28765 12.1068ZM10.6 5.83333C10.6 5.50196 10.3314 5.23333 10 5.23333C9.66863 5.23333 9.4 5.50196 9.4 5.83333H10.6ZM9.4 14.1667C9.39999 14.498 9.66862 14.7667 9.99999 14.7667C10.3314 14.7667 10.6 14.498 10.6 14.1667L9.4 14.1667ZM16.9 10C16.9 13.8108 13.8108 16.9 10 16.9V18.1C14.4735 18.1 18.1 14.4735 18.1 10H16.9ZM10 16.9C6.18923 16.9 3.1 13.8108 3.1 10H1.9C1.9 14.4735 5.52649 18.1 10 18.1V16.9ZM3.1 10C3.1 6.18923 6.18923 3.1 10 3.1V1.9C5.52649 1.9 1.9 5.52649 1.9 10H3.1ZM10 3.1C13.8108 3.1 16.9 6.18923 16.9 10H18.1C18.1 5.52649 14.4735 1.9 10 1.9V3.1ZM10 9.4C9.41158 9.4 8.906 9.23991 8.56505 9.01261C8.21991 8.78252 8.1 8.53054 8.1 8.33333H6.9C6.9 9.0566 7.33973 9.63795 7.89941 10.0111C8.46329 10.387 9.20771 10.6 10 10.6V9.4ZM8.1 8.33333C8.1 8.13613 8.21991 7.88415 8.56505 7.65405C8.906 7.42675 9.41158 7.26667 10 7.26667V6.06667C9.20771 6.06667 8.46329 6.27968 7.89941 6.65559C7.33973 7.02871 6.9 7.61007 6.9 8.33333H8.1ZM10 7.26667C10.8014 7.26667 11.4231 7.5599 11.7124 7.89323L12.6187 7.10677C12.0434 6.44376 11.0493 6.06667 10 6.06667V7.26667ZM10 10.6C10.5884 10.6 11.094 10.7601 11.435 10.9874C11.7801 11.2175 11.9 11.4695 11.9 11.6667H13.1C13.1 10.9434 12.6603 10.362 12.1006 9.98892C11.5367 9.61301 10.7923 9.4 10 9.4V10.6ZM9.4 5.83333V6.66667H10.6V5.83333H9.4ZM9.40002 13.3333L9.4 14.1667L10.6 14.1667L10.6 13.3333L9.40002 13.3333ZM10 12.7333C9.19866 12.7333 8.57688 12.4401 8.28765 12.1068L7.38129 12.8932C7.95658 13.5562 8.95069 13.9333 10 13.9333L10 12.7333ZM11.9 11.6667C11.9 11.8639 11.7801 12.1159 11.435 12.3459C11.094 12.5732 10.5884 12.7333 10 12.7333V13.9333C10.7923 13.9333 11.5367 13.7203 12.1006 13.3444C12.6603 12.9713 13.1 12.3899 13.1 11.6667H11.9ZM9.4 6.66667L9.40002 13.3333L10.6 13.3333L10.6 6.66667L9.4 6.66667Z"
                                                        fill="#21201F" />
                                                </svg>
                                                Скупка бытовой техники
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/skupka-kompyuternoi-tehniki-i-komplektuyushhih/" class="btn btn_icon-left text-sm text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.7124 7.89323C11.9295 8.14351 12.3085 8.17035 12.5588 7.95318C12.8091 7.73601 12.8359 7.35706 12.6187 7.10677L11.7124 7.89323ZM8.28765 12.1068C8.07048 11.8565 7.69153 11.8296 7.44125 12.0468C7.19096 12.264 7.16412 12.6429 7.38129 12.8932L8.28765 12.1068ZM10.6 5.83333C10.6 5.50196 10.3314 5.23333 10 5.23333C9.66863 5.23333 9.4 5.50196 9.4 5.83333H10.6ZM9.4 14.1667C9.39999 14.498 9.66862 14.7667 9.99999 14.7667C10.3314 14.7667 10.6 14.498 10.6 14.1667L9.4 14.1667ZM16.9 10C16.9 13.8108 13.8108 16.9 10 16.9V18.1C14.4735 18.1 18.1 14.4735 18.1 10H16.9ZM10 16.9C6.18923 16.9 3.1 13.8108 3.1 10H1.9C1.9 14.4735 5.52649 18.1 10 18.1V16.9ZM3.1 10C3.1 6.18923 6.18923 3.1 10 3.1V1.9C5.52649 1.9 1.9 5.52649 1.9 10H3.1ZM10 3.1C13.8108 3.1 16.9 6.18923 16.9 10H18.1C18.1 5.52649 14.4735 1.9 10 1.9V3.1ZM10 9.4C9.41158 9.4 8.906 9.23991 8.56505 9.01261C8.21991 8.78252 8.1 8.53054 8.1 8.33333H6.9C6.9 9.0566 7.33973 9.63795 7.89941 10.0111C8.46329 10.387 9.20771 10.6 10 10.6V9.4ZM8.1 8.33333C8.1 8.13613 8.21991 7.88415 8.56505 7.65405C8.906 7.42675 9.41158 7.26667 10 7.26667V6.06667C9.20771 6.06667 8.46329 6.27968 7.89941 6.65559C7.33973 7.02871 6.9 7.61007 6.9 8.33333H8.1ZM10 7.26667C10.8014 7.26667 11.4231 7.5599 11.7124 7.89323L12.6187 7.10677C12.0434 6.44376 11.0493 6.06667 10 6.06667V7.26667ZM10 10.6C10.5884 10.6 11.094 10.7601 11.435 10.9874C11.7801 11.2175 11.9 11.4695 11.9 11.6667H13.1C13.1 10.9434 12.6603 10.362 12.1006 9.98892C11.5367 9.61301 10.7923 9.4 10 9.4V10.6ZM9.4 5.83333V6.66667H10.6V5.83333H9.4ZM9.40002 13.3333L9.4 14.1667L10.6 14.1667L10.6 13.3333L9.40002 13.3333ZM10 12.7333C9.19866 12.7333 8.57688 12.4401 8.28765 12.1068L7.38129 12.8932C7.95658 13.5562 8.95069 13.9333 10 13.9333L10 12.7333ZM11.9 11.6667C11.9 11.8639 11.7801 12.1159 11.435 12.3459C11.094 12.5732 10.5884 12.7333 10 12.7333V13.9333C10.7923 13.9333 11.5367 13.7203 12.1006 13.3444C12.6603 12.9713 13.1 12.3899 13.1 11.6667H11.9ZM9.4 6.66667L9.40002 13.3333L10.6 13.3333L10.6 6.66667L9.4 6.66667Z"
                                                        fill="#21201F" />
                                                </svg>
                                                Скупка компьютерной техники
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/skupka-noutbukov/" class="btn btn_icon-left text-sm text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.7124 7.89323C11.9295 8.14351 12.3085 8.17035 12.5588 7.95318C12.8091 7.73601 12.8359 7.35706 12.6187 7.10677L11.7124 7.89323ZM8.28765 12.1068C8.07048 11.8565 7.69153 11.8296 7.44125 12.0468C7.19096 12.264 7.16412 12.6429 7.38129 12.8932L8.28765 12.1068ZM10.6 5.83333C10.6 5.50196 10.3314 5.23333 10 5.23333C9.66863 5.23333 9.4 5.50196 9.4 5.83333H10.6ZM9.4 14.1667C9.39999 14.498 9.66862 14.7667 9.99999 14.7667C10.3314 14.7667 10.6 14.498 10.6 14.1667L9.4 14.1667ZM16.9 10C16.9 13.8108 13.8108 16.9 10 16.9V18.1C14.4735 18.1 18.1 14.4735 18.1 10H16.9ZM10 16.9C6.18923 16.9 3.1 13.8108 3.1 10H1.9C1.9 14.4735 5.52649 18.1 10 18.1V16.9ZM3.1 10C3.1 6.18923 6.18923 3.1 10 3.1V1.9C5.52649 1.9 1.9 5.52649 1.9 10H3.1ZM10 3.1C13.8108 3.1 16.9 6.18923 16.9 10H18.1C18.1 5.52649 14.4735 1.9 10 1.9V3.1ZM10 9.4C9.41158 9.4 8.906 9.23991 8.56505 9.01261C8.21991 8.78252 8.1 8.53054 8.1 8.33333H6.9C6.9 9.0566 7.33973 9.63795 7.89941 10.0111C8.46329 10.387 9.20771 10.6 10 10.6V9.4ZM8.1 8.33333C8.1 8.13613 8.21991 7.88415 8.56505 7.65405C8.906 7.42675 9.41158 7.26667 10 7.26667V6.06667C9.20771 6.06667 8.46329 6.27968 7.89941 6.65559C7.33973 7.02871 6.9 7.61007 6.9 8.33333H8.1ZM10 7.26667C10.8014 7.26667 11.4231 7.5599 11.7124 7.89323L12.6187 7.10677C12.0434 6.44376 11.0493 6.06667 10 6.06667V7.26667ZM10 10.6C10.5884 10.6 11.094 10.7601 11.435 10.9874C11.7801 11.2175 11.9 11.4695 11.9 11.6667H13.1C13.1 10.9434 12.6603 10.362 12.1006 9.98892C11.5367 9.61301 10.7923 9.4 10 9.4V10.6ZM9.4 5.83333V6.66667H10.6V5.83333H9.4ZM9.40002 13.3333L9.4 14.1667L10.6 14.1667L10.6 13.3333L9.40002 13.3333ZM10 12.7333C9.19866 12.7333 8.57688 12.4401 8.28765 12.1068L7.38129 12.8932C7.95658 13.5562 8.95069 13.9333 10 13.9333L10 12.7333ZM11.9 11.6667C11.9 11.8639 11.7801 12.1159 11.435 12.3459C11.094 12.5732 10.5884 12.7333 10 12.7333V13.9333C10.7923 13.9333 11.5367 13.7203 12.1006 13.3444C12.6603 12.9713 13.1 12.3899 13.1 11.6667H11.9ZM9.4 6.66667L9.40002 13.3333L10.6 13.3333L10.6 6.66667L9.4 6.66667Z"
                                                        fill="#21201F" />
                                                </svg>
                                                Скупка ноутбуков
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/skupka-iphone/" class="btn btn_icon-left text-sm text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.7124 7.89323C11.9295 8.14351 12.3085 8.17035 12.5588 7.95318C12.8091 7.73601 12.8359 7.35706 12.6187 7.10677L11.7124 7.89323ZM8.28765 12.1068C8.07048 11.8565 7.69153 11.8296 7.44125 12.0468C7.19096 12.264 7.16412 12.6429 7.38129 12.8932L8.28765 12.1068ZM10.6 5.83333C10.6 5.50196 10.3314 5.23333 10 5.23333C9.66863 5.23333 9.4 5.50196 9.4 5.83333H10.6ZM9.4 14.1667C9.39999 14.498 9.66862 14.7667 9.99999 14.7667C10.3314 14.7667 10.6 14.498 10.6 14.1667L9.4 14.1667ZM16.9 10C16.9 13.8108 13.8108 16.9 10 16.9V18.1C14.4735 18.1 18.1 14.4735 18.1 10H16.9ZM10 16.9C6.18923 16.9 3.1 13.8108 3.1 10H1.9C1.9 14.4735 5.52649 18.1 10 18.1V16.9ZM3.1 10C3.1 6.18923 6.18923 3.1 10 3.1V1.9C5.52649 1.9 1.9 5.52649 1.9 10H3.1ZM10 3.1C13.8108 3.1 16.9 6.18923 16.9 10H18.1C18.1 5.52649 14.4735 1.9 10 1.9V3.1ZM10 9.4C9.41158 9.4 8.906 9.23991 8.56505 9.01261C8.21991 8.78252 8.1 8.53054 8.1 8.33333H6.9C6.9 9.0566 7.33973 9.63795 7.89941 10.0111C8.46329 10.387 9.20771 10.6 10 10.6V9.4ZM8.1 8.33333C8.1 8.13613 8.21991 7.88415 8.56505 7.65405C8.906 7.42675 9.41158 7.26667 10 7.26667V6.06667C9.20771 6.06667 8.46329 6.27968 7.89941 6.65559C7.33973 7.02871 6.9 7.61007 6.9 8.33333H8.1ZM10 7.26667C10.8014 7.26667 11.4231 7.5599 11.7124 7.89323L12.6187 7.10677C12.0434 6.44376 11.0493 6.06667 10 6.06667V7.26667ZM10 10.6C10.5884 10.6 11.094 10.7601 11.435 10.9874C11.7801 11.2175 11.9 11.4695 11.9 11.6667H13.1C13.1 10.9434 12.6603 10.362 12.1006 9.98892C11.5367 9.61301 10.7923 9.4 10 9.4V10.6ZM9.4 5.83333V6.66667H10.6V5.83333H9.4ZM9.40002 13.3333L9.4 14.1667L10.6 14.1667L10.6 13.3333L9.40002 13.3333ZM10 12.7333C9.19866 12.7333 8.57688 12.4401 8.28765 12.1068L7.38129 12.8932C7.95658 13.5562 8.95069 13.9333 10 13.9333L10 12.7333ZM11.9 11.6667C11.9 11.8639 11.7801 12.1159 11.435 12.3459C11.094 12.5732 10.5884 12.7333 10 12.7333V13.9333C10.7923 13.9333 11.5367 13.7203 12.1006 13.3444C12.6603 12.9713 13.1 12.3899 13.1 11.6667H11.9ZM9.4 6.66667L9.40002 13.3333L10.6 13.3333L10.6 6.66667L9.4 6.66667Z"
                                                        fill="#21201F" />
                                                </svg>
                                                Скупка iPhone
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/skupka-b-u-televizorov/" class="btn btn_icon-left text-sm text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.7124 7.89323C11.9295 8.14351 12.3085 8.17035 12.5588 7.95318C12.8091 7.73601 12.8359 7.35706 12.6187 7.10677L11.7124 7.89323ZM8.28765 12.1068C8.07048 11.8565 7.69153 11.8296 7.44125 12.0468C7.19096 12.264 7.16412 12.6429 7.38129 12.8932L8.28765 12.1068ZM10.6 5.83333C10.6 5.50196 10.3314 5.23333 10 5.23333C9.66863 5.23333 9.4 5.50196 9.4 5.83333H10.6ZM9.4 14.1667C9.39999 14.498 9.66862 14.7667 9.99999 14.7667C10.3314 14.7667 10.6 14.498 10.6 14.1667L9.4 14.1667ZM16.9 10C16.9 13.8108 13.8108 16.9 10 16.9V18.1C14.4735 18.1 18.1 14.4735 18.1 10H16.9ZM10 16.9C6.18923 16.9 3.1 13.8108 3.1 10H1.9C1.9 14.4735 5.52649 18.1 10 18.1V16.9ZM3.1 10C3.1 6.18923 6.18923 3.1 10 3.1V1.9C5.52649 1.9 1.9 5.52649 1.9 10H3.1ZM10 3.1C13.8108 3.1 16.9 6.18923 16.9 10H18.1C18.1 5.52649 14.4735 1.9 10 1.9V3.1ZM10 9.4C9.41158 9.4 8.906 9.23991 8.56505 9.01261C8.21991 8.78252 8.1 8.53054 8.1 8.33333H6.9C6.9 9.0566 7.33973 9.63795 7.89941 10.0111C8.46329 10.387 9.20771 10.6 10 10.6V9.4ZM8.1 8.33333C8.1 8.13613 8.21991 7.88415 8.56505 7.65405C8.906 7.42675 9.41158 7.26667 10 7.26667V6.06667C9.20771 6.06667 8.46329 6.27968 7.89941 6.65559C7.33973 7.02871 6.9 7.61007 6.9 8.33333H8.1ZM10 7.26667C10.8014 7.26667 11.4231 7.5599 11.7124 7.89323L12.6187 7.10677C12.0434 6.44376 11.0493 6.06667 10 6.06667V7.26667ZM10 10.6C10.5884 10.6 11.094 10.7601 11.435 10.9874C11.7801 11.2175 11.9 11.4695 11.9 11.6667H13.1C13.1 10.9434 12.6603 10.362 12.1006 9.98892C11.5367 9.61301 10.7923 9.4 10 9.4V10.6ZM9.4 5.83333V6.66667H10.6V5.83333H9.4ZM9.40002 13.3333L9.4 14.1667L10.6 14.1667L10.6 13.3333L9.40002 13.3333ZM10 12.7333C9.19866 12.7333 8.57688 12.4401 8.28765 12.1068L7.38129 12.8932C7.95658 13.5562 8.95069 13.9333 10 13.9333L10 12.7333ZM11.9 11.6667C11.9 11.8639 11.7801 12.1159 11.435 12.3459C11.094 12.5732 10.5884 12.7333 10 12.7333V13.9333C10.7923 13.9333 11.5367 13.7203 12.1006 13.3444C12.6603 12.9713 13.1 12.3899 13.1 11.6667H11.9ZM9.4 6.66667L9.40002 13.3333L10.6 13.3333L10.6 6.66667L9.4 6.66667Z"
                                                        fill="#21201F" />
                                                </svg>
                                                Скупка б/у телевизоров
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/skupka-tehniki-apple/" class="btn btn_icon-left text-sm text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.7124 7.89323C11.9295 8.14351 12.3085 8.17035 12.5588 7.95318C12.8091 7.73601 12.8359 7.35706 12.6187 7.10677L11.7124 7.89323ZM8.28765 12.1068C8.07048 11.8565 7.69153 11.8296 7.44125 12.0468C7.19096 12.264 7.16412 12.6429 7.38129 12.8932L8.28765 12.1068ZM10.6 5.83333C10.6 5.50196 10.3314 5.23333 10 5.23333C9.66863 5.23333 9.4 5.50196 9.4 5.83333H10.6ZM9.4 14.1667C9.39999 14.498 9.66862 14.7667 9.99999 14.7667C10.3314 14.7667 10.6 14.498 10.6 14.1667L9.4 14.1667ZM16.9 10C16.9 13.8108 13.8108 16.9 10 16.9V18.1C14.4735 18.1 18.1 14.4735 18.1 10H16.9ZM10 16.9C6.18923 16.9 3.1 13.8108 3.1 10H1.9C1.9 14.4735 5.52649 18.1 10 18.1V16.9ZM3.1 10C3.1 6.18923 6.18923 3.1 10 3.1V1.9C5.52649 1.9 1.9 5.52649 1.9 10H3.1ZM10 3.1C13.8108 3.1 16.9 6.18923 16.9 10H18.1C18.1 5.52649 14.4735 1.9 10 1.9V3.1ZM10 9.4C9.41158 9.4 8.906 9.23991 8.56505 9.01261C8.21991 8.78252 8.1 8.53054 8.1 8.33333H6.9C6.9 9.0566 7.33973 9.63795 7.89941 10.0111C8.46329 10.387 9.20771 10.6 10 10.6V9.4ZM8.1 8.33333C8.1 8.13613 8.21991 7.88415 8.56505 7.65405C8.906 7.42675 9.41158 7.26667 10 7.26667V6.06667C9.20771 6.06667 8.46329 6.27968 7.89941 6.65559C7.33973 7.02871 6.9 7.61007 6.9 8.33333H8.1ZM10 7.26667C10.8014 7.26667 11.4231 7.5599 11.7124 7.89323L12.6187 7.10677C12.0434 6.44376 11.0493 6.06667 10 6.06667V7.26667ZM10 10.6C10.5884 10.6 11.094 10.7601 11.435 10.9874C11.7801 11.2175 11.9 11.4695 11.9 11.6667H13.1C13.1 10.9434 12.6603 10.362 12.1006 9.98892C11.5367 9.61301 10.7923 9.4 10 9.4V10.6ZM9.4 5.83333V6.66667H10.6V5.83333H9.4ZM9.40002 13.3333L9.4 14.1667L10.6 14.1667L10.6 13.3333L9.40002 13.3333ZM10 12.7333C9.19866 12.7333 8.57688 12.4401 8.28765 12.1068L7.38129 12.8932C7.95658 13.5562 8.95069 13.9333 10 13.9333L10 12.7333ZM11.9 11.6667C11.9 11.8639 11.7801 12.1159 11.435 12.3459C11.094 12.5732 10.5884 12.7333 10 12.7333V13.9333C10.7923 13.9333 11.5367 13.7203 12.1006 13.3444C12.6603 12.9713 13.1 12.3899 13.1 11.6667H11.9ZM9.4 6.66667L9.40002 13.3333L10.6 13.3333L10.6 6.66667L9.4 6.66667Z"
                                                        fill="#21201F" />
                                                </svg>
                                                Скупка техники Apple
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/skupka-monitorov-v-minske/" class="btn btn_icon-left text-sm text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.7124 7.89323C11.9295 8.14351 12.3085 8.17035 12.5588 7.95318C12.8091 7.73601 12.8359 7.35706 12.6187 7.10677L11.7124 7.89323ZM8.28765 12.1068C8.07048 11.8565 7.69153 11.8296 7.44125 12.0468C7.19096 12.264 7.16412 12.6429 7.38129 12.8932L8.28765 12.1068ZM10.6 5.83333C10.6 5.50196 10.3314 5.23333 10 5.23333C9.66863 5.23333 9.4 5.50196 9.4 5.83333H10.6ZM9.4 14.1667C9.39999 14.498 9.66862 14.7667 9.99999 14.7667C10.3314 14.7667 10.6 14.498 10.6 14.1667L9.4 14.1667ZM16.9 10C16.9 13.8108 13.8108 16.9 10 16.9V18.1C14.4735 18.1 18.1 14.4735 18.1 10H16.9ZM10 16.9C6.18923 16.9 3.1 13.8108 3.1 10H1.9C1.9 14.4735 5.52649 18.1 10 18.1V16.9ZM3.1 10C3.1 6.18923 6.18923 3.1 10 3.1V1.9C5.52649 1.9 1.9 5.52649 1.9 10H3.1ZM10 3.1C13.8108 3.1 16.9 6.18923 16.9 10H18.1C18.1 5.52649 14.4735 1.9 10 1.9V3.1ZM10 9.4C9.41158 9.4 8.906 9.23991 8.56505 9.01261C8.21991 8.78252 8.1 8.53054 8.1 8.33333H6.9C6.9 9.0566 7.33973 9.63795 7.89941 10.0111C8.46329 10.387 9.20771 10.6 10 10.6V9.4ZM8.1 8.33333C8.1 8.13613 8.21991 7.88415 8.56505 7.65405C8.906 7.42675 9.41158 7.26667 10 7.26667V6.06667C9.20771 6.06667 8.46329 6.27968 7.89941 6.65559C7.33973 7.02871 6.9 7.61007 6.9 8.33333H8.1ZM10 7.26667C10.8014 7.26667 11.4231 7.5599 11.7124 7.89323L12.6187 7.10677C12.0434 6.44376 11.0493 6.06667 10 6.06667V7.26667ZM10 10.6C10.5884 10.6 11.094 10.7601 11.435 10.9874C11.7801 11.2175 11.9 11.4695 11.9 11.6667H13.1C13.1 10.9434 12.6603 10.362 12.1006 9.98892C11.5367 9.61301 10.7923 9.4 10 9.4V10.6ZM9.4 5.83333V6.66667H10.6V5.83333H9.4ZM9.40002 13.3333L9.4 14.1667L10.6 14.1667L10.6 13.3333L9.40002 13.3333ZM10 12.7333C9.19866 12.7333 8.57688 12.4401 8.28765 12.1068L7.38129 12.8932C7.95658 13.5562 8.95069 13.9333 10 13.9333L10 12.7333ZM11.9 11.6667C11.9 11.8639 11.7801 12.1159 11.435 12.3459C11.094 12.5732 10.5884 12.7333 10 12.7333V13.9333C10.7923 13.9333 11.5367 13.7203 12.1006 13.3444C12.6603 12.9713 13.1 12.3899 13.1 11.6667H11.9ZM9.4 6.66667L9.40002 13.3333L10.6 13.3333L10.6 6.66667L9.4 6.66667Z"
                                                        fill="#21201F" />
                                                </svg>
                                                Скупка мониторов в Минске
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/skupka-planshetov-v-minske/" class="btn btn_icon-left text-sm text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.7124 7.89323C11.9295 8.14351 12.3085 8.17035 12.5588 7.95318C12.8091 7.73601 12.8359 7.35706 12.6187 7.10677L11.7124 7.89323ZM8.28765 12.1068C8.07048 11.8565 7.69153 11.8296 7.44125 12.0468C7.19096 12.264 7.16412 12.6429 7.38129 12.8932L8.28765 12.1068ZM10.6 5.83333C10.6 5.50196 10.3314 5.23333 10 5.23333C9.66863 5.23333 9.4 5.50196 9.4 5.83333H10.6ZM9.4 14.1667C9.39999 14.498 9.66862 14.7667 9.99999 14.7667C10.3314 14.7667 10.6 14.498 10.6 14.1667L9.4 14.1667ZM16.9 10C16.9 13.8108 13.8108 16.9 10 16.9V18.1C14.4735 18.1 18.1 14.4735 18.1 10H16.9ZM10 16.9C6.18923 16.9 3.1 13.8108 3.1 10H1.9C1.9 14.4735 5.52649 18.1 10 18.1V16.9ZM3.1 10C3.1 6.18923 6.18923 3.1 10 3.1V1.9C5.52649 1.9 1.9 5.52649 1.9 10H3.1ZM10 3.1C13.8108 3.1 16.9 6.18923 16.9 10H18.1C18.1 5.52649 14.4735 1.9 10 1.9V3.1ZM10 9.4C9.41158 9.4 8.906 9.23991 8.56505 9.01261C8.21991 8.78252 8.1 8.53054 8.1 8.33333H6.9C6.9 9.0566 7.33973 9.63795 7.89941 10.0111C8.46329 10.387 9.20771 10.6 10 10.6V9.4ZM8.1 8.33333C8.1 8.13613 8.21991 7.88415 8.56505 7.65405C8.906 7.42675 9.41158 7.26667 10 7.26667V6.06667C9.20771 6.06667 8.46329 6.27968 7.89941 6.65559C7.33973 7.02871 6.9 7.61007 6.9 8.33333H8.1ZM10 7.26667C10.8014 7.26667 11.4231 7.5599 11.7124 7.89323L12.6187 7.10677C12.0434 6.44376 11.0493 6.06667 10 6.06667V7.26667ZM10 10.6C10.5884 10.6 11.094 10.7601 11.435 10.9874C11.7801 11.2175 11.9 11.4695 11.9 11.6667H13.1C13.1 10.9434 12.6603 10.362 12.1006 9.98892C11.5367 9.61301 10.7923 9.4 10 9.4V10.6ZM9.4 5.83333V6.66667H10.6V5.83333H9.4ZM9.40002 13.3333L9.4 14.1667L10.6 14.1667L10.6 13.3333L9.40002 13.3333ZM10 12.7333C9.19866 12.7333 8.57688 12.4401 8.28765 12.1068L7.38129 12.8932C7.95658 13.5562 8.95069 13.9333 10 13.9333L10 12.7333ZM11.9 11.6667C11.9 11.8639 11.7801 12.1159 11.435 12.3459C11.094 12.5732 10.5884 12.7333 10 12.7333V13.9333C10.7923 13.9333 11.5367 13.7203 12.1006 13.3444C12.6603 12.9713 13.1 12.3899 13.1 11.6667H11.9ZM9.4 6.66667L9.40002 13.3333L10.6 13.3333L10.6 6.66667L9.4 6.66667Z"
                                                        fill="#21201F" />
                                                </svg>
                                                Скупка планшетов в Минске
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- <li class="menu__mobile-list-item">
                                <a href="#" class="btn btn_icon-left text-sm text">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10 6.66667V17.5M10 6.66667C10 6.66667 10 5.42269 10 5C10 4.07953 10.7462 3.33333 11.6667 3.33333C12.5871 3.33333 13.3333 4.07953 13.3333 5C13.3333 5.92047 12.5871 6.66667 11.6667 6.66667C11.1689 6.66667 10 6.66667 10 6.66667ZM10 6.66667C10 6.66667 10 5.05243 10 4.58333C10 3.43274 9.06726 2.5 7.91667 2.5C6.76607 2.5 5.83333 3.43274 5.83333 4.58333C5.83333 5.73393 6.76607 6.66667 7.91667 6.66667C8.59817 6.66667 10 6.66667 10 6.66667ZM4.16667 10H15.8333M4.16667 10C3.24619 10 2.5 9.25381 2.5 8.33333C2.5 7.41286 3.24619 6.66667 4.16667 6.66667H15.8333C16.7538 6.66667 17.5 7.41286 17.5 8.33333C17.5 9.25381 16.7538 10 15.8333 10M4.16667 10L4.16667 15.8333C4.16667 16.7538 4.91286 17.5 5.83333 17.5H14.1667C15.0871 17.5 15.8333 16.7538 15.8333 15.8333V10"
                                            stroke="#21201F" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    Подарочные сертификаты
                                </a>
                            </li> -->
                            </ul>
                        </div>
                        <div class="menu__cols">
                            <div class="menu__top js-menu-top">
                                <div class="menu__top-title-wrap">
                                    <button class="btn btn-icon js-mobile-menu-btn">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M9.70711 14.7071C9.31658 15.0976 8.68342 15.0976 8.29289 14.7071L4.29289 10.7071C3.90237 10.3166 3.90237 9.68342 4.29289 9.29289L8.29289 5.29289C8.68342 4.90237 9.31658 4.90237 9.70711 5.29289C10.0976 5.68342 10.0976 6.31658 9.70711 6.70711L7.41421 9L15 9C15.5523 9 16 9.44772 16 10C16 10.5523 15.5523 11 15 11H7.41421L9.70711 13.2929C10.0976 13.6834 10.0976 14.3166 9.70711 14.7071Z"
                                                fill="#21201F" />
                                        </svg>
                                    </button>
                                    <h2 class="menu__top-title text text-sm text--semibold">
                                        Каталог
                                    </h2>
                                </div>
                                <ul class="menu__list">
                                    <?php nout_menu_top_level() ?>
                                </ul>
                            </div>
                            <div class="menu__sub-wrap js-menu__sub-wrap-level-1">
                                <?php nout_menu_second_level() ?>
                            </div>
                            <div class="menu__sub-wrap js-menu__sub-wrap-level-2">
                                <?php nout_menu_third_level() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="popup js-popup" data-popup="1">
                <div class="popup__container">
                    <button class="popup__btn btn btn-icon js-popup-btn" data-popup="1" type="button">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M1.15128 1.15128C1.61991 0.682647 2.3797 0.682647 2.84833 1.15128L7.9998 6.30275L13.1513 1.15128C13.6199 0.682647 14.3797 0.682647 14.8483 1.15128C15.317 1.61991 15.317 2.3797 14.8483 2.84833L9.69686 7.9998L14.8483 13.1513C15.317 13.6199 15.317 14.3797 14.8483 14.8483C14.3797 15.317 13.6199 15.317 13.1513 14.8483L7.9998 9.69686L2.84833 14.8483C2.3797 15.317 1.61991 15.317 1.15128 14.8483C0.682647 14.3797 0.682647 13.6199 1.15128 13.1513L6.30275 7.9998L1.15128 2.84833C0.682647 2.3797 0.682647 1.61991 1.15128 1.15128Z"
                                fill="#474747" />
                        </svg>
                    </button>
                    <div class="popup__pic">
                        <img src="<?php nout_image_directory() ?>popup-pic.png" alt="Фото" class="popup__pic-img">
                    </div>
                    <div class="popup__content">
                        <div class="popup__form">
                            <h3 class="popup__form-title text-2xl">
                                Укажите контактные данные
                            </h3>
                            <p class="popup__form-subtitle text text-base">В ближайшее время мы с вами свяжемся</p>
                            <?php echo do_shortcode('[contact-form-7 id="74310" title="Контактная форма 1"]'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="popup js-popup-tg js-popup" data-popup="2">
                <div class="popup__container popup__container--tg">
                    <button class="popup__btn btn btn-icon js-popup-btn" data-popup="2" type="button">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M1.15128 1.15128C1.61991 0.682647 2.3797 0.682647 2.84833 1.15128L7.9998 6.30275L13.1513 1.15128C13.6199 0.682647 14.3797 0.682647 14.8483 1.15128C15.317 1.61991 15.317 2.3797 14.8483 2.84833L9.69686 7.9998L14.8483 13.1513C15.317 13.6199 15.317 14.3797 14.8483 14.8483C14.3797 15.317 13.6199 15.317 13.1513 14.8483L7.9998 9.69686L2.84833 14.8483C2.3797 15.317 1.61991 15.317 1.15128 14.8483C0.682647 14.3797 0.682647 13.6199 1.15128 13.1513L6.30275 7.9998L1.15128 2.84833C0.682647 2.3797 0.682647 1.61991 1.15128 1.15128Z"
                                fill="#474747" />
                        </svg>
                    </button>
                    <div class="popup__tg">
                        <div class="popup__tg__content">
                            <div class="popup__tg__content-title text-4xl">Получайте интересные предложения первыми</div>
                            <div class="popup__tg__content-desc text-base">Новые поступления, скидки и новости компании в нашем Телеграм-канале</div>
                            <a href="https://t.me/+LexdUf25ZUFhNzY6" target="_blank" class="popup__tg__content-btn  text-base">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M3.0955 9.03809C7.38958 7.06858 10.2482 5.7599 11.6837 5.12499C15.7692 3.32393 16.628 3.01296 17.1801 3C17.3028 3 17.5727 3.02591 17.7567 3.1814C17.904 3.31097 17.9408 3.47942 17.9653 3.60899C17.9898 3.73856 18.0144 4.01067 17.9898 4.21798C17.769 6.67986 16.812 12.6532 16.3213 15.4001C16.1127 16.5663 15.7078 16.955 15.3152 16.9938C14.4564 17.0716 13.8062 16.3978 12.9842 15.8277C11.6837 14.9336 10.9598 14.3765 9.69612 13.4954C8.23613 12.4847 9.18083 11.9276 10.0151 11.0205C10.2359 10.7873 14.0025 7.15928 14.0761 6.83535C14.0884 6.79648 14.0884 6.64099 14.0025 6.56325C13.9166 6.4855 13.7939 6.51142 13.6958 6.53733C13.5608 6.56325 11.4996 8.01446 7.48773 10.878C6.89883 11.3056 6.37127 11.5129 5.89279 11.5C5.36523 11.487 4.35919 11.189 3.59852 10.9298C2.67836 10.6189 1.94223 10.4504 2.00357 9.90622C2.04038 9.62116 2.40845 9.3361 3.0955 9.03809Z" fill="white" />
                                </svg>
                                Подписаться
                            </a>
                            <div class="popup__tg__content-agreement text-xs">Нажимая кнопку «Подписаться» вы соглашаетесь с условиями <a href="/politika-konfidenczialnosti/">политики конфиденциальности</a></div>
                        </div>
                        <div class="popup__tg__pic">
                            <img class="popup__tg__img" src="<?php nout_image_directory() ?>modal-tg/banner-tg.jpg" alt="Подписаться на наш телеграм" />
                        </div>
                    </div>
                </div>
            </div>
            <main class="main">