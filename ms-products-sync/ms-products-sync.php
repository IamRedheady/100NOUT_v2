<?php
/**
 * Plugin Name: MS Products Sync (MoySklad → WooCommerce)
 * Description: Автономная синхронизация товаров/остатков и передача заказов в МойСклад (API v2), с очередью на Action Scheduler и подробными логами.
 * Version: 0.1.0
 * Author: 100NOUT
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MS_PS_VERSION', '0.1.0');
define('MS_PS_FILE', __FILE__);
define('MS_PS_PATH', plugin_dir_path(__FILE__));
define('MS_PS_URL', plugin_dir_url(__FILE__));
define('MS_PS_OPTION', 'ms_ps_settings');
define('MS_PS_API_BASE', 'https://api.moysklad.ru/api/remap/1.2/');

require_once MS_PS_PATH . 'includes/class-ms-ps-log.php';
require_once MS_PS_PATH . 'includes/class-ms-ps-jobs.php';
require_once MS_PS_PATH . 'includes/class-ms-ps-api.php';
require_once MS_PS_PATH . 'includes/class-ms-ps-sync.php';
require_once MS_PS_PATH . 'includes/class-ms-ps-order.php';
require_once MS_PS_PATH . 'includes/class-ms-ps-admin.php';

register_activation_hook(__FILE__, array('MsPs_Jobs', 'install'));

add_action('plugins_loaded', function () {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>';
            echo esc_html__('MS Products Sync: требуется активный WooCommerce (нужен Action Scheduler).', 'ms-products-sync');
            echo '</p></div>';
        });
        return;
    }

    $dbVersion = get_option('ms_ps_db_version');
    if ($dbVersion !== '1') {
        MsPs_Jobs::install();
        update_option('ms_ps_db_version', '1', false);
    }

    MsPs_Sync::init();
    MsPs_Order::init();
    MsPs_Admin::init();
});

