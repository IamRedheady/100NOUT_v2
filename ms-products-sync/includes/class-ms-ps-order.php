<?php

if (!defined('ABSPATH')) {
    exit;
}

final class MsPs_Order
{
    public const GROUP = 'ms-products-sync';

    public const ACTION_CREATE = 'ms_ps_order_create';
    public const ACTION_PAYMENT_COMPLETE = 'ms_ps_order_payment_complete';
    public const ACTION_STATUS_CHANGED = 'ms_ps_order_status_changed';

    public static function init()
    {
        add_action('woocommerce_checkout_order_processed', [self::class, 'onCheckout'], 1000, 1);
        add_action('woocommerce_payment_complete', [self::class, 'onPaymentComplete'], 1000, 1);
        add_action('woocommerce_order_status_changed', [self::class, 'onStatusChanged'], 1000, 4);

        add_action(self::ACTION_CREATE, [self::class, 'handleCreate'], 10, 1);
        add_action(self::ACTION_PAYMENT_COMPLETE, [self::class, 'handlePaymentComplete'], 10, 1);
        add_action(self::ACTION_STATUS_CHANGED, [self::class, 'handleStatusChanged'], 10, 1);
    }

    private static function enabled()
    {
        $settings = (array) get_option(MS_PS_OPTION, []);
        return !empty($settings['orders']['enabled']);
    }

    private static function enqueueAsync($hook, $args)
    {
        if (function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action($hook, $args, self::GROUP);
            return;
        }
        wp_schedule_single_event(time() + 1, $hook, [$args]);
    }

    private static function scheduleSingle($timestamp, $hook, $args)
    {
        if (function_exists('as_schedule_single_action')) {
            as_schedule_single_action($timestamp, $hook, $args, self::GROUP);
            return;
        }
        wp_schedule_single_event($timestamp, $hook, [$args]);
    }

    public static function onCheckout($orderId)
    {
        if (!self::enabled()) {
            return;
        }
        $settings = (array) get_option(MS_PS_OPTION, []);
        $mode = 'auto';
        if (isset($settings['orders']['send_mode'])) {
            $mode = (string) $settings['orders']['send_mode'];
        }
        if ($mode === 'manual') {
            return;
        }
        if ($mode === 'autotime') {
            $min = 5;
            if (isset($settings['orders']['auto_minutes'])) {
                $min = (int) $settings['orders']['auto_minutes'];
            }
            if ($min < 1) {
                $min = 1;
            }
            self::scheduleSingle(time() + ($min * 60), self::ACTION_CREATE, array('payload' => array('order_id' => (int) $orderId)));
            return;
        }
        self::enqueueAsync(self::ACTION_CREATE, array('payload' => array('order_id' => (int) $orderId)));
    }

    public static function onPaymentComplete($orderId)
    {
        if (!self::enabled()) {
            return;
        }
        $settings = (array) get_option(MS_PS_OPTION, []);
        if (empty($settings['orders']['ms_state_on_payment'])) {
            return;
        }
        self::enqueueAsync(self::ACTION_PAYMENT_COMPLETE, array('payload' => array('order_id' => (int) $orderId)));
    }

    public static function onStatusChanged($orderId, $from, $to, $order)
    {
        if (!self::enabled()) {
            return;
        }
        $settings = (array) get_option(MS_PS_OPTION, []);
        if (empty($settings['orders']['sync_status_to_ms'])) {
            return;
        }
        self::enqueueAsync(self::ACTION_STATUS_CHANGED, array('payload' => array('order_id' => (int) $orderId)));
    }

