<?php
// регистрируем файл стилей и добавляем его в очередь
use WCSTORES\WC\MS\Support\Main\Nonce;
use WCSTORES\WC\MS\Wordpress\Rest\RestRoute;
use WCSTORES\WC\MS\Queues\Queues;

function wms_styles()
{
    $version = (is_user_logged_in() && current_user_can('administrator')) ? time() : WCSTORES_MS_VERSION;


    wp_register_style('wms-style', plugins_url('assets/style.css', WMS_PLUGIN), array(), $version);

    wp_register_script('wms-script', plugins_url('assets/js/mswooscript.js', WMS_PLUGIN), array('jquery'), $version);
    wp_register_script('wms-script2', plugins_url('assets/js/wms-script.js', WMS_PLUGIN), array('jquery'), $version);


    wp_enqueue_style('wms-style');
    wp_enqueue_script('wms-script');
    wp_enqueue_script('wms-script2');

}


function wms_styles_admin()
{
    $version = (is_user_logged_in() && current_user_can('administrator')) ? time() : WCSTORES_MS_VERSION;

    wp_enqueue_style('wms-style-states', plugins_url('assets/css/wms-style-states.css', WMS_PLUGIN), array(), $version);
    wp_enqueue_style('wms-style-admin', plugins_url('assets/css/wms-style-admin.css', WMS_PLUGIN), array(), $version);

    wp_register_style('wms-style', plugins_url('assets/css/style.css', WMS_PLUGIN), array(), $version);

    wp_register_script('wms-script', plugins_url('assets/js/mswooscript.js', WMS_PLUGIN), array('jquery'), $version);
    wp_register_script('wms-script2', plugins_url('assets/js/wms-script.js', WMS_PLUGIN), array('jquery'), $version);
    wp_register_script('wms-script-admin', plugins_url('assets/js/wms-script-admin.js', WMS_PLUGIN), array('jquery'), $version);
    wp_enqueue_script('wcstores-ms-core-admin', plugins_url('assets/build/index.js', WMS_PLUGIN), array('wp-element'), $version, true);
    wp_enqueue_script('wms-script-admin');
    wp_enqueue_style('wms-style');


    if (isset($_REQUEST['page']) and $_REQUEST['page'] == 'wms-settings-page') {
        wp_enqueue_script('wms-script');
        wp_enqueue_script('wms-script2');
    }

    wp_localize_script(
        'wcstores-ms-core-admin',
        'wcstoresMsCoreAdmin',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'home_url' => home_url(),
            'ajax_nonce' => wp_create_nonce('wcstores_admin_nonce'),
            'rest_base_url' => RestRoute::getUrlRoute(),
            'rest_start_sync_product_url' => RestRoute::getUrlRoute('v1/products/sync'),
            'rest_nonce' => Nonce::get(),
            'urls' => array(),
            'i18n' => array(
                'request_error' => __('Request Error. Try Again.', 'woocommerce-gateway-beamcheckout'),
            ),
        )
    );
}


function wms_styles_front()
{
    $version = (is_user_logged_in() && current_user_can('administrator')) ? time() : WCSTORES_MS_VERSION;

    if (apply_filters('wms_style_front', true)) {
        wp_enqueue_style('wms-style-front', plugins_url('assets/public/css/wms-styles-front.css', WMS_PLUGIN), array(), $version);

    }

    if (apply_filters('wms_script_front', true)) {
        wp_enqueue_script('wms-script-front', plugins_url('assets/public/js/wms-script-front.js', WMS_PLUGIN), array('jquery'), $version, true);

    }
}


function wms_monitor_buttom()
{
    printf('<button class="btn  btn-danger rtsync"  name="rtsync" onclick="wms_rsync()">Сбросить</button>');
    printf('<button class="btn  btn-danger stopsync"  name="stopsync" onclick="wms_stopsync()" style="margin-left: 15px;">Остановить</button>');
    //printf('<button class="btn  btn-success startsync"  name="startsync" onclick="wms_startsync()">Ручной перезапуск</button>');

}


