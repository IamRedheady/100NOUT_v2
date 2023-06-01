<?php /* Template Name: Доставка */ ?>
<?php get_header();?>

<section class="layout delivery">
                <h1 class="delivery__title text-5xl">Способы получения заказа</h1>
                <div class="banner">
                    <img class="banner-xl" src="<?php the_field('d-banner_xl'); ?>" alt="Баннер">
                    <img class="banner-l" src="<?php the_field('d-banner_l'); ?>" alt="Баннер">
                    <img class="banner-m" src="<?php the_field('d-banner_m'); ?>" alt="Баннер">
                    <img class="banner-s" src="<?php the_field('d-banner_s'); ?>" alt="Баннер">
                    <img class="banner-xs" src="<?php the_field('d-banner_xs'); ?>" alt="Баннер">
                </div>
                <h2 class="delivery__subtitle text-4xl">Самовывоз</h2>

                <ul class="delivery__list">
                    <li class="delivery__list-item">
                        <div class="card delivery__card-item">
                            <p class="text-lg delivery__card-text">
															При оформлении заказа с 9.00 до 16.00 <span class="delivery__text-color">в рабочие дни</span> самовывоз возможен в день заказа с 10.00 до 20.00 если товар находится в магазине. 
                            </p>
														<p class="text-lg">
															Если товар находится на удалённом складе, самовывоз возможен на следующий день после заказа с 17.00 до 20.00. 
													</p>
                        </div>
                    </li>
                    <li class="delivery__list-item">
                        <div class="card delivery__card-item">
													<p class="text-lg delivery__card-text">
														При заказе <span class="delivery__text-color">в выходные дни</span> с 10.00 до 20.00 самовывоз возможен в день заказа с 10.00 до 20.00 если товар находится в магазине.
													</p>
													<p class="text-lg">
													Если товар находится на удалённом складе, самовывоз возможен с 17.00 до 20.00 в любой рабочий день. 
													</p>
                        </div>
                    </li>
								</ul>
								<div class="card delivery__card delivery__card-item">
									<p class="text-lg">
                                    Для юр. лиц. самовывоз возможен в любой рабочий день в промежуток времени с 15.00 до 20.00 вне зависимости от времени оплаты.
									</p>
								</div>
								<p class="text-base">
									* Срок хранения заказа в магазине —<span class="delivery__text-color">2 рабочих дня</span>. По истечении этого срока заказ будет аннулирован (Возможно изменение сроков хранения заказа по согласованию с менеджером, при условии наличия большого кол-ва одинаковых позиций на складе). Для юрлиц срок действия счёт-фактуры 2 рабочих дня, срок хранения заказа после оплаты — 30 дней.
								</p>
								<h2 class="delivery__subtitle text-4xl">Доставка по Минску</h2>

								<ul class="delivery__list">
									<li class="delivery__list-item">
											<div class="card delivery__card-item">
												<p class="text-lg delivery__card-text">
													При оформлении заказа с 9.00 до 16.00 доставка по Минску осуществляется в этот же день в промежуток времени с 18.00 до 21.00.											
												</p>
												<p class="text-lg delivery__card-text">
													Так же возможна доставка в первой половине дня, на следующий рабочий день после заказа в промежуток времени с 9.00 до 12.00 или с 12.00 до 16.00. 
												</p>
												<p class="text-lg delivery__card-text">
													<span class="delivery__text-font">Возможна доставка в выходные дни, по согласованию с менеджером.</span>
												</p>
												<p class="text-lg">
													Стоимость доставки составляет 10 рублей и не зависит от стоимости приобретаемого товара.
												</p>
											</div>
									</li>
									<li class="delivery__list-item">
											<div class="card delivery__card-item">
												<p class="text-lg delivery__card-text">
													Возможна <span class="delivery__text-color">экспресс доставка по городу</span> в течении часа после заказа. 
												</p>
												<p class="text-lg delivery__card-text">
													Стоимость экспресс доставки составляет 20 рублей и не зависит от стоимости приобретаемого товара.
												</p>
												<p class="text-lg">
													<span class="delivery__text-font">Возможна доставка по городу позднее 21.00 после согласования с менеджером по одному из номеров указанных у нас на сайте.</span>
												</p>
											</div>
									</li>
							</ul>
							<h2 class="delivery__subtitle text-4xl">Доставка по РБ</h2>

							<ul class="delivery__list">
								<li class="delivery__list-item">
										<div class="card delivery__card-item">
											<h3 class="text-3xl delivery__card-subtitle">Курьерская служба</h3>
											<p class="text-lg delivery__card-text">
												Доставка по РБ осуществляется с ВТ по СБ. Срок доставки от 1 до 3х дней, в зависимости от населённого пункта.
											</p>
											<p class="text-lg">
												Стоимость доставки составляет от 15 рублей, в зависимости от приобретаемого товара.	
											</p>
										</div>
								</li>
								<li class="delivery__list-item">
										<div class="card delivery__card-item">
											<h3 class="text-3xl delivery__card-subtitle">Европочта</h3>
											<p class="text-lg delivery__card-text">
												Возможна отправка товара стоимостью <span class="delivery__text-font">до 1000 рублей</span>.
											</p>
											<p class="text-lg delivery__card-text">
												Стоимость доставки рассчитывается индивидуально, в зависимости от хрупкости, веса, габаритов и оценочной стоимости товара.
											</p>
											<p class="text-lg">
												Отправка осуществляется на следующий день после заказа.
											</p>
										</div>
								</li>
						</ul>
						<div class="card delivery__card delivery__card-item">
							<h3 class="text-3xl delivery__card-subtitle">Почта</h3>
							<p class="text-lg delivery__card-text">
								Возможна отправка товаров РУП «Белпочта» стоимостью <span class="delivery__text-font">до 100 рублей</span>.
							</p>
							<p class="text-lg">
							Стоимость доставка составляет 10 рублей. Отправка товара осуществляется на следующий день после заказа.
							</p>
						</div>
						<h2 class="delivery__subtitle text-4xl">Условия доставки и получения заказов:</h2>
						<p class="text-lg delivery__text-lg">
							— Доставка осуществляется только для товаров, входящих в состав заказа. Транспортировка нескольких товаров с целью выбора одного из них на месте доставки не производится.
						</p>     
						<p class="text-lg delivery__text delivery__text-lg">
							— Доставка по указанному адресу обязывает покупателя находиться на месте в согласованное накануне время.
						</p>
						<div class="card delivery__card delivery__card-item">
							<h3 class="text-3xl delivery__card-subtitle">Внимание!</h3>
							<p class="text-lg delivery__card-text delivery__text-sm">
								Если непредвиденные обстоятельства мешают вам получить заказ, необходимо как можно раньше связаться с оператором контактного центра по телефону <a style="white-space: nowrap;" class="link link-primary" href="tel:+375444157400">+375 (44) 415-74-00</a> и договориться о переносе времени или места доставки.
							</p>
							<p class="text-lg delivery__text-sm">
								В случае отсутствия Покупателя в назначенное время доставки, курьер ожидает 15 минут, после чего уезжают.
							</p>
						</div>
            </section>

<?php get_footer();