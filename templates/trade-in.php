<?php /* Template Name: Трейд Ин */ ?>
<?php get_header();?>

<section class="layout trade-in">
                <h1 class="trade-in__title text-5xl">
                    Trade-in
                </h1>
                <div class="trade-in__banner banner">
                <img class="banner-xl" src="<?php the_field('tr-banner_xl'); ?>" alt="Баннер">
                    <img class="banner-l" src="<?php the_field('tr-banner_l'); ?>" alt="Баннер">
                    <img class="banner-m" src="<?php the_field('tr-banner_m'); ?>" alt="Баннер">
                    <img class="banner-s" src="<?php the_field('tr-banner_s'); ?>" alt="Баннер">
                    <img class="banner-xs" src="<?php the_field('tr-banner_xs'); ?>" alt="Баннер">
                </div>
                <h2 class="trade-in__subtitle text-4xl">
                    Принесите старую технику в наш магазин по программе <a href="#"
                        class="link link-primary">«Trade-in»</a> и купите новый девайс по специальным
                    условиям
                </h2>
                <h2 class="trade-in__subtitle trade-in__subtitle--mt text-4xl">
                    Как работает «Trade-in»?
                </h2>
                <ul class="trade-in__list">
                    <li class="trade-in__list-item">
                        <div class="card">
                            <h3 class="card__title text-3xl">
                                Сделайте оценку товара
                            </h3>
                            <p class="card__text text-lg">
                                Онлайн и в магазине. Свяжитесь с нами по телефону или в чате и узнайте стоимость вашей
                                техники.
                            </p>
                        </div>
                    </li>
                    <li class="trade-in__list-item">
                        <div class="card">
                            <h3 class="card__title text-3xl">
                                Получите скидку
                            </h3>
                            <p class="card__text text-lg">
                                Стоимость старой техники будет зачтена в счет покупки новой.
                            </p>
                        </div>
                    </li>
                </ul>
                <p class="trade-in__text text-lg trade-in__text-sm">
                    Хотите что-то уточнить? Спросите у нас по телефонам <a class="text link link-secondary trade-in__text-font"
                        href="tel:+375 33 375 74 00">+375 33 375 74 00</a>, <a class="text link link-secondary trade-in__text-font"
                        href="tel:+375 44 415 74 00">+375 44 415 74 00</a> или напишите в чат на сайте.
                </p>
                <div class="trade-in__map map">
                    <h2 class="map__title text-4xl">
                        Как к нам добраться
                    </h2>
                    <div class="map__body">
                        <div class="map__frame">
                            <script type="text/javascript" charset="utf-8" async
                                src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A31b2aac7a7f8d32a102e30f0c3b09e68125073b9717ea50e6307d314fbb80737&amp;width=100%25&amp;height=100%&amp;lang=ru_RU&amp;scroll=false"></script>
                        </div>
                        <div class="map__card card">
                            <h2 class="card__title text-3xl">
                                Адрес нашего шоурума
                            </h2>
                            <p class="card__text text text-lg">
                                220114, Минск, метро Московская, Проспект Независимости д. 94
                            </p>
                            <p class="card__text card__text--mt text text-lg text--semibold">
                                Время работы 10:00 до 20:00
                            </p>
                        </div>
                    </div>
                </div>
            </section>

<?php get_footer();