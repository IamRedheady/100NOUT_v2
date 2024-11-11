<?php /* Template Name: Блог */ ?>
<?php get_header();

$fields = get_field('page_fields');

?>
    <div class="blog">
        <div class="layout">
            <h1 class="text-5xl blog__title"><?= $fields['page_title']; ?></h1>

            <?php if (isset($fields['text_before_catalog']) && trim($fields['text_before_catalog'])): ?>
                <div class="blog__content-before">
                    <?= $fields['text_before_catalog']; ?>
                </div>
            <?php endif; ?>
            <?php
                $posts_per_page = $fields['posts_count'];
                $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($current_page - 1) * $posts_per_page;

                $args = [
                    'post_type' => 'page',
                    'fields' => 'ids',
                    'posts_per_page' => $posts_per_page,
                    'offset' => $offset,
                    'meta_key' => '_wp_page_template',
                    'meta_value' => 'templates/blog-post.php',
                    'no_found_rows' => false // Включение подсчета общего количества постов
                ];
                $query = new WP_Query($args);
                $pages = $query->posts; // Список страниц
                // Общее количество страниц для пагинации
                $total_pages = $query->max_num_pages;
            ?>
                <?php if (count($pages)): ?>
                    <div class="post-catalog">
                    <?php foreach ($pages as $page_item):
                            $card_fields = get_field('blog_post_fields', $page_item);

                            if ($card_fields['hide']) {
                                continue;
                            }
                        ?>
                        <div class="post-catalog__item">
                            <a href="<?= the_permalink($page_item) ?>" class="post-catalog__item-img">
                                <img
                                    src="<?= $card_fields['card_image']['sizes']['large']; ?>"
                                    alt="<?= $card_fields['card_title']; ?>"
                                />
                            </a>
                            <div class="post-catalog__item-info">
                                <a href="<?= the_permalink($page_item) ?>" class="post-catalog__item-title text-2xl">
                                    <?= $card_fields['card_title']; ?>
                                </a>
                                <div class="post-catalog__item-date text-sm">
                                    <svg width="52" height="51" viewBox="0 0 52 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M30.8884 48.3327H21.4074C12.4686 48.3327 7.99919 48.3327 5.22224 45.5557C2.44531 42.7789 2.44531 38.3093 2.44531 29.3706V24.63C2.44531 15.6912 2.44531 11.2218 5.22224 8.44489C7.99919 5.66797 12.4686 5.66797 21.4074 5.66797H30.8884C39.8272 5.66797 44.2968 5.66797 47.0735 8.44489C49.8505 11.2218 49.8505 15.6912 49.8505 24.63V29.3706C49.8505 38.3093 49.8505 42.7789 47.0735 45.5557C45.5253 47.1039 43.451 47.7889 40.3695 48.0921" stroke="#8f8f8f" stroke-width="3.45689" stroke-linecap="round"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.2968 0.332031C15.2786 0.332031 16.0745 1.12793 16.0745 2.10973V5.66512C16.0745 6.64691 15.2786 7.44281 14.2968 7.44281C13.315 7.44281 12.5191 6.64691 12.5191 5.66512V2.10973C12.5191 1.12793 13.315 0.332031 14.2968 0.332031Z" fill="#8f8f8f"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M37.9996 0.332031C38.9814 0.332031 39.7773 1.12793 39.7773 2.10973V5.66512C39.7773 6.64691 38.9814 7.44281 37.9996 7.44281C37.0178 7.44281 36.2219 6.64691 36.2219 5.66512V2.10973C36.2219 1.12793 37.0178 0.332031 37.9996 0.332031Z" fill="#8f8f8f"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.667969 17.5165C0.667969 16.5347 1.46387 15.7388 2.44566 15.7388H11.6304C12.6122 15.7388 13.4081 16.5347 13.4081 17.5165C13.4081 18.4983 12.6122 19.2942 11.6304 19.2942H2.44566C1.46387 19.2942 0.667969 18.4983 0.667969 17.5165ZM21.4077 17.5165C21.4077 16.5347 22.2036 15.7388 23.1854 15.7388H48.6657C49.6475 15.7388 50.4434 16.5347 50.4434 17.5165C50.4434 18.4983 49.6475 19.2942 48.6657 19.2942H23.1854C22.2036 19.2942 21.4077 18.4983 21.4077 17.5165Z" fill="#8f8f8f"/>
                                        <path d="M40.3696 36.4785C40.3696 37.7876 39.3085 38.8488 37.9994 38.8488C36.6903 38.8488 35.6291 37.7876 35.6291 36.4785C35.6291 35.1694 36.6903 34.1082 37.9994 34.1082C39.3085 34.1082 40.3696 35.1694 40.3696 36.4785Z" fill="#8f8f8f"/>
                                        <path d="M40.3696 26.9976C40.3696 28.3066 39.3085 29.3678 37.9994 29.3678C36.6903 29.3678 35.6291 28.3066 35.6291 26.9976C35.6291 25.6885 36.6903 24.6273 37.9994 24.6273C39.3085 24.6273 40.3696 25.6885 40.3696 26.9976Z" fill="#8f8f8f"/>
                                        <path d="M28.5185 36.4785C28.5185 37.7876 27.4574 38.8488 26.1483 38.8488C24.8392 38.8488 23.778 37.7876 23.778 36.4785C23.778 35.1694 24.8392 34.1082 26.1483 34.1082C27.4574 34.1082 28.5185 35.1694 28.5185 36.4785Z" fill="#8f8f8f"/>
                                        <path d="M28.5185 26.9976C28.5185 28.3066 27.4574 29.3678 26.1483 29.3678C24.8392 29.3678 23.778 28.3066 23.778 26.9976C23.778 25.6885 24.8392 24.6273 26.1483 24.6273C27.4574 24.6273 28.5185 25.6885 28.5185 26.9976Z" fill="#8f8f8f"/>
                                        <path d="M16.6671 36.4785C16.6671 37.7876 15.6059 38.8488 14.2969 38.8488C12.9878 38.8488 11.9266 37.7876 11.9266 36.4785C11.9266 35.1694 12.9878 34.1082 14.2969 34.1082C15.6059 34.1082 16.6671 35.1694 16.6671 36.4785Z" fill="#8f8f8f"/>
                                        <path d="M16.6671 26.9976C16.6671 28.3066 15.6059 29.3678 14.2969 29.3678C12.9878 29.3678 11.9266 28.3066 11.9266 26.9976C11.9266 25.6885 12.9878 24.6273 14.2969 24.6273C15.6059 24.6273 16.6671 25.6885 16.6671 26.9976Z" fill="#8f8f8f"/>
                                    </svg>
                                    <span>
                                        <?= get_the_date('d-m-Y'); ?>
                                    </span>
                                </div>
                                <div class="post-catalog__item-excerpt text-md">
                                    <?= $card_fields['card_exceprt']; ?>
                                </div>
                                <div class="post-catalog__item-btn">
                                    <a href="<?= the_permalink($page_item) ?>" class="btn btn_small btn-primary">Читать далее</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                <?php
                    if ($total_pages > 1) {
                        echo '<div class="blog-pagination">';

                        // Ссылка на предыдущую страницу, если она существует
                        if ($current_page > 1) {
                            echo '<a href="?page=' . ($current_page - 1) . '">«</a>';
                        }

                        // Лимит на количество отображаемых страниц
                        $visible_pages = 5; // Количество кнопок для отображения
                        $half_visible = floor($visible_pages / 2);

                        // Вычисляем начальную и конечную страницу для отображения
                        $start = max(1, $current_page - $half_visible);
                        $end = min($total_pages, $current_page + $half_visible);

                        // Добавляем кнопки для первой страницы, если текущая не в начале
                        if ($start > 1) {
                            echo '<a href="?page=1">1</a>';
                            if ($start > 2) {
                                echo '<span class="dots">...</span>';
                            }
                        }

                        // Отображаем страницы в заданном диапазоне
                        for ($i = $start; $i <= $end; $i++) {
                            if ($i === $current_page) {
                                echo '<span class="current-page">' . $i . '</span>';
                            } else {
                                echo '<a href="?page=' . $i . '">' . $i . '</a>';
                            }
                        }

                        // Добавляем кнопки для последней страницы, если текущая не в конце
                        if ($end < $total_pages) {
                            if ($end < $total_pages - 1) {
                                echo '<span class="dots">...</span>';
                            }
                            echo '<a href="?page=' . $total_pages . '">' . $total_pages . '</a>';
                        }

                        // Ссылка на следующую страницу, если она существует
                        if ($current_page < $total_pages) {
                            echo '<a href="?page=' . ($current_page + 1) . '">»</a>';
                        }

                        echo '</div>';
                    }
                ?>
                <?php endif; ?>
        </div>
    </div>

</div>
<?php get_footer();