function add_style_states()
{
    unlink(WMS_PATH . "assets/wms-style-states.css");
    $states_array = WmsOrderStatusApi::get_instance()->get_states();
    $option['wms_states_wc_ms'] = array();
    $option = get_option('wms_settings_order');
    if (isset($option['wms_states_wc_ms'])) {
        $option = $option['wms_states_wc_ms'];
    }
    foreach ($states_array as $k => &$v1) {
        WmsOrderStatusApi::get_instance()->wreate_style($option[$v1['id']]['label'], WmsHelper::wms_color($v1['color']));

    }

    echo 'Цвета установлены';
}

function wms_start_sync()
{
    if ($assortment = get_transient('wms_offset_assortment')) {
        WmsWalkerFactory::get_walker('assortment')->cron_init();
        WmsWalkerFactory::get_walker('assortment')->start_walker('start');
        WmsLogs::set_logs('Ручной перезапуск товаров', true);

    }

    if ($stock = get_transient('wms_offset_stock')) {
        WmsWalkerFactory::get_walker('stock')->cron_init();
        WmsWalkerFactory::get_walker('stock')->start_walker('start');
        WmsLogs::set_logs('Ручной перезапуск остатков', true);

    }

    if ($image = get_transient('wms_offset_image')) {
        WmsWalkerFactory::get_walker('image')->cron_init();
        WmsWalkerFactory::get_walker('image')->start_walker('start');
        WmsLogs::set_logs('Ручной перезапуск картинок', true);

    }

    if ($counterparty = get_transient('wms_offset_counterparty')) {
        WmsWalkerFactory::get_walker('counterparty')->cron_init();
        WmsWalkerFactory::get_walker('counterparty')->start_walker('start');
        WmsLogs::set_logs('Ручной перезапуск контрагентов', true);

    }

}


