<?php


namespace WCSTORES\WC\MS\Controller\Sync;


use ActionScheduler;
use WCSTORES\WC\MS\Queues\Queues;
use WCSTORES\WC\MS\WooCommerce\Importers;
use WmsLogs;

/**
 * Class AssortmentController
 * @package WCSTORES\WC\MS\Controller\Sync
 */
class AssortmentController extends SyncController
{
    /**
     * @param $type
     * @param $action
     * @param $href
     * @return mixed
     * @throws \Exception
     */
    public function webhook($type, $action, $href)
    {
        $wms_webhook_settings = get_option('wms_settings_webhook');

        if ($wms_webhook_settings['wms_active_webhook_product'] == 'on') {
            $assortment = new \WmsAssortmentController();
            $assortment->assortment_webhook($href, $action, false);
        }

        return $type;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function start()
    {
        // Проверяем блокировку синхронизации остатков
        if (!\WmsHelper::can_start_products_sync()) {
            WmsLogs::set_logs('Синхронизация товаров отменена: идет синхронизация остатков', true);
            return $this->responseError(array('Синхронизация товаров заблокирована: идет синхронизация остатков'));
        }

        // Отменяем все существующие задачи синхронизации (легаси + новая) чтобы не было параллельных цепочек
        try {
            \WCSTORES\WC\MS\Queues\Queues::unscheduleAllActions('sync_products_updates', [], 'mswoo');
            \WCSTORES\WC\MS\Queues\Queues::unscheduleAllActions('checking_for_products_updates', [], 'mswoo');
            \WCSTORES\WC\MS\Queues\Queues::unscheduleAllActions('sync_products', [], 'queues');
            \WCSTORES\WC\MS\Queues\Queues::unscheduleAllActions('sync_products_check', [], 'queues');
        } catch (\Exception $e) {
            WmsLogs::set_logs('Ошибка при отмене старых задач: ' . $e->getMessage(), true);
        }

        delete_transient('wms_offset_assortment');

        $importer = Importers::startImport('sync_products', $args = array());

        if (!$importer) {
            return $this->responseError(array('Not start sync'));
        }

        delete_transient('wms_products_sync_stop_requested');

        // Устанавливаем блокировку синхронизации товаров
        \WmsHelper::set_sync_lock('products');

        WmsLogs::set_logs('Стартуем (синхронизация товаров)', true);
        update_option('wms_product_update_start_time', current_time("Y-m-d"));
        update_option('wms_product_update_start', array('load' => 'start', 'message' => 'Начало полной синхронизации...'));

        // Запускаем первую задачу очереди сразу, не дожидаясь cron (иначе синхронизация не стартует при отключённом cron)
        if ($importer && is_numeric($importer) && class_exists('ActionScheduler')) {
            try {
                ActionScheduler::runner()->process_action($importer);
                WmsLogs::set_logs('Первая задача очереди товаров выполнена', true);
            } catch (\Exception $e) {
                WmsLogs::set_logs('Ошибка запуска первой задачи: ' . $e->getMessage(), true);
            }
        }

        return $this->responseOk(array('message' => 'Sync ok'));
    }

    /**
     *
     * @param $importerUuid
     * @param int $offset
     * @return false|int|string[]
     * @throws \Exception
     */
    public function sync($importerUuid, $offset = 0)
    {
        if (get_transient('wms_products_sync_stop_requested')) {
            WmsLogs::set_logs('Очередь товаров: остановка запрошена, выходим без следующего шага', true);
            \WmsHelper::remove_sync_lock('products');
            update_option('wms_product_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));
            delete_transient('wms_products_sync_stop_requested');
            return ['stopped'];
        }

        if (!$Importer = Importers::getImporter($importerUuid, true, 'sync_products')) {
            WmsLogs::set_logs('Очередь товаров: импортер не найден для uuid=' . (string) $importerUuid, true);
            return false;
        }

        $offset = ($offset > 0) ? $offset : (int) $Importer['offset'];
        WmsLogs::set_logs('Очередь синхронизации товаров: запуск (uuid=' . (string) $importerUuid . ', offset=' . $offset . ')', true);

        $assortment = new \WmsAssortmentController();
        $settings = $assortment->get_settings();
        $limit = $assortment->get_limit();

        do_action('wms_assortment_start_sync_loop');

        Importers::installImportVerification($Importer['name'], $importerUuid);

        $assortments = $assortment->get_assortment($offset);

        WmsLogs::set_logs('get_products() начало (rows=' . (isset($assortments['rows']) ? count($assortments['rows']) : 0) . ')', true);
        if ($products = $assortment->get_products($assortments)) {
            WmsLogs::set_logs('get_products() готово: ' . count($products) . ' товаров к обработке', true);

            WmsLogs::set_logs('update_products() начало', true);
            $products = $assortment->update_products($products);
            WmsLogs::set_logs('update_products() готово: ' . count($products) . ' товаров к созданию/обновлению', true);

            if (!empty($products)) {
                WmsLogs::set_logs('create_products() начало (' . count($products) . ' шт)', true);
                $assortment->create_products($products);
                WmsLogs::set_logs('create_products() готово', true);
            }
        } else {
            WmsLogs::set_logs('get_products() вернул пусто', true);
        }

        $count = count($assortments['rows']);

        if ($count < $limit || empty($assortments['rows'])) {

            Importers::endImport($Importer['name'], $importerUuid);

            $count_product = $offset + $count;
            WmsLogs::set_logs('Синхронизация закончилась ' . $count_product . ' товаров было синхронизировано', true);
            update_option('wms_product_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Полная синхронизация'));

            // Снимаем блокировку синхронизации товаров
            \WmsHelper::remove_sync_lock('products');

            do_action('wms_assortment_end_sync');

            return ['ok'];
        }

        $Importer['count_product'] = $assortments['meta']['size'];
        $Importer['offset'] = $offset + $limit;

        Importers::setImporters($Importer, $importerUuid);

        update_option('wms_product_update_start', array('size' => $assortments['meta']['size'], "count" => $offset, "time" => 0, 'load' => 'load'));

        do_action('wms_assortment_end_sync_loop');

        if (get_transient('wms_products_sync_stop_requested')) {
            WmsLogs::set_logs('Очередь товаров: остановка запрошена, следующий шаг не ставим', true);
            \WmsHelper::remove_sync_lock('products');
            update_option('wms_product_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));
            delete_transient('wms_products_sync_stop_requested');
            return ['stopped'];
        }

        return Importers::nextImportStep(
            $Importer['name'],
            $importerUuid,
            (isset($settings['wms_product_type_load']) && $settings['wms_product_type_load'] === 'speed')
        );

    }

    /**
     * @param array $data
     * @return array
     */
    public function responseOk($data = array())
    {
        return $this->response(true, $data);
    }

    /**
     * @param array $errors
     * @return array
     */
    public function responseError($errors = array())
    {
        return $this->response(false, array(), $errors);
    }

    /**
     * @param bool $success
     * @param array $data
     * @param array $errors
     * @return array
     */
    public function response($success = true, $data = array(), $errors = array())
    {
        return array('success' => $success, 'data' => $data, 'errors' => $errors);
    }


    /**
     * Настройка автоматического запуска синхронизации товаров через ActionScheduler.
     * Аналог StockController::settingAutomaticStartupSettings().
     * Вызывается при сохранении настроек (на хуке init в admin).
     *
     * @return bool
     * @throws \Exception
     */
    public function settingAutomaticStartupSettings(): bool
    {
        $settings = get_option('wms_settings_product');
        $isOn = isset($settings['wms_load_auto_product']) && $settings['wms_load_auto_product'] === 'on';
        $hasPending = Queues::getQueuesCountToStatusPending('starting_product_synchronization_automatic') > 0;

        // Полный пересчёт расписания только когда форма настроек товаров сохранена
        if (isset($_REQUEST['wms_settings_product'])) {
            Queues::unscheduleAllActions('starting_product_synchronization_automatic');

            if (!$isOn) {
                return false;
            }

            WmsLogs::set_logs('Товары: настроен автозапуск через ActionScheduler раз в сутки', true);
            Queues::addRecurring(time() + DAY_IN_SECONDS, DAY_IN_SECONDS, 'starting_product_synchronization_automatic');

            return true;
        }

        // При обычной загрузке страницы: если настройка включена, но задачи нет — создаём
        if ($isOn && !$hasPending) {
            WmsLogs::set_logs('Товары: задача автозапуска не найдена, создаём (первый запуск)', true);
            Queues::addRecurring(time() + DAY_IN_SECONDS, DAY_IN_SECONDS, 'starting_product_synchronization_automatic');
        }

        return $isOn;
    }

    /**
     * Хэндлер для recurring-задачи ActionScheduler.
     * Запускает синхронизацию через legacy-старт (с повторной попыткой при блокировке).
     *
     * @return void
     * @throws \Exception
     */
    public function startingProductSynchronizationAutomatic(): void
    {
        $assortment = new \WmsAssortmentController();
        $assortment->start_product_syn_scheduled();
    }

    /**
     *
     */
    public function queues()
    {

    }

}