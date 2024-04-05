<?php /* Template Name: Оплата */ ?>
<?php get_header();?>

<section class="payment layout">
                <h1 class="payment__title text-5xl">Оплата</h1>
                <p class="payment__text text-lg">
                    На данный момент оплата осуществляется только при получении товара в магазине либо курьеру при
                    доставке.
                </p>
                <ul class="payment__list">
                    <li class="payment__list-item">
                        <div class="card">
                            <h3 class="card__title text-3xl">
                                Наличный расчет
                            </h3>
                            <p class="card__text text-lg">
                                Вы можете оплатить покупку при доставке, рассчитываясь с курьером наличными деньгами.
                            </p>
                            <p class="card__text card__text--mt text-lg">
                                Если вы осуществляете самовывоз, оплату необходимо будет совершить в кассе пункта выдачи
                                товара.
                            </p>
                        </div>
                    </li>
                    <li class="payment__list-item">
                        <div class="card">
                            <h3 class="card__title text-3xl">
                                Безналичный расчет
                            </h3>
                            <p class="card__text text-lg">
                                Вы можете оплатить покупку при доставке, оплачивая через эквайринг.
                            </p>
                            <p class="card__text card__text--mt text-lg">
                                Если вы осуществляете самовывоз, оплату необходимо будет совершить в кассе пункта выдачи
                                товара.
                            </p>
                        </div>
                    </li>
                </ul>
                <div class="payment__credit">
                    <div class="card">
                        <h3 class="card__title text-3xl">
                            Оплата в рассрочку:
                        </h3>
                        <ul class="card__list card__list--column">
                            <li class="card__list-item text-lg payment__text">
                                Черепаха от ВТБ – 8 месяцев рассрочки;
                            </li>
                            <li class="card__list-item text-lg payment__text">
                                Магнит от Беларусбанк – 3 месяца рассрочки;
                            </li>
                            <li class="card__list-item text-lg payment__text">
                                Карта покупок от Белгазпромбанка – 3 месяца рассрочки;
                            </li>
                            <li class="card__list-item text-lg payment__text">
                                Халва от МТБанк – 2 месяца рассрочки;
                            </li>
                            <li class="card__list-item text-lg payment__text">
                                Халва микс от МТБанк – 4 месяца рассрочки;
                            </li>
                            <li class="card__list-item text-lg payment__text">
                                Карта FAN от Сбер банка – 3 месяца рассрочки.
                            </li>
                        </ul>
                        <h3 class="card__title text-3xl payment__title-item">
                            Оплата картами рассрочки возможна:
                        </h3>
                        <p class="card__text text-lg">
                            Через терминал при доставке курьером;<br> В наших собственных ПВЗ.
                        </p>
												<div class="payment__credit-gradient">
												</div>
                        <div class="payment__credit-pic">
                            <img class="payment__credit-pic-img" src="<?php nout_image_directory() ?>p1.png" alt="Фото">
                        </div>
                    </div>
                </div>
            </section>

<?php get_footer();