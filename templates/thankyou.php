<?php /* Template Name: Спасибо за заказ */ ?>
<?php get_header(); 

$url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$key = str_replace("https://100nout.by/thank-you/?order_id=", "", $url);

$order_id = wc_get_order_id_by_order_key( $key );
$order = wc_get_order( $order_id );
$order_items = $order->get_items();
$data = $order->get_data();
?>

<section class="layout thanks">
    <div class="banner">
        <img class="banner-xl" src="<?php nout_image_directory();?>thanks/xl1.png" alt="Баннер">
        <img class="banner-l" src="<?php nout_image_directory();?>thanks/l1.png" alt="Баннер">
        <img class="banner-m" src="<?php nout_image_directory();?>thanks/m1.png" alt="Баннер">
        <img class="banner-s" src="<?php nout_image_directory();?>thanks/s1.png" alt="Баннер">
        <img class="banner-xs" src="<?php nout_image_directory();?>thanks/xs1.png" alt="Баннер">
    </div>
    <div class="order">
        <h3 class="text-3xl order__title">Номер заказа: <?php echo $order->get_id();?></h3>
        <hr class="order__line">
        <h3 class="text-3xl order__subtitle">Ваш заказ:</h3>
        <ul class="order__list">
            <?php
                $s_args_usl = array(
                    'status' => 'publish',
                    'limit' => 2000,
                    'tax_query' => array( array(
                        'taxonomy' => 'product_cat',
                        'field' => 'id',
                        'terms' => array( 2992 ),
                        'operator' => 'IN',
                    ) ),
                );
    
                $s_usl_products = wc_get_products($s_args_usl);

                foreach( $order_items as $item_id => $item ){
                    $wc_product = $item->get_product();

                    $checkDsoP = false;
                
                    foreach ($s_usl_products as $s_product) {
                        if ($s_product->get_id() == $wc_product->get_id()) {
                            $checkDsoP = true;
                        }
                    }

                    $p_image_url = wp_get_attachment_url( $wc_product->get_image_id());
                    if ($checkDsoP == false) {
                    ?>
                    <li class="order__list-item">
                            <div class="order__list-content">
                                <div class="order__list-content-pic">
                                    <img class="order__list-content-pic-img" src="<?php if ($p_image_url) { echo $p_image_url;} else {echo "https://100nout.by/wp-content/uploads/2023/03/zagl.png";}?>" alt="фото">
                                </div>
                                <div class="order__list-content-text">
                                    <p class="order__list-content-text-lg text-lg">
                                        <?php echo $item->get_name();?>
                                    </p>
                                    <div class="order__list-content-text-sm">
                                        <p class="text-sm order__list-content-text-sm-code">
                                            Код товара:
                                        </p>
                                        <p class="text-sm">
                                            <?php echo $wc_product->get_sku();?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top: 10px;" class="order__list-content-guarantee" data-product_id="<?php echo $wc_product->get_id();?>">
                                <p data-sku="<?php echo $wc_product->get_sku();?>" class="text-lg order__list-content-guarantee-text">
                                    Бесплатная гарантия на 30 дней
                                </p>
                            </div>
                        </li>
                    <?php
                    }
                }
            ?>
        </ul>
        <hr class="order__line">
        <h3 class="text-3xl order__list-subtitle">Доставка:</h3>
        <p class="text-lg">
            <?php echo $order->get_shipping_method();?>
        </p>
        <hr class="order__line">
        <h3 class="text-3xl order__list-subtitle">Оплата:</h3>
        <p class="text-lg">
            <?php echo $data['payment_method_title'];?>
        </p>
    </div>
    <h3 class="text-3xl order__list-subtitle">Что дальше?</h3>
    <p class="thanks__text text-lg">
В рабочее время (с 10.00 до 20.00)<br>
        Наш менеджер свяжется с вами в течение 20 минут и подтвердит получение заказа.
    </p>
    <p class="text-lg">
        Доставка осуществляется выбранным способом в согласованные с менеджером сроки.
    </p>
    <br>
    <br>
    <a href="https://t.me/stonoutby" class="hide-s__banner banner">
            <img class="banner-xl" src="https://100nout.by/wp-content/themes/nout/dist/images/tgb/xl1.jpg" alt="Баннер">
            <img class="banner-l" src="https://100nout.by/wp-content/themes/nout/dist/images/tgb/l1.jpg" alt="Баннер">
            <img class="banner-m" src="https://100nout.by/wp-content/themes/nout/dist/images/tgb/m1.jpg" alt="Баннер">
            <img class="banner-s" src="https://100nout.by/wp-content/themes/nout/dist/images/tgb/s1.jpg" alt="Баннер">
            <img class="banner-xs" src="https://100nout.by/wp-content/themes/nout/dist/images/tgb/xs1.jpg" alt="Баннер">
        </a>
</section>
<script>
    const orderPrsG = document.querySelectorAll(".order__list-content-guarantee")
    orderPrsG.forEach(element => {
        if (localStorage.getItem(element.getAttribute("data-product_id"))) {
            const dsoItemID = localStorage.getItem(element.getAttribute("data-product_id"))

            if (dsoItemID === "74101") {
                console.log(localStorage.getItem(element.getAttribute("data-product_id")))
                element.children[0].innerHTML = "Дополнительная гарантия на 3 месяца: "
                element.children[1].innerHTML = "19 BYN"
            }
        }
    });
    
    const orderDetails = `<?php echo $data['customer_note'];?>`.split(';')

    const orderData = [];
    const ordersGarantee = document.querySelectorAll(".order__list-content-guarantee-text")
    console.log(orderDetails)

    orderDetails.forEach(element => {
        if (element.includes("0000")) {
            const arr = element.replace(/(\r\n|\n|\r)/gm,"").split(" ").slice(1)
            orderData.push({
                "sku": arr[0],
                "val": arr.slice(2).join(" ")
            })
            console.log(orderData)
        }
    });

    orderData.forEach((element) => {
        const dsoText = document.querySelector(`p[data-sku='${element.sku}']`)
        dsoText.innerHTML = element.val
    });

</script>

<?php get_footer();