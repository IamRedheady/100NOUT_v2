<?php /* Template Name: О компании */ ?>
<?php get_header();?>
<section class="layout company">
                <h1 class="company__title text-5xl">
                    О компании
                </h1>
                <div class="company__banner banner">
                    <img class="banner-xl" src="<?php the_field('co-banner_xl'); ?>" alt="Баннер">
                    <img class="banner-l" src="<?php the_field('co-banner_l'); ?>" alt="Баннер">
                    <img class="banner-m" src="<?php the_field('co-banner_m'); ?>" alt="Баннер">
                    <img class="banner-s" src="<?php the_field('co-banner_s'); ?>" alt="Баннер">
                    <img class="banner-xs" src="<?php the_field('co-banner_xs'); ?>" alt="Баннер">
                </div>
                <h2 class="company__subtitle text-4xl">
                    Мы работаем с 2015 года. Предлагаем нашим клиентам надёжную, протестированную, обслуженную б/у
                    технику с гарантией и сервисом по доступной цене.
                </h2>
                <ul class="company__list">
                    <li class="company__list-item">
                        <div class="card company__list-item-card">
                            <p class="company__list-content text-lg">
                                Мы предоставляем возможность вернуть или обменять б/у устройство в течение 14 дней. С
                                нашими клиентами мы создаем опыт покупки подержанной техники, аналогичный тому, что
                                предоставляется при покупке нового!
                            </p>
                        </div>
                    </li>
                    <li class="company__list-item">
                        <div class="card company__list-item-card">
                            <p class="company__list-content text-lg">
                                Мы осуществляем гарантийное и послегарантийное обслуживание своих устройств по выгодным
                                условиям для наших клиентов. Также мы производим модернизацию и обслуживание сторонних
                                устройств. Работаем с физическими и юридическими лицами. Гарантия сервиса!
                            </p>
                        </div>
                    </li>
                    <li class="company__list-item">
                        <div class="card company__list-item-card">
                            <p class="company__list-content text-lg">
                                Программа Trade-in даёт вам возможность обменять свой старый гаджет на новый с гарантией
                                и выгодой до 90%.
                            </p>
                        </div>
                    </li>
                    <li class="company__list-item">
                        <div class="card company__list-item-card">
                            <p class="company__list-content text-lg">
                                Большинство товаров из нашего каталога доставляются ежедневно. Оставляйте заказ
                                до 16:00 и получайте его в тот же день по г. Минску и на следующий день по всей
                                Республике.
                            </p>
                        </div>
                    </li>
                    <li class="company__list-item">
                        <div class="card company__list-item-card">
                            <p class="company__list-content text-lg">
                                Гарантия на все предложения составляет от 30 дней, с возможностью приобретения
                                дополнительного гарантийного обслуживания до 12 месяцев.
                            </p>
                        </div>
                    </li>
                    <li class="company__list-item">
                        <div class="card company__list-item-card ">
                            <p class="company__list-content text-lg">
                                Наш официальный магазин и сервисный центр — это служба одного окна, это место, где можно
                                купить, продать, сдать в trade-in, обслужить своё устройство. Он удобно расположен
                                в центре Минска, рядом со станцией метро «Московская». <img src="<?php nout_image_directory();?>metro-logo.svg"
                                    alt="фото">
                            </p>
                        </div>
                    </li>
                    <li class="company__list-item">
                        <div class="card company__list-item-card">
                            <p class="company__list-content text-lg">
                                Мы уже давно на рынке б/у техники. За этот период мы получили много ценного опыта,
                                которым мы с радостью поделимся с вами. Обращаясь к нам, вы получите полную и правильную
                                консультацию специалиста в кратчайшие сроки.
                            </p>
                        </div>
                    </li>
                </ul>
                <div class="company__content">
                    <p class="company__content-text text-lg">
                        Все эти преимущества делают наши товары отличным выбором для предприятий и частных лиц, которые
                        хотят купить качественный и проверенный товар по приятной цене. Все товары проходят процедуру
                        обновления — это полная техническая диагностика, чистка загрязнений и пыли, замена всех
                        изношенных деталей на новые или более совершённые, при необходимости производится модульное
                        восстановление. Также выполняется обязательная установка свежей операционной системы с
                        драйверами и первоочерёдными программами.
                    </p>
                    <p class="company__content-text text-lg">
                        Мы предоставляем гарантию сервиса! Наш сервисный отдел поможет при любой проблеме и устранит
                        любые неполадки, возникшие у вас в процессе эксплуатации устройства.
                    </p>
                    <p class="company__content-text text-lg">
                        Наши запасы постоянно обновляются, поэтому заходите на наш сайт, чтобы увидеть новые поступления
                        и акционные предложения.
                    </p>
                    <p class="company__content-text text-lg">
                        Нам искренне интересны ваши задачи, которые вы ставите при выборе товара. Мы ставим интересы
                        клиента в центр нашей работы, чтобы приносить пользу и строить гармоничные отношения с нашими
                        клиентами.
                    </p>
                    <div class="company__content-thanks">
                        <div class="company__content-thanks-item">
                            <h3 class="company__content-thanks-text text-3xl">
                                Спасибо, что выбрали <span
                                    class="company__content-thanks-text-color text-3xl">100NOUT!</span>
                            </h3>
                            <div class="company__content-thanks-logo">
                                <img class="company__content-thanks-logo-img" src="<?php nout_image_directory();?>company-logo.svg"
                                    alt="Логотип">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <?php get_footer();