<?php /* Template Name: Главная */ ?>
<?php get_header(); ?>

<?php
$page_title = get_field('title');
?>

<div class="homepage">
    <section class="intro layout">
        <div class="intro-slider">
            <div class='swiper-container'>
                <div class='swiper-wrapper'>
                    <?php
                    if (get_field('slajder_bannerov')): ?>

                        <?php while (has_sub_field('slajder_bannerov')): ?>
                            <div class='swiper-slide'>
                                <a href="<?php the_sub_field('intro-ssylka'); ?>" class="intro__pic" style="border-radius: 15px; overflow: hidden;">
                                    <img src="<?php the_sub_field('izobrazhenie_bannera_xl'); ?>" alt="Фото"
                                        class="intro__pic-img intro__pic-img-xl">
                                    <img src="<?php the_sub_field('izobrazhenie_bannera_l'); ?>" alt="Фото"
                                        class="intro__pic-img intro__pic-img-l">
                                    <img src="<?php the_sub_field('izobrazhenie_bannera_m'); ?>" alt="Фото"
                                        class="intro__pic-img intro__pic-img-m">
                                    <img src="<?php the_sub_field('izobrazhenie_bannera_s'); ?>" alt="Фото"
                                        class="intro__pic-img intro__pic-img-s">
                                    <img src="<?php the_sub_field('izobrazhenie_bannera_xs'); ?>" alt="Фото"
                                        class="intro__pic-img intro__pic-img-xs">
                                </a>
                            </div>

                        <?php endwhile; ?>

                    <?php endif;
                    ?>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <button type="button" class="swiper-button-prev">
                <svg width="87" height="87" viewBox="0 0 87 87" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <g filter="url(#filter0_d_2586_9853)">
                        <rect width="55" height="55" rx="27.5" transform="matrix(-1 0 0 1 71 4)"
                            fill="#EFEEED" />
                    </g>
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M45.9032 26.8598C46.3031 27.262 46.3031 27.9116 45.9032 28.3139L42.2526 32.0161L45.9032 35.6667C46.3031 36.0689 46.3031 36.7185 45.9032 37.1207C45.7096 37.316 45.446 37.4258 45.1711 37.4258C44.8961 37.4258 44.6325 37.316 44.4389 37.1207L40.0664 32.7482C39.8711 32.5546 39.7613 32.291 39.7613 32.0161C39.7613 31.7411 39.8711 31.4775 40.0664 31.2839L44.4389 26.8598C44.6325 26.6646 44.8961 26.5548 45.1711 26.5548C45.446 26.5548 45.7096 26.6646 45.9032 26.8598Z"
                        fill="#212B36" />
                    <defs>
                        <filter id="filter0_d_2586_9853" x="0" y="0" width="87" height="87"
                            filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix"
                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="12" />
                            <feGaussianBlur stdDeviation="8" />
                            <feColorMatrix type="matrix"
                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.12 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix"
                                result="effect1_dropShadow_2586_9853" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_2586_9853"
                                result="shape" />
                        </filter>
                    </defs>
                </svg>
            </button>
            <button type="button" class="swiper-button-next">
                <svg width="87" height="87" viewBox="0 0 87 87" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <g filter="url(#filter0_d_2586_9861)">
                        <rect x="16" y="4" width="55" height="55" rx="27.5" fill="#EFEEED" />
                    </g>
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M41.0968 26.8598C40.6969 27.262 40.6969 27.9116 41.0968 28.3139L44.7474 32.0161L41.0968 35.6667C40.6969 36.0689 40.6969 36.7185 41.0968 37.1207C41.2904 37.316 41.554 37.4258 41.8289 37.4258C42.1039 37.4258 42.3675 37.316 42.5611 37.1207L46.9336 32.7482C47.1289 32.5546 47.2387 32.291 47.2387 32.0161C47.2387 31.7411 47.1289 31.4775 46.9336 31.2839L42.5611 26.8598C42.3675 26.6646 42.1039 26.5548 41.8289 26.5548C41.554 26.5548 41.2904 26.6646 41.0968 26.8598V26.8598Z"
                        fill="#212B36" />
                    <defs>
                        <filter id="filter0_d_2586_9861" x="0" y="0" width="87" height="87"
                            filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix"
                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="12" />
                            <feGaussianBlur stdDeviation="8" />
                            <feColorMatrix type="matrix"
                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.12 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix"
                                result="effect1_dropShadow_2586_9861" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_2586_9861"
                                result="shape" />
                        </filter>
                    </defs>
                </svg>
            </button>
        </div>
        <ul class="intro__list">
            <li class="intro__list-item">
                <a class="link text-lg link-primary"
                    href="/product-category/novaya-produkcziya-apple/">
                    <img src="<?php nout_image_directory() ?>i-list-00.png" alt="Б/у техника Apple">
                    <span>New техника Apple</span>
                </a>
            </li>
            <li class="intro__list-item">
                <a class="link text-lg link-primary"
                    href="/product-category/smartfony-tv-i-elektronika/planshety-elektronnye-knigi/planshety/">
                    <img src="/wp-content/uploads/2025/05/ipad-air-13-2024-starlight.webp" alt="Планшеты">
                    <span>Планшеты</span>
                </a>
            </li>
            <li class="intro__list-item">
                <a class="link text-lg link-primary"
                    href="/product-category/kompyuternaya-tehnika/noutbuki/">
                    <img src="<?php nout_image_directory() ?>i-list-02.png" alt="Б/у ноутбуки">
                    <span>Б/у ноутбуки</span>
                </a>
            </li>
            <li class="intro__list-item">
                <a class="link text-lg link-primary"
                    href="/product-category/novye-smartofny-android/">
                    <img src="/wp-content/uploads/2025/05/samsung-geleksi-25.png" alt="Новые андроид">
                    <span>NEW Android</span>
                </a>
            </li>
            <li class="intro__list-item">
                <a class="link text-lg link-primary"
                    href="/product-category/smartfony-telefony/apple/">
                    <img src="/wp-content/uploads/2025/05/16e.png" alt="б/у iphone">
                    <span>Б/у iPhone</span>
                </a>
            </li>
             <li class="intro__list-item">
                <a class="link text-lg link-primary"
                    href="/product-category/smartfony-telefony/b-u-android/">
                    <img src="/wp-content/uploads/2025/05/ksyaomi.png" alt="Новые iphone">
                    <span>Б/у Android</span>
                </a>
            </li>
        </ul>

        <h1 class="homepage__title text-5xl"><?= $page_title; ?></h1>
    </section>
    <section class="prs layout">
        <a href="<?php echo get_category_link(2334); ?>" class="prs__title text-4xl">
            Смартфоны Apple
        </a>
        <ul class="prs__list prs__list-2334 hide-scroll">
            <?php
            nout_show_products_by_id(2334);
            ?>
        </ul>
    </section>
    <section class="categories layout">
        <h2 class="categories__title text-4xl">Категории товаров</h2>
        <ul class="categories__list">
            <li class="categories__list-item">
                <a href="/product-category/kompyuternaya-tehnika/noutbuki/" class="categories__card">
                    <div class="categories__card-pic">
                        <img src="<?php nout_image_directory(); ?>hc1.png" alt="Фото" class="categories__card-pic-img">
                    </div>
                    <div class="categories__content">
                        <span class="categories__content-text text-base">
                            Ноутбуки
                        </span>
                    </div>
                </a>
            </li>
            <li class="categories__list-item">
                <a href="/product-category/bytovaya-tehnika/" class="categories__card">
                    <div class="categories__card-pic">
                        <img src="<?php nout_image_directory(); ?>hc6.png" alt="Фото" class="categories__card-pic-img">
                    </div>
                    <div class="categories__content">
                        <span class="categories__content-text text-base">
                            Бытовая техника
                        </span>
                    </div>
                </a>
            </li>
            <li class="categories__list-item">
                <a href="/product-category/smartfony-tv-i-elektronika/smartfony-telefony/smartfony/" class="categories__card">
                    <div class="categories__card-pic">
                        <img src="<?php nout_image_directory(); ?>hc2.png" alt="Фото" class="categories__card-pic-img">
                    </div>
                    <div class="categories__content">
                        <span class="categories__content-text text-base">
                            Смартфоны
                        </span>
                    </div>
                </a>
            </li>
            <li class="categories__list-item">
                <a href="/product-category/periferiya-i-aksessuary/" class="categories__card">
                    <div class="categories__card-pic">
                        <img src="<?php nout_image_directory(); ?>hc7.png" alt="Фото" class="categories__card-pic-img">
                    </div>
                    <div class="categories__content">
                        <span class="categories__content-text text-base">
                            Аксессуары
                        </span>
                    </div>
                </a>
            </li>
            <li class="categories__list-item">
                <a href="/product-category/smartfony-tv-i-elektronika/naushniki-i-garnitury/" class="categories__card">
                    <div class="categories__card-pic">
                        <img src="<?php nout_image_directory(); ?>hc3.png" alt="Фото" class="categories__card-pic-img">
                    </div>
                    <div class="categories__content">
                        <span class="categories__content-text text-base">
                            Наушники
                        </span>
                    </div>
                </a>
            </li>
            <li class="categories__list-item">
                <a href="/product-category/komplektuyushhie-dlya-noutbukov/" class="categories__card">
                    <div class="categories__card-pic">
                        <img src="<?php nout_image_directory(); ?>hc8.png" alt="Фото" class="categories__card-pic-img">
                    </div>
                    <div class="categories__content">
                        <span class="categories__content-text text-base">
                            Комплектующие
                        </span>
                    </div>
                </a>
            </li>
            <li class="categories__list-item">
                <a href="/product-category/smartfony-tv-i-elektronika/planshety-elektronnye-knigi/planshety/" class="categories__card">
                    <div class="categories__card-pic">
                        <img src="<?php nout_image_directory(); ?>hc4.png" alt="Фото" class="categories__card-pic-img">
                    </div>
                    <div class="categories__content">
                        <span class="categories__content-text text-base">
                            Планшеты
                        </span>
                    </div>
                </a>
            </li>
            <li class="categories__list-item">
                <a href="/product-category/zapchasti-dlya-noutbukov/" class="categories__card">
                    <div class="categories__card-pic">
                        <img src="<?php nout_image_directory(); ?>hc9.png" alt="Фото" class="categories__card-pic-img">
                    </div>
                    <div class="categories__content">
                        <span class="categories__content-text text-base">
                            Запчасти
                        </span>
                    </div>
                </a>
            </li>
            <li class="categories__list-item">
                <a href="/product-category/smartfony-tv-i-elektronika/televizory-proigryvateli/" class="categories__card">
                    <div class="categories__card-pic">
                        <img src="<?php nout_image_directory(); ?>hc5.png" alt="Фото" class="categories__card-pic-img">
                    </div>
                    <div class="categories__content">
                        <span class="categories__content-text text-base">
                            Телевизоры
                        </span>
                    </div>
                </a>
            </li>
            <li class="categories__list-item">
                <a href="/product-category/komplektuyushhie-dlya-noutbukov/akkumulyatory-dlya-noutbukov/" class="categories__card">
                    <div class="categories__card-pic">
                        <img src="<?php nout_image_directory(); ?>hc10.png" alt="Фото"
                            class="categories__card-pic-img">
                    </div>
                    <div class="categories__content">
                        <span class="categories__content-text text-base">
                            Аккумуляторы
                        </span>
                    </div>
                </a>
            </li>
        </ul>
    </section>
    <?php nout_show_products_new(); ?>
    <section class="prs layout">
        <h2 class="prs__title text-4xl">
            Рекомендуемые товары
        </h2>
        <ul class="prs__list prs__list-2334 hide-scroll">
            <?php
            nout_show_products_related();
            ?>
        </ul>
    </section>
    <section class="advantage layout">
        <h2 class="advantage__title text-4xl">Наши преимущества</h2>
        <ul class="advantage__list">
            <li class="advantage__list-item">
                <div class="card advantage__list-card advantage__list-card-guarantee">
                    <h4 class="text-2xl advantage__list-card-guarantee-subtitle">Гарантия на товары</h4>
                    <p class="text-base advantage__list-card-guarantee-text">
                        Предоставляем сервисное и гарантийное обслуживание после покупки
                    </p>
                    <div class="advantage__list-card-guarantee-gradient">
                    </div>
                    <div class="advantage__list-card-guarantee-pic">
                        <img class="advantage__list-card-guarantee-pic-img"
                            src="<?php nout_image_directory(); ?>home_advantage-1.svg" alt="Фото">
                    </div>
                </div>
            </li>
            <li class="advantage__list-item">
                <div class="card advantage__list-card advantage__list-card-upgrade">
                    <div class="advantage__list-card-upgrade-gradient">
                    </div>
                    <div class="advantage__list-card-upgrade-pic">
                        <img class="advantage__list-card-upgrade-pic-img" src="<?php nout_image_directory(); ?>home_advantage-2.svg"
                            alt="Фото">
                    </div>
                    <h4 class="text-2xl advantage__list-card-upgrade-subtitle">Апгрейд техники</h4>
                    <p class="text-base advantage__list-card-upgrade-text">
                        Грамотно «разгоним» устаревший ноутбук под ваши нужды
                    </p>
                </div>
            </li>
            <li class="advantage__list-item">
                <div class="card advantage__list-card advantage__list-card-photos">
                    <h4 class="text-2xl advantage__list-card-photos-subtitle">Реальные фото товаров</h4>
                    <p class="text-base advantage__list-card-photos-text">
                        Все фотографии настоящие и они отражают реальное состояние товара
                    </p>
                    <div class="advantage__list-card-photos-gradient">
                    </div>
                    <div class="advantage__list-card-photos-pic">
                        <img class="advantage__list-card-photos-pic-img" src="<?php nout_image_directory(); ?>home-new-pic.png"
                            alt="Фото">
                    </div>
                </div>
            </li>
        </ul>
    </section>
    <section class="prs layout">
        <a href="<?php echo get_category_link(2316); ?>" class="prs__title text-4xl">
            Ноутбуки Dell
        </a>
        <ul class="prs__list hide-scroll">
            <?php
            nout_show_products_by_id(2316);
            ?>
        </ul>
    </section>
    <section class="layout hide-s">
        <h2 class="hide-s text-5xl" style="display: none;">
            Подписывайтесь на наш телеграм
        </h2>
        <a href="https://t.me/+LexdUf25ZUFhNzY6" class="hide-s__banner banner">
            <img class="banner-xl" src="<?php nout_image_directory(); ?>tgb/sm/xl1.jpg" alt="Баннер">
            <img class="banner-l" src="<?php nout_image_directory(); ?>tgb/sm/l1.jpg" alt="Баннер">
            <img class="banner-m" src="<?php nout_image_directory(); ?>tgb/sm/m1.jpg" alt="Баннер">
            <img class="banner-s" src="<?php nout_image_directory(); ?>tgb/sm/s1.jpg" alt="Баннер">
            <img class="banner-xs" src="<?php nout_image_directory(); ?>tgb/sm/xs1.jpg" alt="Баннер">
        </a>
    </section>
    <section class="prs layout">
        <a href="<?php echo get_category_link(2444); ?>" class="prs__title text-4xl">
            Смартфоны Samsung
        </a>
        <ul class="prs__list prs__list-2334 hide-scroll">
            <?php
            nout_show_products_by_id(2444);
            ?>
        </ul>
    </section>
    <section class="prs layout">
        <a href="<?php echo get_category_link(2318); ?>" class="prs__title text-4xl">
            Ноутбуки HP
        </a>
        <ul class="prs__list prs__list-2334 hide-scroll">
            <?php
            nout_show_products_by_id(2318);
            ?>
        </ul>
    </section>
    <section class="layout hide-s">
        <h2 class="hide-s text-5xl" style="display: none;">
            Подписывайтесь на наш телеграм
        </h2>
        <a href="https://t.me/+LexdUf25ZUFhNzY6" class="hide-s__banner banner">
            <img class="banner-xl" src="<?php nout_image_directory(); ?>tgb2/sm/xl.jpg" alt="Баннер">
            <img class="banner-l" src="<?php nout_image_directory(); ?>tgb2/sm/l.jpg" alt="Баннер">
            <img class="banner-m" src="<?php nout_image_directory(); ?>tgb2/sm/m.jpg" alt="Баннер">
            <img class="banner-s" src="<?php nout_image_directory(); ?>tgb2/sm/s.jpg" alt="Баннер">
            <img class="banner-xs" src="<?php nout_image_directory(); ?>tgb2/sm/xs.jpg" alt="Баннер">
        </a>
    </section>
</div>

<?php get_footer();
