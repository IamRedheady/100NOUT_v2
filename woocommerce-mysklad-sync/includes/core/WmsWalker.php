<?php


class WmsWalker
{

    protected $type;

    protected $speed = false;

    function __construct($type = '')
    {
        $this->type = $type;

        add_action('wms_walker_' . $this->type, array($this, 'walker'));
        add_filter('cron_schedules', array($this, 'add_schedule'));


    }

    public function get_type()
    {
        return $this->type;
    }

    /**
     *
     */
    public function walker($type)
    {
        if ($end = get_transient('wms_time_end_loop_' . $type)) {
            // Даем больше времени на сохранение offset перед чтением
            usleep(500000); // 0.5 секунды для гарантии сохранения

            // Принудительно очищаем весь кэш перед чтением
            wp_cache_flush();
            wp_cache_delete('wms_offset_' . $type, 'transient');
            wp_cache_delete('wms_offset_' . $type, 'options');

            // Читаем из option (более надежно)
            $option_key = 'wms_offset_' . $type;
            $saved_offset = get_option($option_key, false);

            // Если в option нет, читаем из transient
            if ($saved_offset === false) {
                $saved_offset = get_transient('wms_offset_' . $type);
                // Если нашли в transient, сохраняем в option для надежности
                if ($saved_offset !== false && is_numeric($saved_offset)) {
                    update_option($option_key, intval($saved_offset));
                }
            }

            WmsLogs::set_logs('walker() для ' . $type . ': обнаружен wms_time_end_loop_, читаем offset из option=' . var_export(get_option($option_key, false), true) . ', transient=' . var_export(get_transient('wms_offset_' . $type), true) . ', используем=' . var_export($saved_offset, true), true);

            // Проверяем, что offset действительно обновился
            if ($saved_offset === false || $saved_offset === '' || !is_numeric($saved_offset)) {
                WmsLogs::set_logs('ОШИБКА: offset не найден или не является числом! option=' . var_export(get_option($option_key, false), true) . ', transient=' . var_export(get_transient('wms_offset_' . $type), true), true);
                delete_transient('wms_time_end_loop_' . $type);
                delete_transient('wms_walker_start_' . $type);
                return;
            }

            delete_transient('wms_time_end_loop_' . $type);
            delete_transient('wms_walker_start_' . $type);

            // Проверяем флаг speed - если установлен, очищаем его и продолжаем
            $is_speed = get_transient('wms_loop_speed_' . $type);
            if ($is_speed) {
                WmsLogs::set_logs('walker() для ' . $type . ': обнаружен wms_loop_speed_, очищаем и продолжаем', true);
                delete_transient('wms_loop_speed_' . $type);
            }

            WmsLogs::set_logs('walker() для ' . $type . ': запускаем новый цикл с offset=' . $saved_offset, true);
            do_action('wms_walker_hook_' . $type, $type);

            wp_die();

        }

        if ($end = get_transient('wms_time_start_sync_' . $type)) {
            do_action('wms_walker_hook_' . $type, $type);
            return;
        }

        $transient = get_transient('wms_time_start_' . $type);
        if ($transient === false) {
            delete_transient('wms_walker_start_' . $type);
            return;
        }

        $walker = get_transient('wms_walker_start_' . $type);

        if ($walker === false) {
            set_transient('wms_walker_start_' . $type, 5);
            return;
        }

        if ($walker == 1) {

            WmsLogs::set_logs('Похоже завис. Подготовка к старту', true);
            set_transient('wms_walker_start_' . $type, 'start');
            return;
        }

        if ($walker == 'start') {

            WmsLogs::set_logs('Cнова стартуем ' . $type, true);
            delete_transient('wms_walker_start_' . $type);
            do_action('wms_walker_hook_' . $type, $type);
            return;

        }

        WmsLogs::set_logs('Зависание до старта ' . $walker, true);
        set_transient('wms_walker_start_' . $type, $walker - 1);
        return;
    }

    /**
     * Cron shedule setup for 1 minute interval
     */
    function add_schedule($schedules)
    {
        $schedules['wms_walker_shedule'] = array(
            'interval' => apply_filters('wms_walker_shedule_interval', 60),
            'display' => 'S30 sec'
        );

        return $schedules;
    }

    /**
     * Cron task restart
     */
    function cron_init()
    {
        if (!wp_next_scheduled('wms_walker_' . $this->type, [$this->type])) {
            wp_schedule_event(time(), 'wms_walker_shedule', 'wms_walker_' . $this->type, [$this->type]);
        }
    }

    /**
     *
     */
    function delete_walker($stop = null)
    {
        WmsLogs::set_logs('delete_walker() вызван для ' . $this->type . ' с параметром stop=' . var_export($stop, true), true);

        if ($stop === null) {
            // Полная очистка - удаляем и offset
            $option_key = 'wms_offset_' . $this->type;
            delete_option($option_key);
            delete_transient('wms_offset_' . $this->type);
            wp_cache_delete('wms_offset_' . $this->type, 'transient');
            wp_cache_delete($option_key, 'options');
            WmsLogs::set_logs('delete_walker() для ' . $this->type . ': offset удален (полная очистка)', true);
        } else {
            // Остановка - offset сохраняем, но останавливаем все процессы
            WmsLogs::set_logs('delete_walker() для ' . $this->type . ': остановка без удаления offset', true);
        }

        // Удаляем все transient'ы walker'а
        delete_transient('wms_time_start_' . $this->type);
        delete_transient('wms_time_end_loop_' . $this->type);
        delete_transient('wms_walker_start_' . $this->type);
        delete_transient('wms_time_start_walker_' . $this->type);
        delete_transient('wms_time_start_sync_' . $this->type);
        delete_transient('wms_loop_speed_' . $this->type);
        delete_transient('wms_sync_id_loop_' . $this->type);

        // Очищаем кэш
        wp_cache_delete('wms_time_start_' . $this->type, 'transient');
        wp_cache_delete('wms_time_end_loop_' . $this->type, 'transient');
        wp_cache_delete('wms_walker_start_' . $this->type, 'transient');

        // Отменяем запланированные cron задачи
        wp_clear_scheduled_hook('wms_walker_' . $this->type, [$this->type]);

        WmsLogs::set_logs('delete_walker() завершен для ' . $this->type . ': все transient и cron задачи очищены', true);
    }


