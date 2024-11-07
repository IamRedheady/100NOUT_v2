<?php /* Template Name: Блог */ ?>
<?php get_header(); ?>
    <div class="blog">
        <div class="layout">
            <h1 class="text-5xl blog__title"><?= the_title(); ?></h1>
            <?php
                $args = [
                    'post_type' => 'page',
                    'fields' => 'ids',
                    'nopaging' => true,
                    'meta_key' => '_wp_page_template',
                    'meta_value' => 'templates/blog-post.php'
                ];
                $pages = get_posts($args);
            ?>
                <?php if (count($pages)): ?>
                    <div class="post-catalog">
                    <?php foreach ($pages as $page_item):
                            $card_fields = get_field('card', $page_item);

                            if ($card_fields['hide']) {
                                continue;
                            }
                        ?>
                        <div class="post-catalog__item">
                            <a href="<?= the_permalink($page_item) ?>" class="post-catalog__item-img">
                                <img src="images/slide1.jpg" alt="Фото">
                            </a>
                            <div class="post-catalog__item-info">
                                <a href="<?= the_permalink($page_item) ?>" class="post-catalog__item-title text-2xl">
                                    Заголовок
                                </a>
                                <div class="post-catalog__item-date text-sm">
                                    12.12.2024
                                </div>
                                <div class="post-catalog__item-excerpt ur__text text-md">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                                </div>
                                <div class="post-catalog__item-btn">
                                    <a href="<?= the_permalink($page_item) ?>" class="btn btn_small btn-primary">Читать далее</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
        </div>
    </div>

    
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
