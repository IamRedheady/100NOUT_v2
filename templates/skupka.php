<?php /* Template Name: Скупка */ ?>
<?php get_header(); ?>
<div class="skupka">
    <section class="skupka-intro-wrap">
        <div class="layout">
            <div class="skupka-intro">
                <h1 class="skupka-intro__title">
                    Быстрая скупка Техники в Минске
                </h1>
                <p class="skupka-intro__subtitle">
                    Узнайте стоимость прямо на сайте, по телефону или по нашему адресу за 10 минут. Наличный
                    и безналичный расчёт. Скупка у физических и юридических лиц.
                    <br>
                    <br>
                    Заплатим больше чем в ломбарде
                </p>
                <div class="skupka-intro__btn">
                    <a href="#online">Продать</a>
                </div>
                <div class="skupka-intro__img">
                    <img src="<?php nout_image_directory(); ?>slide1.jpg" alt="Фото" />
                </div>
            </div>
        </div>
    </section>

    <?php
    $args = [
        'post_type' => 'page',
        'fields' => 'ids',
        'nopaging' => true,
        'meta_key' => '_wp_page_template',
        'meta_value' => 'templates/skupka-dev.php'
    ];
    $pages = get_posts($args);
    ?>
    <?php if (count($pages)): ?>
        <section class="condition" id="types">
            <div class="layout">
                <h2 class="condition__title">Какую технику мы выкупаем</h2>
                <div class="condition__cards" style="flex-wrap: wrap; gap: 40px;">
                    <?php foreach ($pages as $page_item):
                        $card_fields = get_field('card', $page_item);

                        if ($card_fields['hide']) {
                            continue;
                        }
                    ?>
                        <div class="condition__cards-card" style="position: relative;">
                            <a href="<?= the_permalink($page_item) ?>"
                                style="display: block; position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></a>
                            <img class="condition__cards-card-pic" src="<?= $card_fields['image']['url']; ?>"
                                alt="<?= $card_fields['alt']; ?>" />
                            <h2 class="condition__cards-card-title"><?= $card_fields['title']; ?></h2>
                            <div class="condition__cards-card-subtitle">
                                <?= $card_fields['description']; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="advantages">
        <div class="layout">
            <div class="advantages__wrapp">
                <div class="advantages__item">
                    <div class="advantages__img">
                        <img src="<?php nout_image_directory(); ?>Phone.png" alt="" />
                    </div>

                    <h2 class="advantages__name">Быстрая связь</h2>

                    <p class="advantages__discription">+375 33 375 74 00</p>
                </div>

                <div class="advantages__item">
                    <div class="advantages__img">
                        <img src="<?php nout_image_directory(); ?>work.png" alt="" />
                    </div>

                    <h2 class="advantages__name">24/7</h2>

                    <p class="advantages__discription">
                        Работаем без выходных <br />
                        и круглосуточно
                    </p>
                </div>

                <div class="advantages__item">
                    <div class="advantages__img">
                        <img src="<?php nout_image_directory(); ?>Money.png" alt="" />
                    </div>

                    <h2 class="advantages__name">Деньги сразу</h2>

                    <p class="advantages__discription">
                        Наличный и безналичный расчет на месте
                    </p>
                </div>

                <div class="advantages__item">
                    <div class="advantages__img">
                        <img src="<?php nout_image_directory(); ?>human.png" alt="" />
                    </div>

                    <h2 class="advantages__name">Работаем с юр. лицами</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="location">
        <div class="location__wrapper">
            <div class="location__map">
                <script type="text/javascript" charset="utf-8" async
                    src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3Ae09bd7857a1ac0d513bdeef04db2bcfeb42eea9c586ef25f8657781f1cced2fa&amp;width=100%&amp;height=100%&amp;lang=ru_RU&amp;scroll=true"></script>
            </div>
            <div class="location__contacts">
                <div class="location__contacts-info">
                    <h3 class="location__contacts-info-title">мы находимся:</h3>
                    <p class="location__contacts-info-subtitle">
                        г. Минск, <br> пр-т Независимости д. 94
                    </p>
                </div>
                <div class="location__contacts-info">
                    <h3 class="location__contacts-info-title">Звоните:</h3>
                    <p class="location__contacts-info-subtitle">
                        <a class="link link-secondary text" href="tel:+375 33 375 74 00">
                            +375 33 375 74 00
                        </a>
                    </p>
                    <div class="online__social">
                        <div class="online__telegram">
                            <a href="https://t.me/skypim_bot">
                                <svg width="50" height="50" viewBox="0 0 50 50" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_202_48)">
                                        <path
                                            d="M24.9999 50C38.807 50 49.9999 38.8071 49.9999 25C49.9999 11.1929 38.807 0 24.9999 0C11.1928 0 -6.10352e-05 11.1929 -6.10352e-05 25C-6.10352e-05 38.8071 11.1928 50 24.9999 50Z"
                                            fill="#4F9FDA" />
                                        <path
                                            d="M10.4046 24.7833C10.4046 24.7833 22.9046 19.6533 27.2398 17.8469C28.9017 17.1244 34.5375 14.8122 34.5375 14.8122C34.5375 14.8122 37.1387 13.8007 36.9219 16.2573C36.8496 17.2689 36.2716 20.8093 35.6936 24.6388C34.8265 30.058 33.8872 35.9827 33.8872 35.9827C33.8872 35.9827 33.7427 37.6447 32.5144 37.9337C31.2861 38.2227 29.2629 36.9222 28.9017 36.6331C28.6126 36.4164 23.4826 33.1649 21.604 31.5753C21.0982 31.1418 20.5202 30.2748 21.6762 29.2632C24.2774 26.8787 27.3843 23.9164 29.2629 22.0377C30.13 21.1706 30.997 19.1475 27.3843 21.6041C22.2543 25.1446 17.1965 28.4684 17.1965 28.4684C17.1965 28.4684 16.0404 29.1908 13.8728 28.5405C11.7051 27.8903 9.17622 27.0232 9.17622 27.0232C9.17622 27.0232 7.44222 25.9395 10.4046 24.7833Z"
                                            fill="white" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_202_48">
                                            <rect width="50" height="50" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </a>
                        </div>
                        <div class="online__sap">
                            <a href="viber://pa?chatURI=skypimby">
                                <svg width="50" height="50" viewBox="0 0 50 50" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M25 50C38.8071 50 50 38.8071 50 25C50 11.1929 38.8071 0 25 0C11.1929 0 0 11.1929 0 25C0 38.8071 11.1929 50 25 50Z"
                                        fill="#785BE4" />
                                    <path
                                        d="M24.2982 11.0043C22.0512 11.0314 17.2204 11.4007 14.5184 13.8796C12.5085 15.8711 11.8067 18.8154 11.7245 22.4563C11.6562 26.0841 11.574 32.8968 18.1409 34.7518V37.5772C18.1409 37.5772 18.0997 38.7077 18.847 38.9396C19.7675 39.2309 20.2916 38.3612 21.1666 37.4267L22.7985 35.5813C27.2916 35.9549 30.733 35.0939 31.1294 34.9662C32.0411 34.6748 37.1765 34.0186 38.0156 27.2049C38.8766 20.1682 37.5965 15.7346 35.2856 13.7291H35.2716C34.5742 13.0868 31.7716 11.0446 25.5101 11.0218C25.5101 11.0218 25.0455 10.9903 24.2982 11.0034V11.0043ZM24.3752 12.9827C25.0131 12.9783 25.4007 13.0054 25.4007 13.0054C30.7006 13.0194 33.2302 14.6146 33.827 15.1518C35.773 16.8196 36.7757 20.8166 36.0416 26.6913C35.3442 32.3876 31.1792 32.7481 30.4084 32.9939C30.0802 33.0989 27.0492 33.8462 23.2307 33.6003C23.2307 33.6003 20.387 37.0321 19.498 37.9158C19.3571 38.0707 19.1926 38.1162 19.0876 38.0934C18.9371 38.0567 18.8916 37.8703 18.9004 37.6148L18.9275 32.9257C13.3581 31.3857 13.6862 25.5748 13.7457 22.5394C13.814 19.5041 14.3836 17.0208 16.0794 15.3391C18.3622 13.2749 22.4642 12.9967 24.3735 12.9827H24.3752ZM24.7952 16.0172C24.7494 16.0167 24.7039 16.0253 24.6614 16.0425C24.6189 16.0597 24.5803 16.0852 24.5477 16.1174C24.515 16.1496 24.4891 16.1879 24.4714 16.2302C24.4537 16.2725 24.4445 16.3179 24.4444 16.3637C24.4444 16.5597 24.6036 16.7146 24.7952 16.7146C25.6628 16.6981 26.5251 16.8535 27.3323 17.1719C28.1395 17.4902 28.8757 17.9653 29.4984 18.5696C30.7697 19.8042 31.3892 21.4632 31.4129 23.6323C31.4129 23.8239 31.5677 23.9832 31.7637 23.9832V23.9692C31.8561 23.9694 31.9448 23.9331 32.0106 23.8682C32.0763 23.8033 32.1137 23.7151 32.1146 23.6227C32.1571 22.6017 31.9915 21.5827 31.628 20.6277C31.2644 19.6727 30.7104 18.8016 29.9997 18.0673C28.6146 16.7137 26.8594 16.0163 24.7952 16.0163V16.0172ZM20.1831 16.8196C19.9355 16.7834 19.6829 16.8331 19.4674 16.9604H19.449C18.9487 17.2537 18.4981 17.6243 18.1137 18.0586C17.7944 18.4278 17.6211 18.8014 17.5756 19.1611C17.5485 19.3754 17.5669 19.5898 17.6307 19.7946L17.6535 19.8086C18.0131 20.8656 18.483 21.8823 19.057 22.8396C19.7964 24.1844 20.7063 25.4281 21.7642 26.5399L21.7957 26.5854L21.8456 26.6222L21.8771 26.6589L21.9139 26.6904C23.0297 27.7515 24.2764 28.6656 25.6239 29.4108C27.1639 30.2491 28.0984 30.6454 28.6592 30.8099V30.8187C28.8237 30.8686 28.9734 30.8913 29.1239 30.8913C29.6019 30.8563 30.0544 30.6622 30.4092 30.3401C30.8415 29.9557 31.208 29.5031 31.4942 29.0004V28.9917C31.7629 28.4859 31.6719 28.0073 31.2842 27.6836C30.508 27.005 29.6685 26.4023 28.7774 25.8837C28.1806 25.5599 27.5742 25.7559 27.3284 26.0841L26.8042 26.7447C26.5356 27.0728 26.0474 27.0273 26.0474 27.0273L26.0334 27.0361C22.3925 26.1059 21.4212 22.4196 21.4212 22.4196C21.4212 22.4196 21.3757 21.9182 21.7126 21.6627L22.3689 21.1342C22.683 20.8787 22.9017 20.2732 22.5649 19.6756C22.0498 18.7832 21.4485 17.9436 20.7694 17.1687C20.6209 16.986 20.4126 16.8617 20.1814 16.8178L20.1831 16.8196ZM25.4007 17.8591C24.9361 17.8591 24.9361 18.5608 25.4051 18.5608C25.9829 18.5702 26.5531 18.6933 27.0832 18.9232C27.6134 19.153 28.093 19.4851 28.4947 19.9004C28.8612 20.3047 29.1428 20.7784 29.3229 21.2935C29.5029 21.8086 29.5778 22.3545 29.543 22.8991C29.5446 22.9912 29.5822 23.0791 29.6478 23.1439C29.7134 23.2086 29.8017 23.2451 29.8939 23.2456L29.9079 23.2639C30.0007 23.2633 30.0896 23.2261 30.1552 23.1604C30.2209 23.0948 30.2581 23.0059 30.2587 22.9131C30.2902 21.5227 29.858 20.3563 29.0101 19.4218C28.1579 18.4873 26.9687 17.9632 25.4506 17.8591H25.4007ZM25.9756 19.7456C25.497 19.7316 25.4786 20.4473 25.9529 20.4613C27.1061 20.5208 27.6661 21.1036 27.7396 22.3023C27.7412 22.3933 27.7784 22.4801 27.8431 22.544C27.9079 22.6079 27.9951 22.644 28.0861 22.6444H28.1001C28.1469 22.6424 28.1928 22.6311 28.2352 22.6112C28.2776 22.5912 28.3155 22.5631 28.3469 22.5283C28.3783 22.4935 28.4024 22.4528 28.4179 22.4086C28.4333 22.3644 28.4398 22.3176 28.437 22.2708C28.3547 20.7081 27.5025 19.8278 25.9896 19.7464H25.9756V19.7456Z"
                                        fill="white" />
                                </svg>
                            </a>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="steps" id="steps">
        <div class="layout">
            <h2 class="steps__title">Этапы скупки</h2>
            <p class="steps__subtitle">
                Выгодно продайте свою технику по отличной цене за три шага. Мы быстро оценим ваш девайс и
                сразу отдадим вам деньги. Заплатим больше чем в ломбарде!
            </p>
            <ul class="steps__list">
                <li class="steps__list-item">
                    <div class="steps__list-item-num">1</div>
                    <h4 class="steps__list-item-title">Свяжитесь с нами</h4>
                    <p class="steps__list-item-text">
                        В мессенджерах, по телефону <br />
                        или по адресу, указанному на сайте.
                    </p>
                </li>
                <li class="steps__list-item">
                    <div class="steps__list-item-num">2</div>
                    <h4 class="steps__list-item-title">Оценка техники</h4>
                    <p class="steps__list-item-text">
                        Наши эксперты всего за несколько минут оценят вашу технику
                    </p>
                </li>
                <li class="steps__list-item">
                    <div class="steps__list-item-num active-steps">3</div>
                    <h4 class="steps__list-item-title">Получаете деньги</h4>
                    <p class="steps__list-item-text">
                        Получаете деньги сразу <br />
                        в день обращения
                    </p>
                </li>
            </ul>
        </div>
    </section>

    <section class="condition" id="conditions">
        <div class="layout">
            <h2 class="condition__title">Условия скупки</h2>
            <div class="condition__cards">
                <div class="condition__cards-card">
                    <img class="condition__cards-card-pic" src="https://100nout.by/wp-content/uploads/2023/07/image.jpg" alt="Ноутбук" />
                    <h2 class="condition__cards-card-title">В любом состоянии</h2>
                    <p class="condition__cards-card-subtitle">
                        Принимаем новую технику, бывшую в использовании и с дефектами
                    </p>
                </div>
                <div class="condition__cards-card">
                    <img class="condition__cards-card-pic" src="<?php nout_image_directory(); ?>noutbook2.jpg" alt="Ноутбук" />
                    <h2 class="condition__cards-card-title">У юрлиц и физлиц</h2>
                    <p class="condition__cards-card-subtitle">
                        Расчёт наличным, на карту <br> или безналичный расчёт
                    </p>
                </div>
                <div class="condition__cards-card">
                    <img class="condition__cards-card-pic" src="https://100nout.by/wp-content/uploads/2023/07/image.png" alt="Ноутбук" />
                    <h2 class="condition__cards-card-title">
                        Диагностика бесплатно
                    </h2>
                    <p class="condition__cards-card-subtitle">
                        Проверяем технику на наличие повреждений, конфигурацию и состояние системы
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="shop">
        <div class="layout">
            <div class="shop__wrapper">
                <div class="shop__pic">
                    <img class="shop__pic-img" src="<?php nout_image_directory(); ?>iphone.png" alt="Фото" />
                </div>
                <div class="shop__content">
                    <h2 class="shop__content-title">Магазин б/у техники</h2>
                    <p class="shop__content-subtitle">
                        Широкий ассортимент электроники для вас и вашего дома по
                        отличным ценам
                    </p>

                    <div class="skupka-intro__btn">
                        <a href="https://100nout.by/">Перейти в Магазин</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="online" id="online">
        <div class="layout">
            <div class="online__wrap">
                <div class="contacts__content">
                    <h2 class="contacts__title">
                        Онлайн скупка
                    </h2>
                    <p class="contacts__subtitle">
                        Достаточно отправить нам фотографии внешнего вида вашей техники, модель, марку
                        и год покупки
                    </p>
                    <p class="contacts__subtitle">
                        А ещё у нас есть бот, переходите по ссылке ниже и он оценит вашу технику за пару
                        минут
                    </p>
                    <div class="online__social">
                        <div class="online__telegram">
                            <a href="https://t.me/skypim_bot">
                                <svg width="50" height="50" viewBox="0 0 50 50" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_202_48)">
                                        <path
                                            d="M24.9999 50C38.807 50 49.9999 38.8071 49.9999 25C49.9999 11.1929 38.807 0 24.9999 0C11.1928 0 -6.10352e-05 11.1929 -6.10352e-05 25C-6.10352e-05 38.8071 11.1928 50 24.9999 50Z"
                                            fill="#4F9FDA" />
                                        <path
                                            d="M10.4046 24.7833C10.4046 24.7833 22.9046 19.6533 27.2398 17.8469C28.9017 17.1244 34.5375 14.8122 34.5375 14.8122C34.5375 14.8122 37.1387 13.8007 36.9219 16.2573C36.8496 17.2689 36.2716 20.8093 35.6936 24.6388C34.8265 30.058 33.8872 35.9827 33.8872 35.9827C33.8872 35.9827 33.7427 37.6447 32.5144 37.9337C31.2861 38.2227 29.2629 36.9222 28.9017 36.6331C28.6126 36.4164 23.4826 33.1649 21.604 31.5753C21.0982 31.1418 20.5202 30.2748 21.6762 29.2632C24.2774 26.8787 27.3843 23.9164 29.2629 22.0377C30.13 21.1706 30.997 19.1475 27.3843 21.6041C22.2543 25.1446 17.1965 28.4684 17.1965 28.4684C17.1965 28.4684 16.0404 29.1908 13.8728 28.5405C11.7051 27.8903 9.17622 27.0232 9.17622 27.0232C9.17622 27.0232 7.44222 25.9395 10.4046 24.7833Z"
                                            fill="white" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_202_48">
                                            <rect width="50" height="50" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </a>
                        </div>
                        <div class="online__sap">
                            <a href="viber://pa?chatURI=skypimby">
                                <svg width="50" height="50" viewBox="0 0 50 50" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M25 50C38.8071 50 50 38.8071 50 25C50 11.1929 38.8071 0 25 0C11.1929 0 0 11.1929 0 25C0 38.8071 11.1929 50 25 50Z"
                                        fill="#785BE4" />
                                    <path
                                        d="M24.2982 11.0043C22.0512 11.0314 17.2204 11.4007 14.5184 13.8796C12.5085 15.8711 11.8067 18.8154 11.7245 22.4563C11.6562 26.0841 11.574 32.8968 18.1409 34.7518V37.5772C18.1409 37.5772 18.0997 38.7077 18.847 38.9396C19.7675 39.2309 20.2916 38.3612 21.1666 37.4267L22.7985 35.5813C27.2916 35.9549 30.733 35.0939 31.1294 34.9662C32.0411 34.6748 37.1765 34.0186 38.0156 27.2049C38.8766 20.1682 37.5965 15.7346 35.2856 13.7291H35.2716C34.5742 13.0868 31.7716 11.0446 25.5101 11.0218C25.5101 11.0218 25.0455 10.9903 24.2982 11.0034V11.0043ZM24.3752 12.9827C25.0131 12.9783 25.4007 13.0054 25.4007 13.0054C30.7006 13.0194 33.2302 14.6146 33.827 15.1518C35.773 16.8196 36.7757 20.8166 36.0416 26.6913C35.3442 32.3876 31.1792 32.7481 30.4084 32.9939C30.0802 33.0989 27.0492 33.8462 23.2307 33.6003C23.2307 33.6003 20.387 37.0321 19.498 37.9158C19.3571 38.0707 19.1926 38.1162 19.0876 38.0934C18.9371 38.0567 18.8916 37.8703 18.9004 37.6148L18.9275 32.9257C13.3581 31.3857 13.6862 25.5748 13.7457 22.5394C13.814 19.5041 14.3836 17.0208 16.0794 15.3391C18.3622 13.2749 22.4642 12.9967 24.3735 12.9827H24.3752ZM24.7952 16.0172C24.7494 16.0167 24.7039 16.0253 24.6614 16.0425C24.6189 16.0597 24.5803 16.0852 24.5477 16.1174C24.515 16.1496 24.4891 16.1879 24.4714 16.2302C24.4537 16.2725 24.4445 16.3179 24.4444 16.3637C24.4444 16.5597 24.6036 16.7146 24.7952 16.7146C25.6628 16.6981 26.5251 16.8535 27.3323 17.1719C28.1395 17.4902 28.8757 17.9653 29.4984 18.5696C30.7697 19.8042 31.3892 21.4632 31.4129 23.6323C31.4129 23.8239 31.5677 23.9832 31.7637 23.9832V23.9692C31.8561 23.9694 31.9448 23.9331 32.0106 23.8682C32.0763 23.8033 32.1137 23.7151 32.1146 23.6227C32.1571 22.6017 31.9915 21.5827 31.628 20.6277C31.2644 19.6727 30.7104 18.8016 29.9997 18.0673C28.6146 16.7137 26.8594 16.0163 24.7952 16.0163V16.0172ZM20.1831 16.8196C19.9355 16.7834 19.6829 16.8331 19.4674 16.9604H19.449C18.9487 17.2537 18.4981 17.6243 18.1137 18.0586C17.7944 18.4278 17.6211 18.8014 17.5756 19.1611C17.5485 19.3754 17.5669 19.5898 17.6307 19.7946L17.6535 19.8086C18.0131 20.8656 18.483 21.8823 19.057 22.8396C19.7964 24.1844 20.7063 25.4281 21.7642 26.5399L21.7957 26.5854L21.8456 26.6222L21.8771 26.6589L21.9139 26.6904C23.0297 27.7515 24.2764 28.6656 25.6239 29.4108C27.1639 30.2491 28.0984 30.6454 28.6592 30.8099V30.8187C28.8237 30.8686 28.9734 30.8913 29.1239 30.8913C29.6019 30.8563 30.0544 30.6622 30.4092 30.3401C30.8415 29.9557 31.208 29.5031 31.4942 29.0004V28.9917C31.7629 28.4859 31.6719 28.0073 31.2842 27.6836C30.508 27.005 29.6685 26.4023 28.7774 25.8837C28.1806 25.5599 27.5742 25.7559 27.3284 26.0841L26.8042 26.7447C26.5356 27.0728 26.0474 27.0273 26.0474 27.0273L26.0334 27.0361C22.3925 26.1059 21.4212 22.4196 21.4212 22.4196C21.4212 22.4196 21.3757 21.9182 21.7126 21.6627L22.3689 21.1342C22.683 20.8787 22.9017 20.2732 22.5649 19.6756C22.0498 18.7832 21.4485 17.9436 20.7694 17.1687C20.6209 16.986 20.4126 16.8617 20.1814 16.8178L20.1831 16.8196ZM25.4007 17.8591C24.9361 17.8591 24.9361 18.5608 25.4051 18.5608C25.9829 18.5702 26.5531 18.6933 27.0832 18.9232C27.6134 19.153 28.093 19.4851 28.4947 19.9004C28.8612 20.3047 29.1428 20.7784 29.3229 21.2935C29.5029 21.8086 29.5778 22.3545 29.543 22.8991C29.5446 22.9912 29.5822 23.0791 29.6478 23.1439C29.7134 23.2086 29.8017 23.2451 29.8939 23.2456L29.9079 23.2639C30.0007 23.2633 30.0896 23.2261 30.1552 23.1604C30.2209 23.0948 30.2581 23.0059 30.2587 22.9131C30.2902 21.5227 29.858 20.3563 29.0101 19.4218C28.1579 18.4873 26.9687 17.9632 25.4506 17.8591H25.4007ZM25.9756 19.7456C25.497 19.7316 25.4786 20.4473 25.9529 20.4613C27.1061 20.5208 27.6661 21.1036 27.7396 22.3023C27.7412 22.3933 27.7784 22.4801 27.8431 22.544C27.9079 22.6079 27.9951 22.644 28.0861 22.6444H28.1001C28.1469 22.6424 28.1928 22.6311 28.2352 22.6112C28.2776 22.5912 28.3155 22.5631 28.3469 22.5283C28.3783 22.4935 28.4024 22.4528 28.4179 22.4086C28.4333 22.3644 28.4398 22.3176 28.437 22.2708C28.3547 20.7081 27.5025 19.8278 25.9896 19.7464H25.9756V19.7456Z"
                                        fill="white" />
                                </svg>
                            </a>


                        </div>
                    </div>
                </div>
                <div class="contacts__form" id="form">
                    <div class="tg-form-header">
                        <div class="tg-form-title">Оценка онлайн</div>
                        <div class="tg-form-description">Заполните форму и мы отправим вам предварительную
                            стоимость в течении 10 минут</div>
                    </div>

                    <form class="tg-form js-tg-form">
                        <input type="text" id="fname" name="fname" placeholder="Имя"><br>
                        <input type="text" id="ftel" name="ftel" required placeholder="Номер телефона*"><br>
                        <textarea name="ftext" class="ftext" required placeholder="Опишите ваш товар максимально подробно *"></textarea><br>
                        <div class="popup__time-warning" style="margin: 10px 0;">
                            <input size="40" maxlength="400" class="popup__time-warning-hidden" aria-invalid="false" value="" type="text" name="fhidden-text">
                            <div class="popup__time-warning-text">
                                Рабочий день уже завершён. Пожалуйста, выберите удобное время, и мы свяжемся с вами завтра!
                            </div>
                            <select class="popup__time-warning-select popup__form-input" aria-invalid="false" name="fcall-time">
                                <option value="">—Выберите вариант—</option>
                                <option value="с 9:00 до 10:00">с 9:00 до 10:00</option>
                                <option value="с 10:00 до 11:00">с 10:00 до 11:00</option>
                                <option value="с 11:00 до 12:00">с 11:00 до 12:00</option>
                                <option value="с 12:00 до 13:00">с 12:00 до 13:00</option>
                                <option value="с 13:00 до 14:00">с 13:00 до 14:00</option>
                                <option value="с 14:00 до 15:00">с 14:00 до 15:00</option>
                                <option value="с 15:00 до 16:00">с 15:00 до 16:00</option>
                                <option value="с 16:00 до 17:00">с 16:00 до 17:00</option>
                                <option value="с 17:00 до 18:00">с 17:00 до 18:00</option>
                                <option value="с 18:00 до 19:00">с 18:00 до 19:00</option>
                                <option value="с 19:00 до 20:00">с 19:00 до 20:00</option>
                                <option value="с 20:00 до 21:00">с 20:00 до 21:00</option>
                            </select>
                        </div>
                        <br>
                        <span id="status">Выберите максимум 4 фото для загрузки</span><br />
                        <input type="file" class="ffile" name="ffile" id="file-input" accept=".jpg,.jpeg,.png" name="file-input" multiple /><br />
                        <input class="tg-form-submit" id="disbutton" type="submit" value="Отправить на оценку">
                    </form>

                    <div class="tg-form-modal">
                        <div class="tg-form-modal-wrap">
                            <div class="close_modal">+</div>
                            <div class="tg-form-modal-wrap-text">Спасибо за заявку!<br /> С вами свяжутся в течении 10 минут</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contacts" id="contacts">
        <div class="layout">
            <div class="contacts__wrap">
                <div class="contacts__content">
                    <h3 class="contacts__title">Контакты</h3>

                    <ul class="contacts__list">
                        <li class="contacts__list-item">
                            <div class="contacts__list-item-svg">
                                <svg width="25" height="33" viewBox="0 0 25 33" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12.5 0C5.74174 0 0.262388 5.47935 0.262388 12.2376C0.262388 22.2757 12.5 33 12.5 33C12.5 33 24.7376 22.8259 24.7376 12.2383C24.7376 5.47935 19.2583 0 12.5 0ZM12.5 19.6633C8.32314 19.6633 4.93761 16.2764 4.93761 12.1009C4.93761 7.92404 8.32247 4.53851 12.5 4.53851C16.6762 4.53851 20.0624 7.92404 20.0624 12.1009C20.0624 16.2764 16.6762 19.6633 12.5 19.6633Z"
                                        fill="#58B741" />
                                </svg>
                            </div>
                            <p class="contacts__list-item-text">
                                <span class="contacts__list-item-text-title">
                                    Адрес
                                </span>
                                <span class="contacts__list-item-text-subtitle">
                                    г. Минск, <br> пр-т Независимости д. 94
                                </span>
                            </p>
                        </li>
                        <li class="contacts__list-item">
                            <div class="contacts__list-item-svg">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M16 0.166748C7.25525 0.166748 0.166667 7.25533 0.166667 16.0001C0.166667 24.7448 7.25525 31.8334 16 31.8334C24.7448 31.8334 31.8333 24.7448 31.8333 16.0001C31.8333 7.25533 24.7448 0.166748 16 0.166748ZM21.2139 23.4528L14.4167 16.6556V6.50008H17.5833V15.3446L23.4528 21.214L21.2139 23.4528Z"
                                        fill="#58B741" />
                                </svg>
                            </div>
                            <p class="contacts__list-item-text">
                                <span class="contacts__list-item-text-title">
                                    Часы работы
                                </span>
                                <span class="contacts__list-item-text-subtitle">
                                    с 10:00 до 21:00
                                </span>
                            </p>
                        </li>
                        <li class="contacts__list-item">
                            <div class="contacts__list-item-svg">
                                <svg width="31" height="33" viewBox="0 0 31 33" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.5072 15.0497L16.9143 20.8199C17.5006 21.4455 18.5623 21.5348 19.7451 20.9454C22.7553 19.6833 23.7568 19.3756 24.3286 19.9857L30.5442 26.6187C31.2789 27.4027 31.0828 28.7087 30.2612 29.5855C25.995 34.1382 19.0608 34.1382 14.7946 29.5855L3.18363 17.1949C-1.06121 12.665 -1.06121 5.33649 3.18363 0.806611C4.02257 -0.0886621 5.31093 -0.308944 6.07333 0.504655L12.2889 7.13759C12.8276 7.7125 12.5388 8.78331 11.3625 11.9738C10.8157 13.2192 10.9034 14.4053 11.5072 15.0497Z"
                                        fill="#58B741" />
                                </svg>
                            </div>
                            <p class="contacts__list-item-text">
                                <span class="contacts__list-item-text-title">
                                    Контактный номер
                                </span>
                                <a href="tel:+375 33 375 74 00"
                                    class="contacts__list-item-text-subtitle line-hover">
                                    +375 33 375 74 00
                                </a>
                            </p>
                        </li>
                    </ul>

                    <div class="contacts__btn">
                        <button type="button" class="js-popup-btn" data-popup="1">Перезвоните мне</button>
                    </div>
                </div>
                <div class="contacts__img _ibg">
                    <img src="https://100nout.by/wp-content/uploads/2023/07/rectangle-114.png" alt="" />
                </div>
            </div>
        </div>
    </section>
</div>
<?php get_footer();