function wms_stop_sync()
{
    WmsLogs::set_logs('=== wms_stop_sync() НАЧАЛО: остановка всех синхронизаций ===', true);

    // Останавливаем ВСЕ синхронизации независимо от статуса
    // Останавливаем синхронизацию товаров
    WmsLogs::set_logs('Останавливаем синхронизацию товаров', true);
    WmsWalkerFactory::get_walker('assortment')->delete_walker('stop');

    // Отменяем все задачи в очередях для товаров (legacy + App/Importers)
    try {
        Queues::unscheduleAllActions('sync_products_updates', [], 'mswoo');
        Queues::unscheduleAllActions('checking_for_products_updates', [], 'mswoo');
        Queues::unscheduleAllActions('sync_products', [], 'queues');
        Queues::unscheduleAllActions('sync_products_check', [], 'queues');
        // Не отменяем starting_product_synchronization_automatic — это recurring-расписание, оно должно продолжить работу
        WmsLogs::set_logs('Задачи синхронизации товаров отменены', true);
    } catch (Exception $e) {
        WmsLogs::set_logs('Ошибка при отмене задач товаров: ' . $e->getMessage(), true);
    }

    // Флаг для уже запущенных задач: не ставить следующий шаг
    set_transient('wms_products_sync_stop_requested', 1, 600);

    // Удаляем importer-транзиенты (новая система Importers)
    try {
        \WCSTORES\WC\MS\WooCommerce\Importers::deleteAll();
        WmsLogs::set_logs('Importer-транзиенты удалены', true);
    } catch (Exception $e) {
        WmsLogs::set_logs('Ошибка при удалении importer-транзиентов: ' . $e->getMessage(), true);
    }

    // Удаляем offset легаси-системы
    delete_transient('wms_offset_assortment');

    // Снимаем блокировку синхронизации товаров
    WmsHelper::remove_sync_lock('products');
    update_option('wms_product_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));

    // Останавливаем синхронизацию остатков
    WmsLogs::set_logs('Останавливаем синхронизацию остатков', true);
    WmsWalkerFactory::get_walker('stock')->delete_walker('stop');

    // Отменяем все задачи в очередях для остатков
    try {
        Queues::unscheduleAllActions('sync_stocks_updates', [], 'mswoo');
        Queues::unscheduleAllActions('checking_for_stocks_updates', [], 'mswoo');
        Queues::unscheduleAllActions('stock_synchronization_automatic', [], 'mswoo');
        WmsLogs::set_logs('Задачи синхронизации остатков отменены', true);
    } catch (Exception $e) {
        WmsLogs::set_logs('Ошибка при отмене задач остатков: ' . $e->getMessage(), true);
    }

    // Снимаем блокировку синхронизации остатков
    WmsHelper::remove_sync_lock('stock');
    update_option('wms_stock_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));

    // Останавливаем синхронизацию изображений
    WmsLogs::set_logs('Останавливаем синхронизацию изображений', true);
    WmsWalkerFactory::get_walker('image')->delete_walker('stop');

    // Отменяем все задачи в очередях для изображений
    try {
        Queues::unscheduleAllActions('checking_for_image_updates', [], 'mswoo');
        Queues::unscheduleAllActions('update_image', [], 'mswoo');
        WmsLogs::set_logs('Задачи синхронизации изображений отменены', true);
    } catch (Exception $e) {
        WmsLogs::set_logs('Ошибка при отмене задач изображений: ' . $e->getMessage(), true);
    }

    update_option('wms_image_update_start', array('size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));

    // Останавливаем синхронизацию контрагентов
    WmsLogs::set_logs('Останавливаем синхронизацию контрагентов', true);
    WmsWalkerFactory::get_walker('counterparty')->delete_walker('stop');
    update_option('wms_counterparty_update_start', array('size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));

    WmsLogs::set_logs('=== wms_stop_sync() ЗАВЕРШЕНО: все синхронизации остановлены ===', true);

    wp_die('ok');
}

function wms_r_sync()
{
    WmsLogs::set_logs('=== wms_r_sync() НАЧАЛО: полная остановка всех синхронизаций ===', true);

    // Останавливаем все walker'ы
    WmsWalkerFactory::get_walker('assortment')->delete_walker();
    WmsWalkerFactory::get_walker('stock')->delete_walker();
    WmsWalkerFactory::get_walker('image')->delete_walker();
    WmsWalkerFactory::get_walker('counterparty')->delete_walker();

    set_transient('wms_products_sync_stop_requested', 1, 600);

    // Удаляем importer-транзиенты (новая система Importers)
    try {
        \WCSTORES\WC\MS\WooCommerce\Importers::deleteAll();
    } catch (Exception $e) {
        WmsLogs::set_logs('Ошибка при удалении importer-транзиентов: ' . $e->getMessage(), true);
    }

    // Удаляем offset легаси-системы
    delete_transient('wms_offset_assortment');
    delete_transient('wms_offset_stock');

    // Отменяем все задачи в очередях
    try {
        Queues::unscheduleAllActions('sync_products_updates', [], 'mswoo');
        Queues::unscheduleAllActions('checking_for_products_updates', [], 'mswoo');
        Queues::unscheduleAllActions('sync_products', [], 'queues');
        Queues::unscheduleAllActions('sync_products_check', [], 'queues');
        Queues::unscheduleAllActions('sync_stocks_updates', [], 'mswoo');
        Queues::unscheduleAllActions('checking_for_stocks_updates', [], 'mswoo');
        Queues::unscheduleAllActions('stock_synchronization_automatic', [], 'mswoo');
        Queues::unscheduleAllActions('checking_for_image_updates', [], 'mswoo');
        Queues::unscheduleAllActions('update_image', [], 'mswoo');
        WmsLogs::set_logs('Все задачи в очередях отменены', true);
    } catch (Exception $e) {
        WmsLogs::set_logs('Ошибка при отмене задач: ' . $e->getMessage(), true);
    }

    // Снимаем все блокировки синхронизации
    WmsHelper::remove_sync_lock('products');
    WmsHelper::remove_sync_lock('stock');

    // Обновляем статусы
    update_option('wms_product_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));
    update_option('wms_stock_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));
    update_option('wms_image_update_start', array('size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));
    update_option('wms_counterparty_update_start', array('size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));

    WmsLogs::set_logs('=== wms_r_sync() ЗАВЕРШЕНО: все синхронизации полностью остановлены ===', true);
}


function wms_order_hook_order_statuses()
{

    $state = new WmsOrderStatusApi();
    add_filter('init', array($state, 'wc_register_post_statuses'));
    add_filter('wc_order_statuses', array($state, 'wc_add_order_statuses'));
}