<?php /* Template Name: Контакты */ ?>
<?php get_header();?>

<section class="contacts layout">
                <h1 class="contacts__title text-5xl">Контакты</h1>
                <ul class="contacts__list">
                    <li class="contacts__list-item">
                        <div class="card">
                            <h2 class="card__title text-3xl">
                                Связь с нами
                            </h2>
                            <p class="card__text text text-lg">
                                Операторы-консультанты доступны по номерам телефонов в рабочее время магазина с 9:00 до
                                21:00
                            </p>
                            <ul class="card__list">
                                <li class="card__list-item">
                                    <a href="tel:+375 44 574 74 00" class="card__list-item-link text text-base link link-secondary">
                                        <img src="<?php nout_image_directory() ?>Image.png" alt="Фото">
                                        +375 44 574 74 00
                                    </a>
                                </li>
                                <li class="card__list-item">
                                    <a href="tel:+375 33 375 74 00" class="card__list-item-link text text-base link link-secondary">
                                        <img src="<?php nout_image_directory() ?>mts.png" alt="Фото">
                                        +375 33 375 74 00
                                    </a>
                                </li>
                                <li class="card__list-item">
                                    <a href="tel:+375 25 977 74 00" class="card__list-item-link text text-base link link-secondary">
                                        <img src="<?php nout_image_directory() ?>life.png" alt="Фото">
                                        +375 25 977 74 00
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="contacts__list-item">
                        <div class="card contacts__list-item-card--back">
                            <h2 class="card__title text-3xl">
                                Социальные сети
                            </h2>
                            <ul class="card__list">
                                <li class="card__list-item">
                                    <a href="https://www.instagram.com/100nout.by/" class="card__list-item-link text text-base link link-secondary">
                                        Инстаграм
                                    </a>
                                </li>
                                <li class="card__list-item">
                                    <a href="https://t.me/stonoutby" class="card__list-item-link text text-base link link-secondary">
                                        Телеграм
                                    </a>
                                </li>
                                <li class="card__list-item">
                                    <a href="https://vk.com/stonout" class="card__list-item-link text text-base link link-secondary">
                                        Вконтакте
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
                <div class="contacts__map map">
                    <h2 class="map__title text-4xl">
                        Как к нам добраться
                    </h2>
                    <div class="map__body">
                        <div class="map__frame">
                            <script type="text/javascript" charset="utf-8" async
                                src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A31b2aac7a7f8d32a102e30f0c3b09e68125073b9717ea50e6307d314fbb80737&amp;width=100%25&amp;height=100%&amp;lang=ru_RU&amp;scroll=false"></script>
                        </div>
                        <div class="map__card card contacts__map-adress">
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
                <ul class="contacts__list">
                    <li class="contacts__list-item">
                        <div class="card">
                            <h2 class="card__title text-3xl">
                                Юридическая информация
                            </h2>
                            <p class="card__text card__text--lh text text-lg">
                                ИП Валько Денис Валерьевич<br>
                                Адрес: Брестская обл. г. Барановичи ул. Репина д. 58 кв. 10
                                УНП 291558723<br>
                                Номер счета (IBAN): р/с BY75 MTBK 3013 0001 0933 0005 2882
                                Код банка (BIC): MTBKBY22 <br>
                                ЗАО «МТБанк», УНП 100394906, г. Минск ул. Толстого д. 10,
                                Тел: +37544–415–74–00, +37533–375–74–00
                            </p>
                        </div>
                    </li>
                    <li class="contacts__list-item">
                        <div class="card">
                            <h2 class="card__title text-3xl">
                                Если у вас возник вопрос — <span class="link link-primary">напишите нам</span>
                            </h2>
                            <a href="mailto:info@100nout.by" class="card__subtitle text-2xl link link-secondary">
                                info@100nout.by
                            </a>

                        </div>
                    </li>
                </ul>
            </section>

<?php get_footer();