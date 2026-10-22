<?php
if (!defined('ABSPATH')) exit;

/**
 *
 */
class WmsMonitor
{

    static public function init()
    {
        add_action('wdc_admin_page_menu_before', array('WmsMonitor', 'get_monitor_div'), 10);
        add_action('wms_monitor', array('WmsMonitor', 'get_monitor_html'), 10);
        add_action('wp_ajax_wms_monitor', array('WmsMonitor', 'get_monitor_html_ajax'));
        add_action('wp_ajax_nopriv_wms_monitor', array('WmsMonitor', 'get_monitor_html_ajax'));

    }


    static function get_monitor_load($type, $button = '')
    {
        $start = get_option($type);

        if (!is_array($start)) {
            return ' <span class = "$type"> Нет даных</span>';
        }

        $load = (isset($start['load'])) ? $start['load'] : '';
        $message = (isset($start['message'])) ? $start['message'] : 'Нет даных';
        $size = (isset($start['size']) and $start['size'] !== 0) ? $start['size'] : 100;
        $sTime = (isset($start['time'])) ? $start['time'] : 'Нет данных';
        $printMessage = ' <span class = "$type">' . $message . '</span>';

        if ($load== 'start' or $load == 'load') { ?>
            <script> wms_load_button_sync('<?php echo $button;?>'); </script>
        <?php }

        if ($load === 'load' and $type === 'wms_image_update_start') {
            echo $printMessage;
            return true;
        }


        if ($load === 'load' and $type !== 'wms_image_update_start' or isset($_POST['start']) and $_POST['start'] == 'start') {

            $message = '<span> Обработано записей ' . $start['count'] . '</span><progress value="' . $start['count'] . '" max="' . $size . '"></progress>';
            $message .=  'Обновновляется... ' .  ($size > 0 ? round($start['count'] * 100 / $size) : 0) . '% <span> Всего записей ' . $size . '</span>';
            echo $message;
            return true;
        }

        if ($load === 'start') {
            echo $printMessage;
            return true;

        }

        echo $sTime . $printMessage;

        if ($load === 'stop') { ?>
            <script> wms_stop_button_sync('<?php echo $button;?>', 'Синхронизировать'); </script>
        <?php }

        return true;
    }

    static function get_monitor_div()
    {
        ?>
        <div class="alert alert-primary" role="alert">
            Внимание с версии плагина 1.9.16 изменена логика работы вебхуков.
            Для корректной работы нужно выполнить ряд действий:</br>
            1.удалите и заново создайте вебхуки. </br>
            2.настроить крон задание на запрос <?php echo home_url('wp-cron.php') ?></br></br>
            Пример запроса </br>
            # выполнять команду каждые 5 минут</br>
            */5 * * * * wget -O /dev/null -q '<?php echo home_url('wp-cron.php') ?>' </br>

            3.Отключить WP Cron  добавьте в wp-config.php такую константу define( 'DISABLE_WP_CRON', true ); </br>

            Данные действия можно не выполнять если у вас большая посещаемость сайта.
        </div>
        <div class="wms-monitor">
            <?php do_action('wms_monitor'); ?>
        </div>
        <?php
    }

    static function get_monitor_html()
    {
        ?>
        <div class="wms-main-monitor" id="wms-main-monitor">

            <?php $alerts = apply_filters('wms_monitor_alert_data', []); ?>

            <?php if (!empty($alerts)) : ?>

                <?php foreach($alerts as $alert) : ?>

                    <div class="wms-main-load-data" id="wms-main-load-data">
                        <div class="alert alert-<?php echo $alert['type']; ?>" role="alert">
                            <?php echo $alert['message']; ?>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>


            <div class="product-monitor">
                <div> Последняя синхронизация товаров была: <span class="wms-monitor-data "
                                                                  id="product-monitor-id"><?php self::get_monitor_load('wms_product_update_start', '.loadproduct'); ?></span>
                </div>

            </div>
            <div class="stock-monitor">
                <div> Последняя синхронизация остатков была: <span class="wms-monitor-data "
                                                                   id="stock-monitor-id"><?php self::get_monitor_load('wms_stock_update_start', '.loadstock'); ?></span>
                </div>

            </div>

            <div class="image-monitor">
                <div> Последняя синхронизация изображений была: <span class="wms-monitor-data "
                                                                      id="image-monitor-id"><?php self::get_monitor_load('wms_image_update_start'); ?></span>
                </div>

            </div>
            <div class="counterparty-monitor">
                <div> Последняя синхронизация контрагентов была: <span class="wms-monitor-data "
                                                                       id="counterparty-monitor-id"><?php self::get_monitor_load('wms_counterparty_update_start', '.loadcounterparty'); ?></span>
                </div>

            </div>
        </div>


        <?php
    }

    static function get_monitor_html_ajax()
    {
        self::get_monitor_html();
        wp_die();
    }


}
