<?php /* Template Name: Оформление заказа ТЕСТ */ ?>
<?php get_header(); ?>

<div class="p-check-cart 
<?php
global $woocommerce;
$paymentType = WC()->session->get('custom_payment');
if (!$paymentType) {
    $paymentType = 0;
}
$c_total = $woocommerce->cart->subtotal;
if ($c_total == 0) {
    echo 'p-cart-empty';
} ?>">

<?php 
    // Функция для удаления товара и его изображений
    function delete_product_and_images($product_id) {
        // Получаем все изображения товара
        $product = wc_get_product($product_id);
        $attachment_ids = $product->get_gallery_image_ids();
    
        // Удаляем каждое изображение
        foreach ($attachment_ids as $attachment_id) {
            wp_delete_attachment($attachment_id, true);
        }
    
        // Удаляем главное изображение товара
        $featured_image_id = get_post_thumbnail_id($product_id);
        if ($featured_image_id) {
            wp_delete_attachment($featured_image_id, true);
        }
    
        // Удаляем товар
        wp_delete_post($product_id, true);
    }
    
    // Функция для проверки и удаления старых товаров
    function remove_old_products() {
        // Задаем категории
        $categories = array('noutbuki', 'smartfony');
        
        // Задаем дату для сравнения (3 месяца назад)
        $date_compare = date('Y-m-d H:i:s', strtotime('-3 months'));
    
        // WP_Query для получения всех товаров из заданных категорий
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'draft',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $categories,
                    'include_children' => true
                ),
            ),
            'date_query' => array(
                array(
                    'column' => 'post_modified_gmt',
                    'before' => $date_compare,
                ),
            ),
            'fields' => 'ids',
        );
    
        $query = new WP_Query($args);
        
        print_r(count($query->posts));
        // Проходим по каждому товару и удаляем его, если он не изменялся более 3 месяцев
        if ($query->have_posts()) {
            foreach ($query->posts as $product_id) {
                // delete_product_and_images($product_id);
                print_r("");
            }
        }
    }
    
    // Привязываем функцию к хуку WordPress, чтобы запускать скрипт через админку или по крону
    // add_action('init', 'remove_old_products');
