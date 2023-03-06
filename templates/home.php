<?php /* Template Name: Главная */ ?>
<?php get_header();?>

<div class="homepage">
                <section class="intro layout">
                    <div class="intro-slider">
                        <div class='swiper-container'>
                            <div class='swiper-wrapper'>
                                <?php 
                                    if( get_field('slajder_bannerov') ): ?>

                                        <?php while( has_sub_field('slajder_bannerov') ): ?>
                                            <div class='swiper-slide'>
                                                <div class="intro__pic" style="border-radius: 15px; overflow: hidden;">
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
                                                </div>
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
                                href="/product-category/smartfony-tv-i-jelektronika/smartfony-telefony/?proizvoditel=apple">
                                <img src="<?php nout_image_directory() ?>i-list-00.png" alt="Б/у техника Apple">
                                <span>Б/у техника Apple</span>
                            </a>
                        </li>
                        <li class="intro__list-item">
                            <a class="link text-lg link-primary"
                                href="/product-category/smartfony-tv-i-jelektronika/smartfony-telefony/">
                                <img src="<?php nout_image_directory() ?>i-list-01.png" alt="Б/у смартфоны">
                                <span>Б/у смартфоны</span>
                            </a>
                        </li>
                        <li class="intro__list-item">
                            <a class="link text-lg link-primary"
                                href="/product-category/kompjuternaja-tehnika/noutbuki/">
                                <img src="<?php nout_image_directory() ?>i-list-02.png" alt="Б/у ноутбуки">
                                <span>Б/у ноутбуки</span>
                            </a>
                        </li>
                    </ul>
                </section>
                <section class="prs layout">
                    <h2 class="prs__title text-4xl">
                        Смартфоны Apple
                    </h2>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
                    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
                    <ul class="prs__list hide-scroll">
                        <li class="prs__list-item">
                            <div class="product js-product-01">
                                <span class="product__tag product__tag--green">
                                    Новинка
                                </span>
                                <div class="product__swiper-wrap"
                                    onmouseleave="onLeaveProductPhoto(event, 'js-product-01')"
                                    onmousemove="changePhotos(event, 'js-product-01')">
                                    <div class="product__swiper js-product-swiper">
                                        <div class='swiper-wrapper'>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr1.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr2.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr3.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                                <div class="product__center">
                                    <a href="#" class="product__title">
                                        Конвектор Royal Clima Verona Econo REC–VE15 0M
                                    </a>
                                    <p class="product__status product__status--in-stock">
                                        В наличии
                                    </p>
                                    <div class="product__info-wrap">
                                        <a href="#" class="product__buy btn btn-primary">
                                            В корзину
                                        </a>
                                        <ul class="product__info">
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Диагональ экрана:
                                                </p>
                                                <p class="product__info-item-val">
                                                    15,6
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Модель процессора:
                                                </p>
                                                <p class="product__info-item-val">
                                                    Intel Pentium N3510
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Разрешение экрана:
                                                </p>
                                                <p class="product__info-item-val">
                                                    HD 1366х768
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Графический адаптер:
                                                </p>
                                                <p class="product__info-item-val">
                                                    встроенный
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Оперативная память, ГБ:
                                                </p>
                                                <p class="product__info-item-val">
                                                    ГБ: 4
                                                </p>
                                            </li>
                                        </ul>
                                        <div class="product__tags hide-scroll">
                                            <div class="product__tags-label product__tags-label--new">
                                                Как новый
                                            </div>
                                            <div class="product__tags-label product__tags-label--in-stock">
                                                В магазине
                                            </div>
                                            <div class="product__tags-label product__tags-label--credit">
                                                В рассрочку
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="product__box">
                                    <div class="product__actions">
                                        <p class="product__price">
                                            890
                                            <span>BYN</span>
                                        </p>
                                        <p class="product__price-old">
                                            1290
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
                            <script>
                                const productGalery01 = new Swiper(`.js-product-01 .js-product-swiper`, {
                                    effect: "fade",
                                    lazy: true,
                                    pagination: {
                                        el: `.js-product-01 .swiper-pagination`,
                                        clickable: true,
                                    },
                                })
                            </script>
                        </li>
                        <li class="prs__list-item">
                            <div class="product js-product-02">
                                <span class="product__tag product__tag--green">
                                    Новинка
                                </span>
                                <div class="product__swiper-wrap" onmousemove="changePhotos(event, 'js-product-02')">
                                    <div class="product__swiper js-product-swiper">
                                        <div class='swiper-wrapper'>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr1.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr2.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr3.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                                <div class="product__center">
                                    <a href="#" class="product__title">
                                        Конвектор Royal Clima Verona Econo REC–VE15 0M
                                    </a>
                                    <p class="product__status product__status--in-stock">
                                        В наличии
                                    </p>
                                    <div class="product__info-wrap">
                                        <a href="#" class="product__buy btn btn-primary">
                                            В корзину
                                        </a>
                                        <ul class="product__info">
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Диагональ экрана:
                                                </p>
                                                <p class="product__info-item-val">
                                                    15,6
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Модель процессора:
                                                </p>
                                                <p class="product__info-item-val">
                                                    Intel Pentium N3510
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Разрешение экрана:
                                                </p>
                                                <p class="product__info-item-val">
                                                    HD 1366х768
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Графический адаптер:
                                                </p>
                                                <p class="product__info-item-val">
                                                    встроенный
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Оперативная память, ГБ:
                                                </p>
                                                <p class="product__info-item-val">
                                                    ГБ: 4
                                                </p>
                                            </li>
                                        </ul>
                                        <div class="product__tags hide-scroll">
                                            <div class="product__tags-label product__tags-label--new">
                                                Как новый
                                            </div>
                                            <div class="product__tags-label product__tags-label--in-stock">
                                                В магазине
                                            </div>
                                            <div class="product__tags-label product__tags-label--credit">
                                                В рассрочку
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="product__box">
                                    <div class="product__actions">
                                        <p class="product__price">
                                            890
                                            <span>BYN</span>
                                        </p>
                                        <p class="product__price-old">
                                            1290
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
                            <script>
                                const productGalery02 = new Swiper(`.js-product-02 .js-product-swiper`, {
                                    effect: "fade",
                                    lazy: true,
                                    pagination: {
                                        el: `.js-product-02 .swiper-pagination`,
                                        clickable: true,
                                    },
                                })
                            </script>
                        </li>
                        <li class="prs__list-item">
                            <div class="product js-product-03">
                                <span class="product__tag product__tag--green">
                                    Новинка
                                </span>
                                <div class="product__swiper-wrap" onmousemove="changePhotos(event, 'js-product-03')">
                                    <div class="product__swiper js-product-swiper">
                                        <div class='swiper-wrapper'>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr1.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr2.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr3.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                                <div class="product__center">
                                    <a href="#" class="product__title">
                                        Конвектор Royal Clima Verona Econo REC–VE15 0M
                                    </a>
                                    <p class="product__status product__status--in-stock">
                                        В наличии
                                    </p>
                                    <div class="product__info-wrap">
                                        <a href="#" class="product__buy btn btn-primary">
                                            В корзину
                                        </a>
                                        <ul class="product__info">
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Диагональ экрана:
                                                </p>
                                                <p class="product__info-item-val">
                                                    15,6
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Модель процессора:
                                                </p>
                                                <p class="product__info-item-val">
                                                    Intel Pentium N3510
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Разрешение экрана:
                                                </p>
                                                <p class="product__info-item-val">
                                                    HD 1366х768
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Графический адаптер:
                                                </p>
                                                <p class="product__info-item-val">
                                                    встроенный
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Оперативная память, ГБ:
                                                </p>
                                                <p class="product__info-item-val">
                                                    ГБ: 4
                                                </p>
                                            </li>
                                        </ul>
                                        <div class="product__tags hide-scroll">
                                            <div class="product__tags-label product__tags-label--new">
                                                Как новый
                                            </div>
                                            <div class="product__tags-label product__tags-label--in-stock">
                                                В магазине
                                            </div>
                                            <div class="product__tags-label product__tags-label--credit">
                                                В рассрочку
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="product__box">
                                    <div class="product__actions">
                                        <p class="product__price">
                                            890
                                            <span>BYN</span>
                                        </p>
                                        <p class="product__price-old">
                                            1290
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
                            <script>
                                const productGalery03 = new Swiper(`.js-product-03 .js-product-swiper`, {
                                    effect: "fade",
                                    lazy: true,
                                    pagination: {
                                        el: `.js-product-03 .swiper-pagination`,
                                        clickable: true,
                                    },
                                })
                            </script>
                        </li>
                        <li class="prs__list-item">
                            <div class="product js-product-04">
                                <span class="product__tag product__tag--green">
                                    Новинка
                                </span>
                                <div class="product__swiper-wrap" onmousemove="changePhotos(event, 'js-product-04')">
                                    <div class="product__swiper js-product-swiper">
                                        <div class='swiper-wrapper'>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr1.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr2.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr3.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                                <div class="product__center">
                                    <a href="#" class="product__title">
                                        Конвектор Royal Clima Verona Econo REC–VE15 0M
                                    </a>
                                    <p class="product__status product__status--in-stock">
                                        В наличии
                                    </p>
                                    <div class="product__info-wrap">
                                        <a href="#" class="product__buy btn btn-primary">
                                            В корзину
                                        </a>
                                        <ul class="product__info">
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Диагональ экрана:
                                                </p>
                                                <p class="product__info-item-val">
                                                    15,6
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Модель процессора:
                                                </p>
                                                <p class="product__info-item-val">
                                                    Intel Pentium N3510
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Разрешение экрана:
                                                </p>
                                                <p class="product__info-item-val">
                                                    HD 1366х768
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Графический адаптер:
                                                </p>
                                                <p class="product__info-item-val">
                                                    встроенный
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Оперативная память, ГБ:
                                                </p>
                                                <p class="product__info-item-val">
                                                    ГБ: 4
                                                </p>
                                            </li>
                                        </ul>
                                        <div class="product__tags hide-scroll">
                                            <div class="product__tags-label product__tags-label--new">
                                                Как новый
                                            </div>
                                            <div class="product__tags-label product__tags-label--in-stock">
                                                В магазине
                                            </div>
                                            <div class="product__tags-label product__tags-label--credit">
                                                В рассрочку
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="product__box">
                                    <div class="product__actions">
                                        <p class="product__price">
                                            890
                                            <span>BYN</span>
                                        </p>
                                        <p class="product__price-old">
                                            1290
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
                            <script>
                                const productGalery04 = new Swiper(`.js-product-04 .js-product-swiper`, {
                                    effect: "fade",
                                    lazy: true,
                                    pagination: {
                                        el: `.js-product-04 .swiper-pagination`,
                                        clickable: true,
                                    },
                                })
                            </script>
                        </li>
                        <li class="prs__list-item">
                            <div class="product js-product-05">
                                <span class="product__tag product__tag--green">
                                    Новинка
                                </span>
                                <div class="product__swiper-wrap" onmousemove="changePhotos(event, 'js-product-05')">
                                    <div class="product__swiper js-product-swiper">
                                        <div class='swiper-wrapper'>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr1.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr2.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class='swiper-slide'>
                                                <a href="#" class="product__pic">
                                                    <img src="<?php nout_image_directory() ?>pr3.png" alt="Фото" class="product__pic-img"
                                                        loading="lazy">
                                                    <div class="swiper-lazy-preloader ">
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                                <div class="product__center">
                                    <a href="#" class="product__title">
                                        Конвектор Royal Clima Verona Econo REC–VE15 0M
                                    </a>
                                    <p class="product__status product__status--in-stock">
                                        В наличии
                                    </p>
                                    <div class="product__info-wrap">
                                        <a href="#" class="product__buy btn btn-primary">
                                            В корзину
                                        </a>
                                        <ul class="product__info">
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Диагональ экрана:
                                                </p>
                                                <p class="product__info-item-val">
                                                    15,6
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Модель процессора:
                                                </p>
                                                <p class="product__info-item-val">
                                                    Intel Pentium N3510
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Разрешение экрана:
                                                </p>
                                                <p class="product__info-item-val">
                                                    HD 1366х768
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Графический адаптер:
                                                </p>
                                                <p class="product__info-item-val">
                                                    встроенный
                                                </p>
                                            </li>
                                            <li class="product__info-item">
                                                <p class="product__info-item-name">
                                                    Оперативная память, ГБ:
                                                </p>
                                                <p class="product__info-item-val">
                                                    ГБ: 4
                                                </p>
                                            </li>
                                        </ul>
                                        <div class="product__tags hide-scroll">
                                            <div class="product__tags-label product__tags-label--new">
                                                Как новый
                                            </div>
                                            <div class="product__tags-label product__tags-label--in-stock">
                                                В магазине
                                            </div>
                                            <div class="product__tags-label product__tags-label--credit">
                                                В рассрочку
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="product__box">
                                    <div class="product__actions">
                                        <p class="product__price">
                                            890
                                            <span>BYN</span>
                                        </p>
                                        <p class="product__price-old">
                                            1290
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
                            <script>
                                const productGalery05 = new Swiper(`.js-product-05 .js-product-swiper`, {
                                    effect: "fade",
                                    lazy: true,
                                    pagination: {
                                        el: `.js-product-05 .swiper-pagination`,
                                        clickable: true,
                                    },
                                })
                            </script>
                        </li>
                    </ul>
                </section>
                <div class="categories layout">
                    <h2 class="categories__title text-4xl">Категории товаров</h2>
                    <ul class="categories__list">
                        <li class="categories__list-item">
                            <a href="" class="categories__card">
                                <div class="categories__card-pic">
                                    <img src="images/home_categories-1.svg" alt="Фото" class="categories__card-pic-img">
                                </div>
                                <div class="categories__content">
                                    <span class="categories__content-text text-base">
                                        Ноутбуки
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="categories__list-item">
                            <a href="" class="categories__card">
                                <div class="categories__card-pic">
                                    <img src="images/home_categories-2.svg" alt="Фото" class="categories__card-pic-img">
                                </div>
                                <div class="categories__content">
                                    <span class="categories__content-text text-base">
                                        Бытовая техника
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="categories__list-item">
                            <a href="" class="categories__card">
                                <div class="categories__card-pic">
                                    <img src="images/home_categories-3.svg" alt="Фото" class="categories__card-pic-img">
                                </div>
                                <div class="categories__content">
                                    <span class="categories__content-text text-base">
                                        Смартфоны
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="categories__list-item">
                            <a href="" class="categories__card">
                                <div class="categories__card-pic">
                                    <img src="images/home_categories-4.svg" alt="Фото" class="categories__card-pic-img">
                                </div>
                                <div class="categories__content">
                                    <span class="categories__content-text text-base">
                                        Аксессуары
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="categories__list-item">
                            <a href="" class="categories__card">
                                <div class="categories__card-pic">
                                    <img src="images/home_categories-5.svg" alt="Фото" class="categories__card-pic-img">
                                </div>
                                <div class="categories__content">
                                    <span class="categories__content-text text-base">
                                        Наушники
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="categories__list-item">
                            <a href="" class="categories__card">
                                <div class="categories__card-pic">
                                    <img src="images/home_categories-6.svg" alt="Фото" class="categories__card-pic-img">
                                </div>
                                <div class="categories__content">
                                    <span class="categories__content-text text-base">
                                        Комплектующие
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="categories__list-item">
                            <a href="" class="categories__card">
                                <div class="categories__card-pic">
                                    <img src="images/home_categories-7.svg" alt="Фото" class="categories__card-pic-img">
                                </div>
                                <div class="categories__content">
                                    <span class="categories__content-text text-base">
                                        Планшеты
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="categories__list-item">
                            <a href="" class="categories__card">
                                <div class="categories__card-pic">
                                    <img src="images/home_categories-8.svg" alt="Фото" class="categories__card-pic-img">
                                </div>
                                <div class="categories__content">
                                    <span class="categories__content-text text-base">
                                        Запчасти
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="categories__list-item">
                            <a href="" class="categories__card">
                                <div class="categories__card-pic">
                                    <img src="images/home_categories-9.svg" alt="Фото" class="categories__card-pic-img">
                                </div>
                                <div class="categories__content">
                                    <span class="categories__content-text text-base">
                                        Телевизоры
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="categories__list-item">
                            <a href="" class="categories__card">
                                <div class="categories__card-pic">
                                    <img src="images/home_categories-10.svg" alt="Фото"
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
                </div>
                <div class="advantage layout">
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
                                        src="images/home_advantage-1.svg" alt="Фото">
                                </div>
                            </div>
                        </li>
                        <li class="advantage__list-item">
                            <div class="card advantage__list-card advantage__list-card-upgrade">
                                <div class="advantage__list-card-upgrade-gradient">
                                </div>
                                <div class="advantage__list-card-upgrade-pic">
                                    <img class="advantage__list-card-upgrade-pic-img" src="images/home_advantage-2.svg"
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
                                    <img class="advantage__list-card-photos-pic-img" src="images/home_advantage-3.svg"
                                        alt="Фото">
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

<?php get_footer();