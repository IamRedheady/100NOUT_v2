<?php /* Template Name: Избранное */ ?>
<?php get_header();?>
<section class="wishlist">
<?php echo do_shortcode('[ti_wishlistsview]');?>
</section>
<section class="layout hide-s">
                    <h1 class="hide-s text-5xl" style="display: none;">
                        Подписывайтесь на наш телеграм
                    </h1>
                    <a href="https://t.me/stonoutby" class="hide-s__banner banner">
                        <img class="banner-xl" src="<?php nout_image_directory();?>tgb2/xl1.png" alt="Баннер">
                        <img class="banner-l" src="<?php nout_image_directory();?>tgb2/l1.png" alt="Баннер">
                        <img class="banner-m" src="<?php nout_image_directory();?>tgb2/m1.png" alt="Баннер">
                        <img class="banner-s" src="<?php nout_image_directory();?>tgb2/s1.png" alt="Баннер">
                        <img class="banner-xs" src="<?php nout_image_directory();?>tgb2/xs1.png" alt="Баннер">
                    </a>
                </section>

<?php get_footer();
