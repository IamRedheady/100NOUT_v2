<?php /* Template Name: Статья блога */ ?>
<?php get_header();

$fields = get_field('blog_post_fields');

?>
    
    <div class="layout">
        <div class="breadcrumbs text-sm">
            <a href="<?= home_url(); ?>" class="link link-primary">Главная</a>
            <span class="breadcrumbs__separator">/</span>
            <a href="<?= get_permalink(wp_get_post_parent_id(get_the_ID())); ?>" class="link link-primary">Блог</a>
            <span class="breadcrumbs__separator">/</span>
            <span class="breadcrumbs__current"><?php the_title(); ?></span>
        </div>
    </div>

    <div class="blog-post">
        <div class="layout ur__text">
            <h1 class="blog-post__title text-5xl"><?= $fields['title']; ?></h1>
            <div class="blog-post__info">
                <div class="blog-post__date text-sm">
                    <?php the_date('d.m.Y'); ?>
                </div>
                <?php if (isset($fields['tags']) && count($fields['tags'])): ?>
                    <div class="blog-post__tags">
                        <?php foreach ($fields['tags'] as $tag): ?>
                        <a
                                href="<?= $tag['link']; ?>"
                                class="blog-post__tags-item-btn btn btn-secondary"
                        >
                            <?= $tag['title']; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="blog-post__content">
                <?= $fields['content']; ?>
            </div>
        </div>
    </div>
    <?php
        // Get random posts
        $args = array(
            'post_type' => 'page',
            'posts_per_page' => 4,
            'orderby' => 'rand',
            'meta_key' => '_wp_page_template',
            'meta_value' => 'templates/blog-post.php',
            'post__not_in' => array(get_the_ID())
        );
        $related_posts = get_posts($args);

        if($related_posts): ?>
            <div class="layout">
                <div class="related-posts">
                    <h2 class="related-posts__title text-3xl">Похожие статьи</h2>
                    <div class="post-catalog">
                    <?php foreach($related_posts as $post):
                        setup_postdata($post);
                        $card_fields = get_field('blog_post_fields');
                        
                        if ($card_fields['hide']) {
                            continue;
                        }
                    ?>
                        <div class="post-catalog__item">
                            <a href="<?= the_permalink($post); ?>" class="post-catalog__item-img">
                                <img
                                    src="<?= $card_fields['card_image']['sizes']['large']; ?>"
                                    alt="<?= $card_fields['card_title']; ?>"
                                />
                            </a>
                            <div class="post-catalog__item-info">
                                <a href="<?= the_permalink($post); ?>" class="post-catalog__item-title text-md">
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
                                <div class="post-catalog__item-excerpt text-sm">
                                    <?= $card_fields['card_exceprt']; ?>
                                </div>
                                <div class="post-catalog__item-btn">
                                    <a href="<?= the_permalink($post); ?>" class="btn btn_small btn-primary">Читать далее</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>



</div>
<?php get_footer();