    public static function handleCreate($args)
    {
        $payload = is_array($args) ? $args : array();
        if (isset($payload['payload']) && is_array($payload['payload'])) {
            $payload = $payload['payload'];
        }
        $orderId = isset($payload['order_id']) ? (int) $payload['order_id'] : 0;
        if ($orderId <= 0) {
            return;
        }

        $order = wc_get_order($orderId);
        if (!$order) {
            return;
        }

        $settings = (array) get_option(MS_PS_OPTION, []);
        $api = MsPs_Api::fromSettings($settings);
        if (!$api->isConfigured()) {
            MsPs_Log::log('Order: API не настроен', ['order_id' => $orderId]);
            return;
        }

        $msOrderId = (string) $order->get_meta('_ms_order_id');
        if ($msOrderId !== '') {
            return;
        }

        $payload = self::buildOrderPayload($order, $settings, $api);
        if (is_wp_error($payload)) {
            MsPs_Log::log('Order: ошибка payload', ['order_id' => $orderId, 'error' => $payload->get_error_message()]);
            return;
        }

        $created = $api->createCustomerOrder($payload);
        if (is_wp_error($created)) {
            MsPs_Log::log('Order: не создан', ['order_id' => $orderId, 'error' => $created->get_error_message()]);
            $order->add_order_note('МойСклад: ошибка создания заказа: ' . $created->get_error_message());
            return;
        }

        $order->update_meta_data('_ms_order_id', isset($created['id']) ? (string) $created['id'] : '');
        $order->update_meta_data('_ms_updated', isset($created['updated']) ? (string) $created['updated'] : '');
        $order->save();

        $positions = self::buildPositions($order, $settings, $api);
        if (is_wp_error($positions)) {
            MsPs_Log::log('Order: позиции не собраны', ['order_id' => $orderId, 'error' => $positions->get_error_message()]);
            return;
        }

        $href = '';
        if (isset($created['meta']['href'])) {
            $href = (string) $created['meta']['href'];
        }
        if ($href !== '') {
            $posRes = $api->createCustomerOrderPositions($href, $positions);
            if (is_wp_error($posRes)) {
                MsPs_Log::log('Order: позиции не выгружены', ['order_id' => $orderId, 'error' => $posRes->get_error_message()]);
                $order->add_order_note('МойСклад: ошибка выгрузки позиций: ' . $posRes->get_error_message());
                return;
            }
        }

        MsPs_Log::log('Order: создан и выгружен', ['order_id' => $orderId, 'ms_order_id' => (string) $order->get_meta('_ms_order_id')]);
        $order->add_order_note('МойСклад: заказ успешно выгружен');
    }

    public static function handlePaymentComplete($args)
    {
        $payload = is_array($args) ? $args : array();
        if (isset($payload['payload']) && is_array($payload['payload'])) {
            $payload = $payload['payload'];
        }
        $orderId = isset($payload['order_id']) ? (int) $payload['order_id'] : 0;
        if ($orderId <= 0) {
            return;
        }
        $order = wc_get_order($orderId);
        if (!$order) {
            return;
        }

        $settings = (array) get_option(MS_PS_OPTION, []);
        $stateId = '';
        if (isset($settings['orders']['ms_state_on_payment'])) {
            $stateId = (string) $settings['orders']['ms_state_on_payment'];
        }
        if ($stateId === '') {
            return;
        }

        $msOrderId = (string) $order->get_meta('_ms_order_id');
        if ($msOrderId === '') {
            return;
        }

        $api = MsPs_Api::fromSettings($settings);
        $payload = array(
            'state' => array(
                'meta' => array(
                    'href' => MS_PS_API_BASE . 'entity/customerorder/metadata/states/' . $stateId,
                    'type' => 'state',
                    'mediaType' => 'application/json',
                ),
            ),
        );
        $res = $api->updateCustomerOrder($msOrderId, $payload);
        if (is_wp_error($res)) {
            MsPs_Log::log('Order: не обновлён статус при оплате', ['order_id' => $orderId, 'error' => $res->get_error_message()]);
            return;
        }
        MsPs_Log::log('Order: обновлён статус при оплате', ['order_id' => $orderId]);
    }