    function end_loop($offset = '', $speed = false)
    {
        // Убеждаемся, что offset - это число
        $offset = intval($offset);

        WmsLogs::set_logs('end_loop() вызван для ' . $this->type . ': устанавливаем offset=' . $offset . ', speed=' . ($speed ? 'true' : 'false'), true);

        // Получаем текущий offset для сравнения
        $old_offset = get_transient('wms_offset_' . $this->type);
        WmsLogs::set_logs('end_loop() для ' . $this->type . ': старый offset=' . ($old_offset !== false ? $old_offset : 'не установлен') . ', новый offset=' . $offset, true);

        delete_transient('wms_time_start_' . $this->type);
        delete_transient('wms_sync_id_loop_' . $this->type);

        // Используем более надежный способ: сохраняем в option И transient
        // Option более надежен, так как не кэшируется так агрессивно
        $option_key = 'wms_offset_' . $this->type;
        update_option($option_key, $offset);

        // Также сохраняем в transient с увеличенным временем жизни
        delete_transient('wms_offset_' . $this->type);
        wp_cache_delete('wms_offset_' . $this->type, 'transient'); // Принудительно очищаем кэш
        $day_seconds = defined('DAY_IN_SECONDS') ? DAY_IN_SECONDS : 86400;
        $week_seconds = defined('WEEK_IN_SECONDS') ? WEEK_IN_SECONDS : (7 * $day_seconds);
        set_transient('wms_offset_' . $this->type, $offset, $week_seconds); // Увеличиваем время жизни до недели

        // Принудительно очищаем кэш WordPress
        wp_cache_flush();

        // Проверяем, что offset действительно сохранен (читаем из option, так как он более надежен)
        $saved_offset_option = get_option($option_key, false);
        $saved_offset_transient = get_transient('wms_offset_' . $this->type);

        if ($saved_offset_option != $offset) {
            WmsLogs::set_logs('ОШИБКА: offset не сохранился в option! Ожидалось: ' . $offset . ', получено: ' . var_export($saved_offset_option, true), true);
            // Пытаемся сохранить еще раз
            update_option($option_key, $offset);
        } else {
            WmsLogs::set_logs('end_loop() для ' . $this->type . ': offset успешно сохранен в option=' . $saved_offset_option . ', transient=' . var_export($saved_offset_transient, true), true);
        }

        // Устанавливаем флаг завершения цикла только после успешного сохранения offset
        set_transient('wms_time_end_loop_' . $this->type, time());

        if ($speed === false) {
            delete_transient('wms_loop_speed_' . $this->type);
        } elseif ($speed === true) {
            set_transient('wms_loop_speed_' . $this->type, time());
        }

        // Финальная проверка
        $final_offset_option = get_option($option_key, false);
        $final_offset_transient = get_transient('wms_offset_' . $this->type);
        WmsLogs::set_logs('end_loop() завершен для ' . $this->type . ': финальный offset option=' . var_export($final_offset_option, true) . ', transient=' . var_export($final_offset_transient, true), true);
    }

    function loop($id = '')
    {
        set_transient('wms_sync_id_loop_' . $this->type, $id);
    }

    function get_id_loop()
    {
        $transient = get_transient('wms_sync_id_loop_' . $this->type);
        if ($transient === false) {
            return false;
        }
        return $transient;
    }


    function start_loop($offset = '')
    {
        $offset = intval($offset);
        $option_key = 'wms_offset_' . $this->type;

        // Сохраняем в option и transient
        update_option($option_key, $offset);
        $day_seconds = defined('DAY_IN_SECONDS') ? DAY_IN_SECONDS : 86400;
        $week_seconds = defined('WEEK_IN_SECONDS') ? WEEK_IN_SECONDS : (7 * $day_seconds);
        set_transient('wms_offset_' . $this->type, $offset, $week_seconds);
        set_transient('wms_time_start_' . $this->type, time());
        delete_transient('wms_time_start_sync_' . $this->type);

        // Очищаем кэш
        wp_cache_delete('wms_offset_' . $this->type, 'transient');
        wp_cache_delete($option_key, 'options');
    }

    function start_sync()
    {
        set_transient('wms_time_start_sync_' . $this->type, time());
    }


    function start_walker($start = null)
    {
        set_transient('wms_time_start_walker_' . $this->type, time());
        if ($start == 'start') {
            do_action('wms_walker_hook_' . $this->type, $this->type);
        }

    }

    function get_start_walker()
    {
        return get_transient('wms_time_start_walker_' . $this->type);
    }

    function get_start_loop()
    {
        return get_transient('wms_time_start_' . $this->type);
    }


}

class WmsWalkerFactory
{
    protected static $walker = array();

    public static function push_walker(WmsWalker $walker)
    {
        self::$walker[$walker->get_type()] = $walker;
    }

    public static function get_walker($type)
    {
        return isset(self::$walker[$type]) ? self::$walker[$type] : null;
    }

    public static function remove_walker($type)
    {
        if (array_key_exists($type, self::$walker)) {
            unset(self::$walker[$type]);
        }
    }
}