?>
</div>
<section class="ordering layout">
    <a href="/" class="btn btn_icon-left text text-base link link-secondary ordering__back">
        <svg width="18" height="8" viewBox="0 0 18 8" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4.83333 7.33317L1.5 3.99984M1.5 3.99984L4.83333 0.666504M1.5 3.99984L16.5 3.99984" stroke="#8F8F8F" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Вернуться в каталог
    </a>
    <div class="ordering__top">
        <h1 class="ordering__title text-5xl">
            Корзина
        </h1>
        <?php 
            //  remove_old_products();
        ?>
        <form class="clear-cart" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post" onsubmit="javascript:localStorage.clear();">
            <button type="submit" onclick='javascript:if(!confirm("Удалить все товары из корзины?")) {localStorage.clear(); return false;}' class="ordering__clear btn btn-secondary btn_icon-left" name="clear-cart"><svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.832 4.83333L13.1093 14.9521C13.047 15.8243 12.3212 16.5 11.4468 16.5H4.55056C3.67616 16.5 2.95043 15.8243 2.88813 14.9521L2.16536 4.83333M6.33203 8.16667V13.1667M9.66536 8.16667V13.1667M10.4987 4.83333V2.33333C10.4987 1.8731 10.1256 1.5 9.66536 1.5H6.33203C5.87179 1.5 5.4987 1.8731 5.4987 2.33333V4.83333M1.33203 4.83333H14.6654" stroke="#474747" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                </svg> Очистить корзину</button>
        </form>
    </div>
    <div class="ordering__body">
        <div class="ordering__content">
            <div class="ordering__card php-cart">
                <h2 class="ordering__card-title text-2xl">
                    Товары в заказе
                </h2>
                <?php echo do_shortcode('[woocommerce_cart]') ?>
            </div>
            <div class="ordering__subtotal">
                <p class="ordering__subtotal-name text-2xl">
                    Итого
                </p>
                <div class="ordering__subtotal-content">
                    <p class="ordering__subtotal-row">
                        <span class="text text-lg">Товары:</span>
                        <span class="text text-lg text--semibold"><?php echo number_format(WC()->cart->subtotal - nout_get_dso_cost(), 0, '', ' '); ?> BYN</span>
                    </p>
                    <?php
                    if (nout_get_dso_cost() !== 0) {
                    ?>
                        <p class="ordering__subtotal-row">
                            <span class="text text-lg">Доп. гарантия:</span>
                            <span class="text text-lg text--semibold"><?php echo nout_get_dso_cost(); ?> BYN</span>
                        </p>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="ordering__card">
                <div class="ordering__card-inner">
                    <h2 class="ordering__card-title text-2xl">
                        Выберите способ получения
                    </h2>
                    <a href="/dostavka/" class="ordering__card-link btn btn_icon-right link link-secondary">
                        Подробнее о способах доставки
                        <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.912046 13.4224C0.586609 13.097 0.586609 12.5694 0.912046 12.2439L6.15612 6.99984L0.912046 1.75576C0.586609 1.43032 0.586609 0.902685 0.912046 0.577249C1.23748 0.251811 1.76512 0.251811 2.09056 0.577249L7.92389 6.41058C8.24933 6.73602 8.24933 7.26366 7.92389 7.58909L2.09056 13.4224C1.76512 13.7479 1.23748 13.7479 0.912046 13.4224Z" fill="#8F8F8F" />
                        </svg>

                    </a>
                </div>
                <div class="ordering__grid">
                    <label class="ordering__label">
                        <input value="1" type="radio" name="orderDelivery">
                        <span class="ordering__label-circle"></span>
                        <span class="ordering__label-content">
                            <span class="ordering__label-title text text-base">
                                Самовывоз
                            </span>
                            <span class="ordering__label-subtitle text text-xs">
                                Минск – сегодня, до 20:00
                            </span>
                        </span>
                    </label>
                    <label class="ordering__label">
                        <input value="0" type="radio" name="orderDelivery">
                        <span class="ordering__label-circle"></span>
                        <span class="ordering__label-content">
                            <span class="ordering__label-title text text-base">
                                Доставка по Минску: 10 BYN
                            </span>
                            <span class="ordering__label-subtitle text text-xs">
                                <?php
                                $todayH = date("H") + 3;
                                if ($todayH < 16) {
                                ?>
                                    Сегодня, с 18:00 до 21:00
                                <?php
                                } else {
                                ?>
                                    завтра, до 21:00
                                <?php
                                }
                                ?> (Возможна экспресс-доставка)
                            </span>
                        </span>
                    </label>
                    <label class="ordering__label">
                        <input value="2" type="radio" name="orderDelivery">
                        <span class="ordering__label-circle"></span>
                        <span class="ordering__label-content">
                            <span class="ordering__label-title text text-base">
                                Доставка по Беларуси: 25 BYN
                            </span>
                            <span class="ordering__label-subtitle text text-xs">
                                <?php
                                $arr = [
                                    'января',
                                    'февраля',
                                    'марта',
                                    'апреля',
                                    'мая',
                                    'июня',
                                    'июля',
                                    'августа',
                                    'сентября',
                                    'октября',
                                    'ноября',
                                    'декабря'
                                ];

                                $d = strtotime("+2 day");
                                $todayM = date("n", $d);
                                $nextDay = date("d", $d);
                                ?>
                                Курьером до <?php
                                            echo $nextDay;
                                            echo " ";
                                            echo $arr[$todayM - 1]; ?>
                            </span>
                        </span>
                    </label>
                    <label class="ordering__label">
                        <input value="3" type="radio" name="orderDelivery">
                        <span class="ordering__label-circle"></span>
                        <span class="ordering__label-content">
                            <span class="ordering__label-title text text-base">
                                Почтой по Беларуси: до 20 BYN
                            </span>
                            <span class="ordering__label-subtitle text text-xs">
                                Цена и срок зависит от выбранной почтовой службы
                            </span>
                        </span>
                    </label>
                </div>
                <div class="ordering__container js-address-container">
                    <div class="ordering__card-inner">
                        <h2 class="ordering__card-title text-2xl">
                            Адрес доставки
                        </h2>
                        <label class="ordering__label-express js-express">
                            <input type="checkbox" name="express">
                            <span class="ordering__label-express-circle">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_5658_10415)">
                                        <rect width="20" height="20" rx="6" fill="#69C856" />
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0611 5.42238C15.5183 5.73229 15.6376 6.35411 15.3277 6.81124L10.2432 14.3112C10.0771 14.5562 9.8111 14.715 9.51671 14.745C9.22232 14.7749 8.92977 14.673 8.71777 14.4665L4.80234 10.6536C4.40667 10.2683 4.39827 9.6352 4.78358 9.23953C5.16888 8.84386 5.80199 8.83546 6.19766 9.22077L9.25771 12.2007L13.6723 5.68895C13.9822 5.23182 14.604 5.11247 15.0611 5.42238Z" fill="white" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_5658_10415">
                                            <rect width="20" height="20" rx="6" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <span class="text text-base">Хочу экспресс-доставку в течение 2х часов: 20
                                BYN</span>
                        </label>
                    </div>
                    <div class="ordering__flex ordering__flex--address">
                        <div class="ordering__input-wrapper">
                            <label class="ordering__label-text required">
                                <input class="ordering__input js-address-input" type="text" name="address" placeholder="Улица, номер дома">
                            </label>
                            <div class="ordering__input-results js-address-result"></div>
                        </div>
                        <div class="ordering__input-wrapper">
                            <input class="ordering__input" type="text" name="address" placeholder="Подъезд">
                        </div>
                        <div class="ordering__input-wrapper">
                            <input class="ordering__input" type="text" name="address" placeholder="Этаж">
                        </div>
                        <div class="ordering__input-wrapper">
                            <label class="ordering__label-text js-last-val">
                                <input class="ordering__input" type="text" name="address" placeholder="Квартира">
                            </label>
                        </div>
                        <div class="ordering__input-wrapper">
                            <label class="ordering__label-text js-last-val">
                                <input class="ordering__input" type="text" name="address" placeholder="Комментарий курьеру">
                            </label>
                        </div>
                    </div>
                </div>
                <div class="ordering__container js-map-container hide">
                    <div class="ordering__container-map map">
                        <h2 class="map__title  ordering__container-map-title text-2xl">
                            Как к нам добраться
                        </h2>
                        <div class="map__body ordering__container-map-body">
                            <div class="map__frame">
                                <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A31b2aac7a7f8d32a102e30f0c3b09e68125073b9717ea50e6307d314fbb80737&amp;width=100%25&amp;height=100%&amp;lang=ru_RU&amp;scroll=false"></script>
                            </div>
                            <div class="map__card card">
                                <h2 class="card__title text-2xl">
                                    Адрес нашего шоурума
                                </h2>
                                <p class="card__text text text-lg">
                                    220114, Минск, метро Московская, Проспект Независимости д. 94
                                </p>
                                <p class="card__text card__text--mt text text-lg text--semibold">
                                    Время работы 10:00 до 20:00
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <h2 class="ordering__card-title text-2xl">
                    Получатель
                </h2>
                <div class="ordering__flex ordering__flex--column">
                    <div class="ordering__input-wrapper">
                        <label class="ordering__label-text required">
                            <input class="ordering__input js-input-name" type="text" name="orderContacts" placeholder="Имя">
                        </label>
                    </div>
                    <div class="ordering__input-wrapper">
                        <label class="ordering__label-text">
                            <input class="ordering__input" type="text" name="orderContacts" placeholder="Фамилия">
                        </label>
                    </div>
                    <div class="ordering__input-wrapper">
                        <label class="ordering__label-text required">
                            <input class="ordering__input js-input-email" type="text" name="orderContacts" placeholder="E-mail">
                        </label>
                    </div>
                    <div class="ordering__input-wrapper">
                        <label class="ordering__label-text required">
                            <input class="ordering__input js-input-tel" type="text" name="orderContacts" placeholder="Номер телефона">
                        </label>
                    </div>
                </div>
            </div>
            <div class="ordering__card">
                <div class="ordering__card-inner">
                    <h2 class="ordering__card-title text-2xl">
                        Способ оплаты
                    </h2>
                    <a href="/oplata/" class="ordering__card-link btn btn_icon-right link link-secondary">
                        Подробнее об оплате
                        <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.912046 13.4224C0.586609 13.097 0.586609 12.5694 0.912046 12.2439L6.15612 6.99984L0.912046 1.75576C0.586609 1.43032 0.586609 0.902685 0.912046 0.577249C1.23748 0.251811 1.76512 0.251811 2.09056 0.577249L7.92389 6.41058C8.24933 6.73602 8.24933 7.26366 7.92389 7.58909L2.09056 13.4224C1.76512 13.7479 1.23748 13.7479 0.912046 13.4224Z" fill="#8F8F8F" />
                        </svg>

                    </a>
                </div>
                <div class="ordering__flex ordering__flex--column">
                    <div class="ordering__input-wrapper">
                        <label class="ordering__label">
                            <input value="0" type="radio" name="orderPayment" <?php echo $paymentType == 0 ? "checked" : "" ?>>
                            <span class="ordering__label-circle"></span>
                            <span class="ordering__label-content">
                                <span class="ordering__label-title text text-base">
                                    Наличными
                                </span>
                                <span class="ordering__label-subtitle text text-xs">
                                    В нашем шоуруме или во время доставки
                                </span>
                            </span>
                        </label>
                    </div>
                    <div class="ordering__input-wrapper">
                        <label class="ordering__label">
                            <input value="1" type="radio" name="orderPayment" <?php echo $paymentType == 1 ? "checked" : "" ?>>
                            <span class="ordering__label-circle"></span>
                            <span class="ordering__label-content">
                                <span class="ordering__label-title text text-base">
                                    Рассрочка
                                </span>
                                <span class="ordering__label-subtitle text text-xs">
                                    Картами рассрочки (Черепаха, Магнит, Халва и д.р.)
                                </span>
                            </span>
                        </label>
                    </div>
                    <div class="ordering__input-wrapper">
                        <label class="ordering__label">
                            <input value="2" type="radio" name="orderPayment" <?php echo $paymentType == 2 ? "checked" : "" ?>>
                            <span class="ordering__label-circle"></span>
                            <span class="ordering__label-content">
                                <span class="ordering__label-title text text-base">
                                    Безналичный расчёт
                                </span>
                                <span class="ordering__label-subtitle text text-xs">
                                    Оплата на расчётный счёт или эквайринг
                                </span>
                            </span>
                        </label>
                    </div>
                    <div class="ordering__input-wrapper">
                        <label class="ordering__label">
                            <input value="3" type="radio" name="orderPayment" <?php echo $paymentType == 3 ? "checked" : "" ?>>
                            <span class="ordering__label-circle"></span>
                            <span class="ordering__label-content">
                                <span class="ordering__label-title text text-base">
                                    Безналичный расчет для юридических лиц
                                </span>
                                <span class="ordering__label-subtitle text text-xs">
                                    *Для юридических лиц акционная цена, к сожалению, не применяется, однако вы получаете годовую гарантию от нашего магазина бесплатно
                                </span>
                            </span>
                        </label>
                        <div class="ordering__flex ordering__flex--column ordering__flex--inner js-company-container hide">
                            <div class="ordering__grid">
                                <label class="ordering__label">
                                    <input value="Индивидуальный предприниматель" type="radio" name="companyType" placeholder="Тип организации" checked>
                                    <span class="ordering__label-circle"></span>
                                    <span class="ordering__label-content">
                                        <span class="ordering__label-title text text-base">
                                            Индивидуальный предприниматель
                                        </span>
                                    </span>
                                </label>
                                <label class="ordering__label">
                                    <input value="Юридическое лицо" type="radio" name="companyType" placeholder="Тип организации">
                                    <span class="ordering__label-circle"></span>
                                    <span class="ordering__label-content">
                                        <span class="ordering__label-title text text-base">
                                            Юридическое лицо
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <div class="ordering__input-wrapper">
                                <label class="ordering__label-text required">
                                    <input class="ordering__input" type="text" name="companyName" placeholder="Название организации">
                                </label>
                            </div>
                            <div class="ordering__input-wrapper">
                                <label class="ordering__label-text required">
                                    <input class="ordering__input" type="text" name="companyYpn" placeholder="УПН">
                                </label>
                            </div>
                            <div class="ordering__input-wrapper">
                                <label class="ordering__label-text required">
                                    <textarea class="ordering__input" type="text" name="companyAdres" placeholder="Юридический адрес, индекст, город, ул, дом, пом\офис"></textarea>
                                </label>
                            </div>
                            <div class="ordering__input-wrapper">
                                <label class="ordering__label-text required">
                                    <input class="ordering__input" type="text" name="companyBank" placeholder="Реквизиты банка">
                                </label>
                            </div>
                            <div class="ordering__input-wrapper">
                                <label class="ordering__label-text required">
                                    <input class="ordering__input" type="text" name="companyBiks" placeholder="бикс">
                                </label>
                            </div>
                            <div class="ordering__input-wrapper">
                                <label class="ordering__label-text required">
                                    <input class="ordering__input" type="text" name="companyBankName" placeholder="Наименование банка">
                                </label>
                            </div>
                            <div class="ordering__input-wrapper">
                                <label class="ordering__label-text required">
                                    <input class="ordering__input" type="text" name="companyBankAccount" placeholder="Расчётный счёт">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <p class="ordering__policy text text-base">
                Нажимая кнопку «Оформить заказ», я соглашаюсь <a href="/dogovor-oferty/" class="link link-primary">
                    с условиями заказа и доставки
                </a>, на обработку
                персональных данных в соответствии с <a href="/politika-konfidenczialnosti/" class="link link-primary">
                    Политикой обработки персональных данных
                </a> и на получение
                звонков в процессе обработки заказа.
            </p>
        </div>
        <div class="ordering__sum">
            <style>
                .woocommerce-form-coupon-toggle {
                    display: none;
                }
            </style>
            <div class="ordering__card">
                <h2 class="ordering__card-title text-2xl">
                    Итого
                </h2>
                <?php the_content(); ?>
                <button type="button" class="btn btn-primary js-order-submit">Оформить заказ</button>
            </div>
        </div>
    </div>