    public static function handleStatusChanged($args)
    {
        $payload = is_array($args) ? $args : array();
        if (isset($payload['payload']) && is_array($payload['payload'])) {
            $payload = $payload['payload'];
        }
        $orderId = isset($payload['order_id']) ? (int) $payload['order_id'] : 0;
        if ($orderId <= 0) {
            return;
        }
        $order = wc_get_order($orderId);
        if (!$order) {
            return;
        }

        $settings = (array) get_option(MS_PS_OPTION, []);
        $map = array();
        if (isset($settings['orders']['status_map']) && is_array($settings['orders']['status_map'])) {
            $map = (array) $settings['orders']['status_map'];
        }
        $wcStatus = (string) $order->get_status();
        $stateId = isset($map[$wcStatus]) ? (string) $map[$wcStatus] : '';
        if ($stateId === '') {
            return;
        }

        $msOrderId = (string) $order->get_meta('_ms_order_id');
        if ($msOrderId === '') {
            return;
        }

        $api = MsPs_Api::fromSettings($settings);
        $payload = array(
            'state' => array(
                'meta' => array(
                    'href' => MS_PS_API_BASE . 'entity/customerorder/metadata/states/' . $stateId,
                    'type' => 'state',
                    'mediaType' => 'application/json',
                ),
            ),
        );
        $res = $api->updateCustomerOrder($msOrderId, $payload);
        if (is_wp_error($res)) {
            MsPs_Log::log('Order: не обновлён статус', ['order_id' => $orderId, 'error' => $res->get_error_message()]);
            return;
        }
        MsPs_Log::log('Order: обновлён статус', ['order_id' => $orderId, 'wc_status' => $wcStatus]);
    }

    private static function buildOrderPayload($order, $settings, $api)
    {
        $orgId = isset($settings['orders']['organization_id']) ? (string) $settings['orders']['organization_id'] : '';
        $storeId = isset($settings['orders']['store_id']) ? (string) $settings['orders']['store_id'] : '';
        if ($orgId === '' || $storeId === '') {
            return new WP_Error('ms_ps_order_settings', 'Не заполнены организация/склад для заказов');
        }

        $prefix = isset($settings['orders']['prefix']) ? (string) $settings['orders']['prefix'] : 'wc';
        $postfix = isset($settings['orders']['postfix']) ? (string) $settings['orders']['postfix'] : '';
        $name = $prefix . $order->get_order_number() . $postfix;

        $desc = 'Данные клиента: ' . strip_tags(str_replace('<br/>', ' ', $order->get_formatted_billing_address())) . PHP_EOL;
        $desc .= "Телефон: " . $order->get_billing_phone() . PHP_EOL;
        $desc .= "email: " . $order->get_billing_email() . PHP_EOL;
        $desc .= "Метод оплаты: " . $order->get_payment_method_title() . PHP_EOL;
        $desc .= "Метод доставки: " . $order->get_shipping_method() . PHP_EOL;
        $desc .= "Стоимость доставки: " . $order->get_shipping_total() . PHP_EOL;
        $desc .= "Комментарий заказа: " . $order->get_customer_note() . PHP_EOL;

        $payload = array(
            'name' => $name,
            'organization' => array(
                'meta' => array(
                    'href' => MS_PS_API_BASE . 'entity/organization/' . $orgId,
                    'type' => 'organization',
                    'mediaType' => 'application/json',
                ),
            ),
            'store' => array(
                'meta' => array(
                    'href' => MS_PS_API_BASE . 'entity/store/' . $storeId,
                    'type' => 'store',
                    'mediaType' => 'application/json',
                ),
            ),
            // Агент/контрагент: в этой версии используем контрагента по умолчанию (настройка) или email как поиск (добавим в следующем шаге).
            'description' => $desc,
            'vatEnabled' => false,
        );

        $agentId = isset($settings['orders']['counterparty_id']) ? (string) $settings['orders']['counterparty_id'] : '';

        if ($agentId === '') {
            $search = '';
            $email = (string) $order->get_billing_email();
            $phone = (string) $order->get_billing_phone();
            $nameCp = trim((string) $order->get_formatted_billing_full_name());

            if ($email !== '') {
                $search = $email;
            } elseif ($phone !== '') {
                $search = $phone;
            } elseif ($nameCp !== '') {
                $search = $nameCp;
            }

            if ($search === '') {
                return new WP_Error('ms_ps_order_agent', 'Не удалось определить контрагента: пустые email/телефон/имя');
            }

            $found = $api->searchCounterparty($search, 10);
            if (is_wp_error($found)) {
                return new WP_Error('ms_ps_order_agent', 'Ошибка поиска контрагента: ' . $found->get_error_message());
            }
            $rows = (isset($found['rows']) && is_array($found['rows'])) ? (array) $found['rows'] : array();
            if (!empty($rows) && isset($rows[0]['id'])) {
                $agentId = (string) $rows[0]['id'];
            } else {
                $tagsRaw = isset($settings['orders']['counterparty_tags']) ? (string) $settings['orders']['counterparty_tags'] : '';
                $tags = array();
                if ($tagsRaw !== '') {
                    foreach (explode(',', $tagsRaw) as $t) {
                        $t = trim($t);
                        if ($t !== '') {
                            $tags[] = $t;
                        }
                    }
                }

                $createPayload = array(
                    'name' => $nameCp !== '' ? $nameCp : $search,
                );
                if ($email !== '') {
                    $createPayload['email'] = $email;
                }
                if ($phone !== '') {
                    $createPayload['phone'] = $phone;
                }
                if (!empty($tags)) {
                    $createPayload['tags'] = $tags;
                }

                $created = $api->createCounterparty($createPayload);
                if (is_wp_error($created)) {
                    return new WP_Error('ms_ps_order_agent', 'Ошибка создания контрагента: ' . $created->get_error_message());
                }
                $agentId = isset($created['id']) ? (string) $created['id'] : '';
            }
        }

        if ($agentId === '') {
            return new WP_Error('ms_ps_order_agent', 'Не удалось определить/создать контрагента');
        }

        $payload['agent'] = array(
            'meta' => array(
                'href' => MS_PS_API_BASE . 'entity/counterparty/' . $agentId,
                'type' => 'counterparty',
                'mediaType' => 'application/json',
            ),
        );

        return $payload;
    }

