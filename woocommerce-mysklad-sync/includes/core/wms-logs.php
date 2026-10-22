<?php
//add_action( 'wms_logs',  array('WmsLogs',  'get_button'));
//add_action( 'wp_ajax_my_action_logs',  array('WmsLogs',  'get_log'));
//add_action( 'wp_ajax_nopriv_my_action_logs',  array('WmsLogs',  'get_log' ));

class WmsLogs
{

    static public function init()
    {
        add_action('wms_logs', array('WmsLogs', 'wms_get_logs_page'));

    }

    static function clear_log()
    {
        $dir = WmsLogs::get_dir_logs();
        if (file_exists($dir)) {
            foreach (glob($dir . "*.txt") as $file) {
                unlink($file);
            }
        }
    }

    static function wms_get_logs_page()
    {
        $time = current_time('Y-m-d');
        if (isset($_POST['date'])) $time = $_POST['date'];
        ?>
        <form action="" method="POST">
            <input type="date" id="date" name="date" value="<?php echo $time ?>">
            <button class="btn btn-success" name="loadlogs">Загрузить журнал</button>
            <button class="btn btn-error" name="clearlogs">Очистить журнал</button>

        </form>

        <?php

        if (isset($_POST['clearlogs'])) {
            WmsLogs::clear_log();
        }


        if (isset($_POST['loadlogs'])) {
             WmsLogs::get_log($_POST['date']);
        } else {
            WmsLogs::get_log(current_time('Y-m-d'));
        }

    }

    static public function get_log($falename)
    {

        if (empty($falename)) {
            $falename = current_time('Y-m-d');
        }
        $falename = WmsLogs::get_dir_logs() . $falename . '.txt';
        $logs_resalut = WmsLogs::get_logs($falename);
        if (!empty($logs_resalut)) {
            $count = count($logs_resalut);
            for ($i = 0; $i < $count; $i++) {
                echo '<p>' . $logs_resalut[$i] . '</p>';
            }
        } else {
            echo 'Нет данных';
        }

    }


    static public function get_logs($falename)
    {
        $logs_resalut = @file_get_contents($falename);
        if ($logs_resalut == true) {
            $logs_resalut = explode('%', $logs_resalut);
            return $logs_resalut;

        }
    }

    static public function set_logs($message = '', $on = false)
    {
        if ($on == false) return false;
        if (is_array($message)) {
            $message = WmsHelper::encode($message);
        }
        $filename = WmsLogs::get_filename_logs();
        $array_message = WmsLogs::get_date_time_logs() . ' ' . $message;
        $array_message = WmsHelper::encode($array_message);
        self::wreate_logs($array_message, $filename);
    }

    static public function get_filename_logs()
    {
        return WmsLogs::get_dir_logs() . current_time('Y-m-d') . ".txt";
    }

    static public function get_dir_logs()
    {
        $dir = WP_CONTENT_DIR . "/woocommerce-mysklad-sync/logs/";

        if (file_exists($dir)) {
            return $dir;
        }

        if ( ! wp_mkdir_p( $dir ) ) {
            return false;
        }

        return $dir ;
    }

    static public function get_date_time_logs()
    {
        return current_time('d-m-Y H:i:s');
    }


    static private function wreate_logs($message, $filename)
    {

        if (!$handle = fopen($filename, 'a')) {
            echo "Не могу открыть файл ( $filename )";
            return false;
        }

        if (fwrite($handle, $message . "%\r\n") === FALSE) {
            return false;
        }


        fclose($handle);

    }


}