</section>
<div class="not-found-wrap">
    <h1 class="text-5xl layout">Корзина</h1>
    <div class="p-not-found">
        <div class="p-not-found__pic">
            <svg width="230" height="156" viewBox="0 0 230 156" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M197.442 3H32.5734C24.2529 3 17.5078 9.74511 17.5078 18.0656V130.434C17.5078 138.755 24.2529 145.5 32.5734 145.5H197.442C205.763 145.5 212.508 138.755 212.508 130.434V18.0656C212.508 9.74511 205.763 3 197.442 3Z" stroke="#A3A3A3" stroke-width="4.16667" stroke-linejoin="round" />
                <path d="M2.5 153H227.5" stroke="#A3A3A3" stroke-width="4.16667" stroke-miterlimit="10" stroke-linecap="round" />
                <g clip-path="url(#clip0_6104_28987)">
                    <path d="M139.91 110.003C140.185 107.27 139.829 104.514 138.864 101.912C137.898 99.3098 136.346 96.9177 134.304 94.8876C132.262 92.8576 129.776 91.2339 127.003 90.1199C124.231 89.0059 121.232 88.4259 118.199 88.4166C115.165 88.4074 112.162 88.9692 109.381 90.0663C106.601 91.1634 104.102 92.7718 102.046 94.7894C99.9887 96.807 98.4182 99.1896 97.4337 101.786C96.4493 104.382 96.0725 107.135 96.3273 109.87L101.435 109.481C101.24 107.387 101.529 105.279 102.283 103.291C103.036 101.303 104.239 99.4787 105.814 97.934C107.389 96.3892 109.301 95.1577 111.43 94.3177C113.56 93.4777 115.859 93.0475 118.181 93.0546C120.504 93.0617 122.8 93.5058 124.923 94.3588C127.046 95.2117 128.949 96.4549 130.513 98.0092C132.076 99.5635 133.265 101.395 134.004 103.387C134.743 105.38 135.015 107.49 134.805 109.582L139.91 110.003Z" fill="#A3A3A3" />
                    <path d="M67.1213 48.0774C67.0069 49.2284 67.155 50.3885 67.5561 51.4842C67.9572 52.5799 68.6025 53.5871 69.451 54.4418C70.2995 55.2966 71.3328 55.9802 72.485 56.4493C73.6373 56.9183 74.8834 57.1626 76.1441 57.1665C77.4049 57.1703 78.6527 56.9338 79.8084 56.4719C80.9641 56.0099 82.0023 55.3327 82.8571 54.4832C83.7119 53.6337 84.3646 52.6305 84.7737 51.5373C85.1828 50.4442 85.3394 49.2849 85.2335 48.1333L83.1106 48.2973C83.1917 49.1791 83.0718 50.0666 82.7586 50.9036C82.4453 51.7406 81.9456 52.5087 81.2911 53.1591C80.6366 53.8096 79.8417 54.3281 78.9569 54.6818C78.072 55.0355 77.1166 55.2166 76.1513 55.2136C75.186 55.2106 74.2319 55.0236 73.3497 54.6645C72.4674 54.3054 71.6764 53.7819 71.0267 53.1275C70.377 52.473 69.8829 51.7018 69.5758 50.8629C69.2687 50.024 69.1553 49.1358 69.2429 48.2545L67.1213 48.0774Z" fill="#A3A3A3" />
                    <path d="M144.199 48.0774C144.085 49.2284 144.233 50.3885 144.634 51.4842C145.035 52.5799 145.681 53.5871 146.529 54.4418C147.378 55.2966 148.411 55.9802 149.563 56.4493C150.715 56.9183 151.961 57.1626 153.222 57.1665C154.483 57.1703 155.731 56.9338 156.887 56.4719C158.042 56.0099 159.08 55.3327 159.935 54.4832C160.79 53.6337 161.443 52.6305 161.852 51.5373C162.261 50.4442 162.418 49.2849 162.312 48.1333L160.189 48.2973C160.27 49.1791 160.15 50.0666 159.837 50.9036C159.523 51.7406 159.024 52.5087 158.369 53.1591C157.715 53.8096 156.92 54.3281 156.035 54.6818C155.15 55.0355 154.195 55.2166 153.229 55.2136C152.264 55.2106 151.31 55.0236 150.428 54.6645C149.546 54.3054 148.754 53.7819 148.105 53.1275C147.455 52.473 146.961 51.7018 146.654 50.8629C146.347 50.024 146.233 49.1358 146.321 48.2545L144.199 48.0774Z" fill="#A3A3A3" />
                </g>
                <defs>
                    <clipPath id="clip0_6104_28987">
                        <rect width="95.8333" height="64.5833" fill="white" transform="translate(67.0859 46.75)" />
                    </clipPath>
                </defs>
            </svg>
        </div>
        <div class="p-not-found__text text-2xl">
            В вашей корзине <br> пока пусто
        </div>
    </div>
</div>
<style>
    .not-found-wrap {
        display: none;
    }

    .p-cart-empty~.not-found-wrap {
        display: block;
    }

    .p-cart-empty~.ordering {
        display: none;
    }
</style>

<?php get_footer();