    private static function buildPositions($order, $settings, $api)
    {
        $positions = [];
        $reserve = !empty($settings['orders']['reserve']);

        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if (!$product) {
                continue;
            }
            $sku = (string) $product->get_sku();
            if ($sku === '') {
                continue;
            }

            $msAssortment = $api->searchAssortmentBySku($sku);
            if (is_wp_error($msAssortment)) {
                return $msAssortment;
            }
            $rows = isset($msAssortment['rows']) && is_array($msAssortment['rows']) ? (array) $msAssortment['rows'] : array();
            if (count($rows) !== 1) {
                return new WP_Error('ms_ps_order_sku_search', 'Не удалось однозначно найти товар в МС по SKU: ' . $sku);
            }
            $r = $rows[0];
            $id = isset($r['id']) ? (string) $r['id'] : '';
            $type = isset($r['meta']['type']) ? (string) $r['meta']['type'] : '';
            if ($id === '' || $type === '') {
                return new WP_Error('ms_ps_order_sku_search', 'Некорректный ответ поиска МС по SKU: ' . $sku);
            }

            $qty = (float) $item->get_quantity();
            $price = (float) $order->get_item_total($item, false) * 100;

            $pos = array(
                'quantity' => $qty,
                'price' => $price,
                'assortment' => array(
                    'meta' => array(
                        'href' => MS_PS_API_BASE . 'entity/' . $type . '/' . $id,
                        'type' => $type,
                        'mediaType' => 'application/json',
                    ),
                ),
            );
            if ($reserve) {
                $pos['reserve'] = $qty;
            }
            $positions[] = $pos;
        }

        $deliverySku = isset($settings['orders']['delivery_sku']) ? (string) $settings['orders']['delivery_sku'] : '';
        if ($deliverySku !== '' && (float) $order->get_shipping_total() > 0) {
            $msAssortment = $api->searchAssortmentBySku($deliverySku);
            if (is_wp_error($msAssortment)) {
                return $msAssortment;
            }
            $rows = isset($msAssortment['rows']) && is_array($msAssortment['rows']) ? (array) $msAssortment['rows'] : array();
            if (!empty($rows)) {
                $r = $rows[0];
                $id = isset($r['id']) ? (string) $r['id'] : '';
                $type = isset($r['meta']['type']) ? (string) $r['meta']['type'] : '';
                if ($id !== '' && $type !== '') {
                    $positions[] = array(
                        'quantity' => 1,
                        'price' => (float) $order->get_shipping_total() * 100,
                        'assortment' => array(
                            'meta' => array(
                                'href' => MS_PS_API_BASE . 'entity/' . $type . '/' . $id,
                                'type' => $type,
                                'mediaType' => 'application/json',
                            ),
                        ),
                    );
                }
            }
        }

        return $positions;
    }
}

