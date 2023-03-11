<?php /* Template Name: Отзывы */ ?>
<?php get_header();?>

<section class="reviews layout">
    <h1 class="reviews__title text-5xl">Отзывы</h1>
    <div class="reviews__intro-card reviews__intro-card--logo">
        <h3 class="reviews__intro-card-text text-3xl">
            Мы ценим ваше мнение и будем рады отзывам о нашей работе. Благодарны вам за положительную оценку и за конструктивную критику, которая поможет улучшить наш сервис.
        </h3>
    </div>
    <div class="reviews__body">
        <div class="reviews__column">
            <ul class="reviews__list">
                <?php

                /*
                *  Перебираем Повторитель
                */

                if( get_field('otzyvy-ya') ): ?>

                    <?php while( has_sub_field('otzyvy-ya') ): ?>
                        <li class="reviews__list-item">
                            <div class="card reviews__card">
                                <div class="reviews__list-item-pic">
                                    <img src="<?php the_sub_field('otzyvy-ya-izobrazhenie'); ?>" alt="Фото"
                                        class="reviews__list-item-pic-img">
                                </div>
                                <div class="reviews__content">
                                    <h4 class="reviews__content-title text-2xl">
                                        <?php the_sub_field('otzyvy-ya-imya_i_familiya'); ?>
                                    </h4>
                                    <p class="text-base">
                                        <?php the_sub_field('otzyvy-ya-text'); ?>
                                    </p>
                                </div>
                            </div>
                        </li>
                    <?php endwhile; ?>

                <?php endif; ?>
            </ul>
            <a class="btn btn-primary reviews__btn" href="<?php the_field('ssylka_na_vse_otzyvy-ya');?>">Больше отзывов на Яндекс Картах</a>
        </div>
        <div class="reviews__column">
            <ul class="reviews__list">
                <?php
                    if( get_field('otzyvy-go') ): ?>

                        <?php while( has_sub_field('otzyvy-go') ): ?>
                            <li class="reviews__list-item">
                                <div class="card reviews__card">
                                    <div class="reviews__list-item-pic">
                                        <img src="<?php the_sub_field('otzyvy-go-izobrazhenie'); ?>" alt="Фото"
                                            class="reviews__list-item-pic-img">
                                    </div>
                                    <div class="reviews__content">
                                        <h4 class="reviews__content-title text-2xl">
                                            <?php the_sub_field('otzyvy-go-imya_i_familiya'); ?>
                                        </h4>
                                        <p class="text-base">
                                            <?php the_sub_field('otzyvy-go-text'); ?>
                                        </p>
                                    </div>
                                </div>
                            </li>
                        <?php endwhile; ?>

                    <?php endif; ?>
            </ul>
            <a class="btn btn-primary reviews__btn" href="<?php the_field('ssylka_na_vse_otzyvy-go');?>">Смотреть все отзывы в Google</a>
        </div>
    </div>
</section>


<?php get_footer();