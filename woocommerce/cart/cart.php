<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined('ABSPATH') || exit;


$paymentType = WC()->session->get('custom_payment');
/* if ($paymentType == 3) {
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];

        // Отключаем sale price
        if ($product->is_on_sale()) {
            if ($payment_method === $specific_payment_method) {
                $product->set_price($product->get_regular_price());
            }
        }
        $products_data[] = array(
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'price' => $product->get_price(),
            // Добавьте другие необходимые поля товара
        );
    }

    WC()->cart->calculate_totals();
} */
do_action('woocommerce_before_cart'); ?>
<style>
    .ordering__cart-item--disabled {
        display: none;
    }

    /* .ordering__cart-item:has(+ .ordering__cart-item--disabled) {
        border: none;
        padding-bottom: 0;
        margin-bottom: 0;
    } */
</style>
<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    <!-- <?php do_action('woocommerce_before_cart_table'); ?> -->

    <div class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
        <?php do_action('woocommerce_before_cart_contents'); ?>
        <ul class="ordering__cart">
            <?php

            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                $p_image_url = wp_get_attachment_url($_product->get_image_id());
                $link = $_product->get_permalink($cart_item);

                $s_args_usl = array(
                    'status' => 'publish',
                    'limit' => 2000,
                    'tax_query' => array(array(
                        'taxonomy' => 'product_cat',
                        'field' => 'id',
                        'terms' => array(2992),
                        'operator' => 'IN',
                    )),
                );

                $s_usl_products = wc_get_products($s_args_usl);

                $checkDsoP = false;

                foreach ($s_usl_products as $s_product) {
                    if ($s_product->get_id() == $product_id) {
                        $checkDsoP = true;
                    }
                }

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
            ?>
                    <li class="<?php
                                if ($checkDsoP) {
                                    echo 'ordering__cart-item--disabled';
                                } else {
                                    echo 'ordering__cart-item';
                                }
                                ?> js-order-cart-item" data-product_id="<?php echo $_product->get_id(); ?>">
                        <?php
                        if ($_product->is_sold_individually()) {
                            $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                        } else {
                            $product_quantity = woocommerce_quantity_input(
                                array(
                                    'input_name'   => "cart[{$cart_item_key}][qty]",
                                    'input_value'  => $cart_item['quantity'],
                                    'max_value'    => $_product->get_max_purchase_quantity(),
                                    'min_value'    => '0',
                                    'product_name' => $_product->get_name(),
                                ),
                                $_product,
                                false
                            );
                        }

                        echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
                        ?>
                        <a href="<?php echo $link; ?>" class="ordering__cart-pic">
                            <img src="<?php if ($p_image_url) {
                                            echo $p_image_url;
                                        } else {
                                            echo "https://100nout.by/wp-content/uploads/2023/03/zagl.png";
                                        } ?>" alt="<?php echo $_product->get_name(); ?>" class="ordering__cart-pic-img">
                        </a>
                        <div class="ordering__cart-content">
                            <div class="ordering__cart-content-center">
                                <a href="<?php echo $link; ?>" class="ordering__cart-title text text-lg text--semibold">
                                    <?php
                                    $productNameSost = "";
                                    if ($_product->get_attribute('sostoyanie') == "новый") {
                                        $productNameSost = "Новый " . $_product->get_name() . "";
                                    } else if ($_product->get_attribute('sostoyanie') == "5") {
                                        $productNameSost = "" . $_product->get_name() . ", отличное состояние";
                                    } else {
                                        $productNameSost = "Б/у " . $_product->get_name() . "";
                                    }
                                    echo $productNameSost;
                                    ?>
                                </a>
                                <p class="ordering__cart-sku text text-sm">
                                    Код товара: <span class="ordering__cart-sku-num"><?php echo $_product->get_sku(); ?></span>
                                </p>
                                <?php
                                if (get_field('garantii', 'option')) : ?>
                                    <?php while (has_sub_field('garantii', 'option')) : ?>

                                        <?php

                                        if ($_product->get_price() > get_sub_field('minimalnaya_czena', 'option') and $_product->get_price() <= get_sub_field('maksimalnaya_czena', 'option')) {
                                        ?>
                                            <div class="ordering__cart-dropdown">
                                                <?php if ($paymentType == 3) { ?>
                                                    <div class="dropdown__btn">
                                                        <span class="text-base text js-dropdown-value">Бесплатная гарантия на 12 месяцев для юридических лиц</span>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="dropdown js-dropdown">
                                                        <button type="button" class="dropdown__btn">
                                                            <span class="text-base text js-dropdown-value">Гарантия 30 дней</span>
                                                            <svg class="dropdown__arrow" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 7.29289C5.68342 6.90237 6.31658 6.90237 6.70711 7.29289L10 10.5858L13.2929 7.29289C13.6834 6.90237 14.3166 6.90237 14.7071 7.29289C15.0976 7.68342 15.0976 8.31658 14.7071 8.70711L10.7071 12.7071C10.3166 13.0976 9.68342 13.0976 9.29289 12.7071L5.29289 8.70711C4.90237 8.31658 4.90237 7.68342 5.29289 7.29289Z" fill="#B8B8B8" />
                                                            </svg>
                                                        </button>
                                                        <ul class="dropdown__list">
                                                            <li class="dropdown__list-item">
                                                                <button type="button" class="dropdown__list-item-btn text-sm button wp-element-button product_type_simple" data-free-dso="1" data-parent-product_id="<?php echo $_product->get_id(); ?>">
                                                                    Гарантия 30 дней
                                                                </button>
                                                            </li>
                                                            <?php if (get_sub_field('garantii_r', 'option')) : ?>
                                                                <?php while (has_sub_field('garantii_r', 'option')) : ?>
                                                                    <?php
                                                                    $s_product_id = get_sub_field('tovar-garantii_r', 'option');

                                                                    $s_product = wc_get_product($s_product_id);
                                                                    ?>
                                                                    <li class="dropdown__list-item">
                                                                        <a data-product_id="<?php echo $s_product_id; ?>" data-parent-product_id="<?php echo $_product->get_id(); ?>" data-product_sku="<?php echo $s_product->get_sku(); ?>" data-quantity="1" href="?add-to-cart=<?php echo $s_product_id; ?>" class="dropdown__list-item-btn text-sm button wp-element-button product_type_simple add_to_cart_button ajax_add_to_cart js-cart-dso">
                                                                            <?php
                                                                            the_sub_field('nazvanie_garantii_r', 'option');
                                                                            echo ': +';
                                                                            echo $s_product->get_price();
                                                                            ?> BYN
                                                                        </a>
                                                                    </li>
                                                                <?php endwhile; ?>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
                                                <?php } ?>
                                                <div class="btn btn-icon btn-tooltip-white" name="Доп гарантия">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16 8C16 12.4183 12.4183 16 8 16C3.58172 16 0 12.4183 0 8C0 3.58172 3.58172 0 8 0C12.4183 0 16 3.58172 16 8ZM8.00001 5C7.63113 5 7.3076 5.19922 7.13318 5.50073C6.85664 5.97879 6.24491 6.14215 5.76685 5.86561C5.28879 5.58906 5.12543 4.97733 5.40197 4.49927C5.91918 3.60518 6.88833 3 8.00001 3C9.65687 3 11 4.34315 11 6C11 7.30622 10.1652 8.41746 9.00001 8.82929V9C9.00001 9.55228 8.5523 10 8.00001 10C7.44773 10 7.00001 9.55228 7.00001 9V8C7.00001 7.44772 7.44773 7 8.00001 7C8.5523 7 9.00001 6.55228 9.00001 6C9.00001 5.44772 8.5523 5 8.00001 5ZM8 13C8.55228 13 9 12.5523 9 12C9 11.4477 8.55228 11 8 11C7.44772 11 7 11.4477 7 12C7 12.5523 7.44772 13 8 13Z" fill="#474747" />
                                                    </svg>
                                                    <div class="btn-tooltip-white-body">
                                                        <p class="text text-sm">Дополнительная гарантия от нашего сервиса!</p><a href="/dso" class="text-sm link link-primary">Подробнее</a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }

                                        ?>

                                        <!-- <div>
                                    <?php if (get_sub_field('child_repeater')) : ?>
                                        <ul>
                                        <?php while (has_sub_field('child_repeater')) : ?>
                                            <li><?php the_sub_field('item'); ?></li>
                                        <?php endwhile; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>	 -->

                                    <?php endwhile; ?>
                                <?php endif;
                                ?>
                            </div>
                            <div class="ordering__cart-content-actions">
                                <div class="ordering__cart-price">
                                    <p class="product__price">
                                        <?php echo number_format($_product->get_price(), 0, '', ' '); ?>
                                        <span>BYN</span>
                                    </p>
                                    <?php
                                    if ($_product->get_regular_price() != $_product->get_price()) {
                                    ?>
                                        <p class="product__price-old">
                                            <?php echo number_format($_product->get_regular_price(), 0, '', ' '); ?>
                                        </p>
                                    <?php } ?>
                                </div>
                                <div class="ordering__cart-btns">
                                    <?php echo do_shortcode('[ti_wishlists_addtowishlist]'); ?>
                                    <?php
                                    echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        'woocommerce_cart_item_remove_link',
                                        sprintf(
                                            '<a href="%s" class="remove remove_from_cart_button btn btn-icon" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" onclick="onCartClose()">
                                                <svg width="18" height="20" viewBox="0 0 18 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M16 5L15.1327 17.1425C15.0579 18.1891 14.187 19 13.1378 19H4.86224C3.81296 19 2.94208 18.1891 2.86732 17.1425L2 5M7 9V15M11 9V15M12 5V2C12 1.44772 11.5523 1 11 1H7C6.44772 1 6 1.44772 6 2V5M1 5H17"
                                                    stroke="#21201F" stroke-width="1.2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                                </a>',
                                            esc_url(wc_get_cart_remove_url($cart_item_key)),
                                            esc_attr__('Remove this item', 'woocommerce'),
                                            esc_attr($product_id),
                                            esc_attr($cart_item_key),
                                            esc_attr($_product->get_sku())
                                        ),
                                        $cart_item_key
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </li>
            <?php
                }
            }
            ?>
        </ul>
        <script>
            dropdowns = document.querySelectorAll(".js-dropdown")

            if (dropdowns) {
                dropdowns.forEach(dropdown => {
                    const btn = dropdown.children[0]
                    const menu = dropdown.children[1]
                    const menuItems = Array.from(menu.children)

                    btn.addEventListener("click", () => {
                        dropdown.classList.toggle("active")
                    })

                    menuItems.forEach((item, index) => {
                        const listBtn = item.children[0]

                        listBtn.addEventListener("click", () => {
                            const sortFilters = document.querySelectorAll("[data-filter-type='wpfSortBy'] input")

                            if (sortFilters.length !== 0) {
                                sortFilters[index].click()
                            }
                            btn.children[0].innerHTML = listBtn.innerHTML
                            dropdown.classList.toggle("active")
                        })
                        if (document.querySelectorAll("[data-filter-type='wpfSortBy'] input").length !== 0) {
                            if (document.querySelectorAll("[data-filter-type='wpfSortBy'] input")[index].checked) {
                                btn.children[0].innerHTML = listBtn.innerHTML
                            }
                        }
                    })
                })
            }

            document.querySelectorAll(".js-order-cart-item").forEach(item => {
                const parentSelector = `.js-order-cart-item[data-product_id="${item.dataset.product_id}"]`;
                let currentDSO = "";

                document.querySelectorAll(`${parentSelector} .js-cart-dso`).forEach((element, elIndex) => {
                    if (localStorage.getItem(element.getAttribute('data-parent-product_id')) === element.getAttribute('data-product_id')) {
                        currentDSO = element.getAttribute('data-product_id');
                        const currentSelector = document.querySelector(`${parentSelector} .dropdown__btn .text-base`)
                        currentSelector.innerHTML = element.innerHTML
                        element.style.pointerEvents = "none";
                    }
                });

                document.querySelectorAll(`${parentSelector} .remove_from_cart_button`).forEach(btn => {
                    btn.addEventListener("click", () => {
                        localStorage.setItem(btn.getAttribute("data-product_id"), "")
                        localStorage.setItem("deleteDSO", currentDSO)
                    })
                });

                document.querySelectorAll(`${parentSelector} .dropdown__list-item-btn`).forEach((element, elIndex) => {
                    if (localStorage.getItem(element.getAttribute('data-parent-product_id')) !== element.getAttribute('data-product_id')) {
                        element.addEventListener("click", () => {
                            localStorage.setItem("deleteDSO", currentDSO)
                        })
                    }

                    if (element.getAttribute('data-free-dso')) {
                        element.addEventListener("click", () => {
                            localStorage.setItem(element.getAttribute('data-parent-product_id'), "")
                            localStorage.setItem("deleteDSO", currentDSO)
                            document.querySelector("button[name='update_cart']").click()
                        })
                    }
                });
            });


            if (document.querySelector(`.js-order-cart-item[data-product_id="${localStorage.getItem('deleteDSO')}"] .qty`) !== null) {
                document.querySelector(`.js-order-cart-item[data-product_id="${localStorage.getItem('deleteDSO')}"] .qty`).value = Number(document.querySelector(`.js-order-cart-item[data-product_id="${localStorage.getItem('deleteDSO')}"] .qty`).value) - 1

                localStorage.setItem("deleteDSO", "")
                localStorage.setItem("deleteDSO", "")

                document.querySelector("button[name='update_cart']").click()
            }
        </script>
        <?php do_action('woocommerce_cart_contents'); ?>



        <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>

        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>

        <?php do_action('woocommerce_after_cart_contents'); ?>
    </div>
    <?php do_action('woocommerce_after_cart_table'); ?>
</form>

<?php do_action('woocommerce_before_cart_collaterals'); ?>

<?php do_action('woocommerce_after_cart'); ?>