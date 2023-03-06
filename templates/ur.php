<?php /* Template Name: Юр лицам */ ?>
<?php get_header();?>

<section class="ur layout">
                <h1 class="ur__title text-5xl">
                    Покупайте как юрлицо
                </h1>
                <div class="ur__banner banner">
                    <img class="banner-xl" src="<?php the_field('ur-banner_xl'); ?>" alt="Баннер">
                    <img class="banner-l" src="<?php the_field('ur-banner_l'); ?>" alt="Баннер">
                    <img class="banner-m" src="<?php the_field('ur-banner_m'); ?>" alt="Баннер">
                    <img class="banner-s" src="<?php the_field('ur-banner_s'); ?>" alt="Баннер">
                    <img class="banner-xs" src="<?php the_field('ur-banner_xs'); ?>" alt="Баннер">
                </div>
                <h2 class="ur__subtitle text-4xl">
                    Продаём б/у ноутбуки юридическим лицам на условиях безналичного расчета.
                </h2>
                <p class="ur__text text-lg">
                    Помогаем экономить на покупке ноутбуков для персонала, клиентов компании и их обслуживании. Экономия
                    только на покупке устройства составит до 60% от стоимости нового.
                </p>
                <ul class="ur__list">
                    <li class="ur__list-item">
                        <div class="card ur__card-short">
                            <p class="card__text text-lg">
                                Гарантия <span class="ur__text-color">от одного месяца</span> 	для наших корпоративных клиентов
                            </p>
                        </div>
                    </li>
                    <li class="ur__list-item">
                        <div class="card ur__card-long">
                            <p class="ur__card-text text-lg">
                                Бесплатная поддержка нашей техники <span class="ur__text-color">на 12 месяцев</span> (замена операционной системы, профилактика устройств, быстрый ремонт в случае поломки и т.д.)
                            </p>
                        </div>
                    </li>
                </ul>
                <h4 class="ur__title-sm text-2xl">
                    По всем вопросам вы можете звонить по телефонам, указанным на нашем сайте.
                </h4>
								<h2 class="text-4xl ur__text-2xl ur__subtitle-text">
									Мы запускаем программу лояльности для юридических лиц. 
								</h2>
								<p class="text-lg">
									При осуществлении безналичных покупок клиенту предоставляется 12 месяцев гарантии на товары, а также возможность приобрести страховой полис со скидкой до 50%.
								</p>
								<p class="text-lg ur__text-lg">
									Разрабатываем выгодные предложения для наших клиентов. Чтобы помочь им экономить на покупке техники, мы предоставляем оптимальную цену и расширенную гарантию. <span class="ur__text-color">При первой закупке мы автоматически зарегистрируем клиента в системе лояльности.</span> 
								</p>
                <h2 class="ur__subtitle text-4xl ur__subtitle-text">
                    Наши реквизиты
                </h2>
                <p class="ur__text ur__text--short text-lg">
                    ИП Валько Денис Валерьевич <br>
                    Адрес: Брестская обл. г. Барановичи ул. Репина д. 58 кв. 10
                    УНП 291558723 <br>
                    Номер счета (IBAN): р/с BY75 MTBK 3013 0001 0933 0005 2882 <br>
                    Код банка (BIC): MTBKBY22 <br>
                    ЗАО «МТБанк», УНП 100394906, г. Минск ул. Толстого д. 10,
                    Тел: +37544–415–74–00, +37533–375–74–00
                </p>
            </section>

<?php get_footer();