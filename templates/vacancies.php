<?php /* Template Name: Вакансии */ ?>
<?php get_header();?>

<section class="jobs layout">
                <h1 class="jobs__title text-5xl">Вакансии</h1>
                <ul class="jobs__list">
                <?php if( get_field('vakansii') ): ?>
                    <?php while( has_sub_field('vakansii') ): ?>
                        <li class="jobs__list-item">
                        <div class="card">
                            <h2 class="card__title text-4xl">
                                <?php the_sub_field('nazvanie_vakansii'); ?>
                            </h2>
                            <p class="card__text text text-lg jobs__list-terms">
                                Заработная плата: <?php the_sub_field('zp_vakansii'); ?>
                            </p>
                            <p class="card__text text text-lg jobs__list-terms">
                                Занятость: <?php the_sub_field('zan_vakansii'); ?>
                            </p>
                            <?php if( get_sub_field('treb_vakansii') ): ?>
                                <h4 class="card-title text-2xl jobs__list-title">
                                    Требования
                                </h4>
                                <ul class="card__list card__list--column card__list--markers-disc">
                                <?php while( has_sub_field('treb_vakansii') ): ?>
                                    <li class="card__list-item jobs__text">
                                        <p class="text-lg">
                                            <?php the_sub_field('treb_vakansii_punkt'); ?>
                                        </p>
                                    </li>
                                <?php endwhile; ?>
                                </ul>
                            <?php endif; ?>
                            <?php if( get_sub_field('ob_vakansii') ): ?>
                                <h4 class="card-title text-2xl jobs__list-title">
                                    Обязанности
                                </h4>
                                <ul class="card__list card__list--column card__list--markers-disc">
                                <?php while( has_sub_field('ob_vakansii') ): ?>
                                    <li class="card__list-item jobs__text">
                                        <p class="text-lg">
                                            <?php the_sub_field('ob_vakansii_punkt'); ?>
                                        </p>
                                    </li>
                                <?php endwhile; ?>
                                </ul>
                            <?php endif; ?>
                            <?php if( get_sub_field('usl_vakansii') ): ?>
                                <h4 class="card-title text-2xl jobs__list-title">
                                    Условия
                                </h4>
                                <ul class="card__list card__list--column card__list--markers-disc">
                                <?php while( has_sub_field('usl_vakansii') ): ?>
                                    <li class="card__list-item jobs__text">
                                        <p class="text-lg">
                                            <?php the_sub_field('usl_vakansii_punkt'); ?>
                                        </p>
                                    </li>
                                <?php endwhile; ?>
                                </ul>
                            <?php endif; ?>
                            <h4 class="card-title text-2xl">
                                Если вас заинтересовала вакансия, свяжитесь с нами:
                                <a class="link link-secondary text-2xl" href="tel:+375259269777">+375 (25) 926–97–77</a>
                            </h4>
                        </div>
                    </li>
                    <?php endwhile; ?>
                <?php endif; ?>
                </ul>
            </section>

<?php get_footer();