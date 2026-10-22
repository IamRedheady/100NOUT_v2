<?php

if (!defined('ABSPATH')) {
    exit;
}

final class MsPs_Admin
{
    public static function init()
    {
        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_post_ms_ps_save_settings', [self::class, 'saveSettings']);

        add_action('wp_ajax_ms_ps_start_sync', [self::class, 'ajaxStartSync']);
        add_action('wp_ajax_ms_ps_stop_sync', [self::class, 'ajaxStopSync']);
        add_action('wp_ajax_ms_ps_get_job', [self::class, 'ajaxGetJob']);
        add_action('wp_ajax_ms_ps_clear_log', [self::class, 'ajaxClearLog']);
    }

    public static function menu()
    {
        add_menu_page(
            'MS Products Sync',
            'МойСклад Sync',
            'manage_woocommerce',
            'ms-products-sync',
            [self::class, 'page'],
            'dashicons-update',
            56
        );
    }

    private static function nonceField()
    {
        wp_nonce_field('ms_ps_admin', 'ms_ps_nonce');
    }

    private static function checkNonce()
    {
        if (!isset($_POST['ms_ps_nonce']) || !wp_verify_nonce((string) $_POST['ms_ps_nonce'], 'ms_ps_admin')) {
            wp_die('Bad nonce');
        }
    }

    public static function page()
    {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $settings = (array) get_option(MS_PS_OPTION, []);
        $job = MsPs_Jobs::findRunning(MsPs_Sync::JOB_TYPE_PRODUCTS);

        $loginRaw = isset($settings['auth']['login']) ? (string) $settings['auth']['login'] : '';
        $login = esc_attr($loginRaw);
        $password = isset($settings['auth']['password']) ? (string) $settings['auth']['password'] : '';

        $pageLimit = isset($settings['products']['page_limit']) ? (int) $settings['products']['page_limit'] : 100;
        $stepTime = isset($settings['products']['step_time_limit_sec']) ? (int) $settings['products']['step_time_limit_sec'] : 20;
        $newStatus = isset($settings['products']['new_post_status']) ? (string) $settings['products']['new_post_status'] : 'publish';
        $skuSource = isset($settings['products']['sku_source']) ? (string) $settings['products']['sku_source'] : 'code';
        $priceTypeId = esc_attr(isset($settings['products']['price_type_id']) ? (string) $settings['products']['price_type_id'] : '');

        $storeIds = isset($settings['store_ids']) && is_array($settings['store_ids']) ? (array) $settings['store_ids'] : array();

        $categoriesEnabled = !isset($settings['categories']['enabled']) || !empty($settings['categories']['enabled']);
        $attributesEnabled = !isset($settings['attributes']['enabled']) || !empty($settings['attributes']['enabled']);

        $stockMode = isset($settings['stock']['mode']) ? (string) $settings['stock']['mode'] : 'stock_quantity';
        $metaShop = esc_attr(isset($settings['stock']['meta_key_in_shop']) ? (string) $settings['stock']['meta_key_in_shop'] : 'v_magazine');
        $metaPre = esc_attr(isset($settings['stock']['meta_key_preorder']) ? (string) $settings['stock']['meta_key_preorder'] : 'pod_zakaz');
        $priceZeroToDraft = !empty($settings['stock']['price_zero_to_draft']);

        $ordersEnabled = !empty($settings['orders']['enabled']);
        $sendMode = isset($settings['orders']['send_mode']) ? (string) $settings['orders']['send_mode'] : 'auto';
        $autoMin = isset($settings['orders']['auto_minutes']) ? (int) $settings['orders']['auto_minutes'] : 5;
        $orgId = esc_attr(isset($settings['orders']['organization_id']) ? (string) $settings['orders']['organization_id'] : '');
        $orderStoreId = esc_attr(isset($settings['orders']['store_id']) ? (string) $settings['orders']['store_id'] : '');
        $counterpartyId = esc_attr(isset($settings['orders']['counterparty_id']) ? (string) $settings['orders']['counterparty_id'] : '');
        $counterpartyTags = esc_attr(isset($settings['orders']['counterparty_tags']) ? (string) $settings['orders']['counterparty_tags'] : '');
        $prefix = esc_attr(isset($settings['orders']['prefix']) ? (string) $settings['orders']['prefix'] : 'wc');
        $postfix = esc_attr(isset($settings['orders']['postfix']) ? (string) $settings['orders']['postfix'] : '');
        $reserve = !empty($settings['orders']['reserve']);
        $deliverySku = esc_attr(isset($settings['orders']['delivery_sku']) ? (string) $settings['orders']['delivery_sku'] : '');
        $syncStatus = !empty($settings['orders']['sync_status_to_ms']);
        $stateOnPayment = esc_attr(isset($settings['orders']['ms_state_on_payment']) ? (string) $settings['orders']['ms_state_on_payment'] : '');

        $logDate = isset($_GET['log_date']) ? sanitize_text_field((string) $_GET['log_date']) : current_time('Y-m-d');
        $logs = MsPs_Log::read($logDate);

        ?>
        <div class="wrap">
            <h1>MS Products Sync</h1>

            <h2>Синхронизация</h2>
            <p>
                <button class="button button-primary" id="msps-start">Синхронизировать товары</button>
                <button class="button" id="msps-stop" <?php echo $job ? '' : 'disabled'; ?>>Стоп</button>
                <span id="msps-job"></span>
            </p>

            <hr/>

            <h2>Настройки</h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="ms_ps_save_settings"/>
                <?php self::nonceField(); ?>

                <h3>Авторизация МойСклад (API v2)</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label>Логин</label></th>
                        <td><input type="text" name="ms_ps_settings[auth][login]" value="<?php echo $login; ?>" class="regular-text"/></td>
                    </tr>
                    <tr>
                        <th scope="row"><label>Пароль / токен</label></th>
                        <td>
                            <input type="password" name="ms_ps_settings[auth][password]" value="<?php echo esc_attr($password); ?>" class="regular-text" autocomplete="new-password"/>
                            <p class="description">Хранится в опциях сайта. Рекомендуется отдельный пользователь/токен.</p>
                        </td>
                    </tr>
                </table>

                <h3>Товары</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Товаров в странице API</th>
                        <td><input type="number" min="10" max="500" name="ms_ps_settings[products][page_limit]" value="<?php echo (int) $pageLimit; ?>"/></td>
                    </tr>
                    <tr>
                        <th scope="row">Лимит времени шага (сек)</th>
                        <td><input type="number" min="5" max="25" name="ms_ps_settings[products][step_time_limit_sec]" value="<?php echo (int) $stepTime; ?>"/></td>
                    </tr>
                    <tr>
                        <th scope="row">Статус новых товаров</th>
                        <td>
                            <select name="ms_ps_settings[products][new_post_status]">
                                <option value="publish" <?php selected($newStatus, 'publish'); ?>>Опубликовано</option>
                                <option value="pending" <?php selected($newStatus, 'pending'); ?>>На утверждении</option>
                                <option value="draft" <?php selected($newStatus, 'draft'); ?>>Черновик</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Источник SKU</th>
                        <td>
                            <select name="ms_ps_settings[products][sku_source]">
                                <option value="code" <?php selected($skuSource, 'code'); ?>>code</option>
                                <option value="article" <?php selected($skuSource, 'article'); ?>>article</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Тип цены (priceTypeId в МС)</th>
                        <td>
                            <input type="text" name="ms_ps_settings[products][price_type_id]" value="<?php echo $priceTypeId; ?>" class="regular-text"/>
                            <p class="description">Если пусто — берём первую цену из <code>salePrices</code>.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Категории (productFolder → product_cat)</th>
                        <td><label><input type="checkbox" name="ms_ps_settings[categories][enabled]" value="1" <?php checked($categoriesEnabled); ?>/> Включить</label></td>
                    </tr>
                    <tr>
                        <th scope="row">Атрибуты (attributes/characteristics)</th>
                        <td><label><input type="checkbox" name="ms_ps_settings[attributes][enabled]" value="1" <?php checked($attributesEnabled); ?>/> Включить</label></td>
                    </tr>
                </table>

                <h3>Склады</h3>
                <p class="description">Список складов подтянется после сохранения логина/пароля. Выберите склады для фильтра ассортимента и отчёта остатков.</p>
                <div style="max-height: 220px; overflow:auto; border:1px solid #ccd0d4; padding: 10px; background: #fff;">
                    <?php
                    $stores = self::getStoresForSettings($settings);
                    $storesArr = is_array($stores) ? $stores : array();
                    if (is_wp_error($stores)) {
                        echo '<p style="color:#b32d2e;">' . esc_html($stores->get_error_message()) . '</p>';
                    } elseif (empty($storesArr)) {
                        echo '<p>Склады не загружены.</p>';
                    } else {
                        foreach ($storesArr as $s) {
                            $id = isset($s['id']) ? (string) $s['id'] : '';
                            $name = isset($s['name']) ? (string) $s['name'] : '';
                            if ($id === '') {
                                continue;
                            }
                            $checked = in_array($id, $storeIds, true);
                            echo '<label style="display:block; margin:4px 0;">';
                            echo '<input type="checkbox" name="ms_ps_settings[store_ids][]" value="' . esc_attr($id) . '" ' . checked($checked, true, false) . ' /> ';
                            echo esc_html($name ?: $id);
                            echo '</label>';
                        }
                    }
                    ?>
                </div>

                <h3>Остатки и наличие</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Режим остатков</th>
                        <td>
                            <select name="ms_ps_settings[stock][mode]">
                                <option value="stock_quantity" <?php selected($stockMode, 'stock_quantity'); ?>>Передача количества</option>
                                <option value="stock_status" <?php selected($stockMode, 'stock_status'); ?>>Только в наличии/нет</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Мета «в магазине»</th>
                        <td><input type="text" name="ms_ps_settings[stock][meta_key_in_shop]" value="<?php echo $metaShop; ?>" class="regular-text"/></td>
                    </tr>
                    <tr>
                        <th scope="row">Мета «под заказ»</th>
                        <td><input type="text" name="ms_ps_settings[stock][meta_key_preorder]" value="<?php echo $metaPre; ?>" class="regular-text"/></td>
                    </tr>
                    <tr>
                        <th scope="row">Цена 0 → черновик</th>
                        <td><label><input type="checkbox" name="ms_ps_settings[stock][price_zero_to_draft]" value="1" <?php checked($priceZeroToDraft); ?>/> Включить</label></td>
                    </tr>
                </table>

                <h4>Роли складов (логика наличия/мета)</h4>
                <p class="description">Для каждого выбранного склада назначьте роль: учитываем в остатке / «в магазине» / «под заказ» / «материальный».</p>
                <table class="widefat" style="max-width: 900px;">
                    <thead>
                    <tr><th>Склад</th><th>Роль</th></tr>
                    </thead>
                    <tbody>
                    <?php
                    $roles = (isset($settings['stock']['store_roles']) && is_array($settings['stock']['store_roles'])) ? (array) $settings['stock']['store_roles'] : array();
                    foreach ((array) $storesArr as $s) {
                        $id = isset($s['id']) ? (string) $s['id'] : '';
                        if ($id === '' || !in_array($id, $storeIds, true)) {
                            continue;
                        }
                        $name = isset($s['name']) ? (string) $s['name'] : $id;
                        $role = isset($roles[$id]) ? (string) $roles[$id] : 'total';
                        echo '<tr>';
                        echo '<td>' . esc_html($name) . '</td>';
                        echo '<td>';
                        echo '<select name="ms_ps_settings[stock][store_roles][' . esc_attr($id) . ']">';
                        echo '<option value="total" ' . selected($role, 'total', false) . '>Учитывать в общем остатке</option>';
                        echo '<option value="in_shop" ' . selected($role, 'in_shop', false) . '>В магазине</option>';
                        echo '<option value="preorder" ' . selected($role, 'preorder', false) . '>Под заказ</option>';
                        echo '<option value="material" ' . selected($role, 'material', false) . '>Материальный/приёмка</option>';
                        echo '</select>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>

                <h3>Заказы → МойСклад</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Включить выгрузку заказов</th>
                        <td><label><input type="checkbox" name="ms_ps_settings[orders][enabled]" value="1" <?php checked($ordersEnabled); ?>/> Включить</label></td>
                    </tr>
                    <tr>
                        <th scope="row">Отправление заказов</th>
                        <td>
                            <select name="ms_ps_settings[orders][send_mode]">
                                <option value="auto" <?php selected($sendMode, 'auto'); ?>>Автоматически при заказе</option>
                                <option value="autotime" <?php selected($sendMode, 'autotime'); ?>>Автоматически по расписанию</option>
                                <option value="manual" <?php selected($sendMode, 'manual'); ?>>Только вручную</option>
                            </select>
                            <div style="margin-top:6px;">
                                <label>Минут до отправки:
                                    <input type="number" min="1" max="1440" name="ms_ps_settings[orders][auto_minutes]" value="<?php echo (int) $autoMin; ?>"/>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Организация (ID в МС)</th>
                        <td><input type="text" name="ms_ps_settings[orders][organization_id]" value="<?php echo $orgId; ?>" class="regular-text"/></td>
                    </tr>
                    <tr>
                        <th scope="row">Склад заказа (ID в МС)</th>
                        <td><input type="text" name="ms_ps_settings[orders][store_id]" value="<?php echo $orderStoreId; ?>" class="regular-text"/></td>
                    </tr>
                    <tr>
                        <th scope="row">Контрагент (counterparty_id)</th>
                        <td>
                            <input type="text" name="ms_ps_settings[orders][counterparty_id]" value="<?php echo $counterpartyId; ?>" class="regular-text"/>
                            <p class="description">Если пусто — плагин попробует найти/создать контрагента по email/телефону/имени заказа.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Теги контрагента (через запятую)</th>
                        <td>
                            <input type="text" name="ms_ps_settings[orders][counterparty_tags]" value="<?php echo $counterpartyTags; ?>" class="regular-text"/>
                            <p class="description">Пример: <code>Сайт, Покупатели, Интернет</code></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Префикс/постфикс</th>
                        <td>
                            <input type="text" name="ms_ps_settings[orders][prefix]" value="<?php echo $prefix; ?>" style="width:120px;"/> +
                            <span># заказа</span> +
                            <input type="text" name="ms_ps_settings[orders][postfix]" value="<?php echo $postfix; ?>" style="width:120px;"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Резерв</th>
                        <td><label><input type="checkbox" name="ms_ps_settings[orders][reserve]" value="1" <?php checked($reserve); ?>/> Включить резерв в позициях</label></td>
                    </tr>
                    <tr>
                        <th scope="row">Доставка (SKU товара/услуги в МС)</th>
                        <td><input type="text" name="ms_ps_settings[orders][delivery_sku]" value="<?php echo $deliverySku; ?>" class="regular-text"/></td>
                    </tr>
                    <tr>
                        <th scope="row">Синхронизировать статус в МС при смене статуса на сайте</th>
                        <td><label><input type="checkbox" name="ms_ps_settings[orders][sync_status_to_ms]" value="1" <?php checked($syncStatus); ?>/> Включить</label></td>
                    </tr>
                    <tr>
                        <th scope="row">Статус МС при успешной оплате (stateId)</th>
                        <td><input type="text" name="ms_ps_settings[orders][ms_state_on_payment]" value="<?php echo $stateOnPayment; ?>" class="regular-text"/></td>
                    </tr>
                </table>

                <p><button type="submit" class="button button-primary">Сохранить настройки</button></p>
            </form>

            <hr/>

            <h2>Логи</h2>
            <form method="get" action="">
                <input type="hidden" name="page" value="ms-products-sync"/>
                <input type="date" name="log_date" value="<?php echo esc_attr($logDate); ?>"/>
                <button class="button">Загрузить</button>
                <button class="button" type="button" id="msps-clear-log">Очистить</button>
            </form>
            <div style="margin-top:10px; background:#fff; border:1px solid #ccd0d4; padding:10px; max-height: 380px; overflow:auto;">
                <?php
                if (empty($logs)) {
                    echo '<p>Нет данных</p>';
                } else {
                    foreach ($logs as $line) {
                        echo '<p style="margin:0 0 6px 0;">' . esc_html($line) . '</p>';
                    }
                }
                ?>
            </div>
        </div>

        <script>
          (function () {
            const startBtn = document.getElementById('msps-start');
            const stopBtn = document.getElementById('msps-stop');
            const jobEl = document.getElementById('msps-job');
            const clearBtn = document.getElementById('msps-clear-log');

            function ajax(action, data) {
              const form = new FormData();
              form.append('action', action);
              form.append('nonce', '<?php echo esc_js(wp_create_nonce('ms_ps_ajax')); ?>');
              Object.keys(data || {}).forEach(k => form.append(k, data[k]));
              return fetch(ajaxurl, { method: 'POST', body: form }).then(r => r.json());
            }

            let currentJobId = <?php echo $job ? (int)$job['id'] : 0; ?>;

            function renderJob(job) {
              if (!job) {
                jobEl.textContent = '';
                stopBtn.disabled = true;
                return;
              }
              currentJobId = job.id;
              stopBtn.disabled = job.status !== 'running';
              jobEl.textContent = `job#${job.id} status=${job.status} phase=${job.phase} offset=${job.offset}:${job.cursor} processed=${job.processed}` + (job.total ? `/${job.total}` : '');
            }

            function poll() {
              if (!currentJobId) return;
              ajax('ms_ps_get_job', { job_id: currentJobId }).then(res => {
                if (res && res.success) {
                  renderJob(res.data.job);
                  if (res.data.job && res.data.job.status === 'running') {
                    setTimeout(poll, 2000);
                  }
                }
              }).catch(() => {});
            }

            startBtn.addEventListener('click', function () {
              startBtn.disabled = true;
              ajax('ms_ps_start_sync', {}).then(res => {
                startBtn.disabled = false;
                if (res && res.success) {
                  renderJob(res.data.job);
                  poll();
                } else {
                  alert(res && res.data && res.data.message ? res.data.message : 'Ошибка запуска');
                }
              }).catch(() => { startBtn.disabled = false; });
            });

            stopBtn.addEventListener('click', function () {
              if (!currentJobId) return;
              ajax('ms_ps_stop_sync', { job_id: currentJobId }).then(() => poll());
            });

            clearBtn.addEventListener('click', function () {
              ajax('ms_ps_clear_log', { date: '<?php echo esc_js($logDate); ?>' }).then(() => location.reload());
            });

            if (currentJobId) {
              poll();
            }
          })();
        </script>
        <?php
    }

    private static function getStoresForSettings(array $settings)
    {
        $api = MsPs_Api::fromSettings($settings);
        if (!$api->isConfigured()) {
            return new WP_Error('ms_ps_no_auth', 'Сначала сохраните логин/пароль МойСклад.');
        }

        $cacheKey = 'ms_ps_stores_cache';
        $cached = get_transient($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $stores = [];
        $offset = 0;
        $limit = 100;
        for ($i = 0; $i < 20; $i++) {
            $res = $api->getStores($limit, $offset);
            if (is_wp_error($res)) {
                return $res;
            }
            $rows = (isset($res['rows']) && is_array($res['rows'])) ? (array) $res['rows'] : array();
            foreach ($rows as $r) {
                $stores[] = $r;
            }
            if (count($rows) < $limit) {
                break;
            }
            $offset += $limit;
        }

        set_transient($cacheKey, $stores, 10 * 60);
        return $stores;
    }

    public static function saveSettings()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Forbidden');
        }
        self::checkNonce();

        $incoming = isset($_POST['ms_ps_settings']) ? (array) $_POST['ms_ps_settings'] : array();

        // Минимальная санитизация.
        $settings = (array) get_option(MS_PS_OPTION, []);
        $settings['auth']['login'] = sanitize_text_field(isset($incoming['auth']['login']) ? (string) $incoming['auth']['login'] : '');
        $settings['auth']['password'] = isset($incoming['auth']['password']) ? (string) $incoming['auth']['password'] : '';

        $settings['products']['page_limit'] = (int) (isset($incoming['products']['page_limit']) ? $incoming['products']['page_limit'] : 100);
        $settings['products']['step_time_limit_sec'] = (int) (isset($incoming['products']['step_time_limit_sec']) ? $incoming['products']['step_time_limit_sec'] : 20);
        $settings['products']['new_post_status'] = sanitize_text_field(isset($incoming['products']['new_post_status']) ? (string) $incoming['products']['new_post_status'] : 'publish');
        $settings['products']['sku_source'] = sanitize_text_field(isset($incoming['products']['sku_source']) ? (string) $incoming['products']['sku_source'] : 'code');
        $settings['products']['price_type_id'] = sanitize_text_field(isset($incoming['products']['price_type_id']) ? (string) $incoming['products']['price_type_id'] : '');

        $incomingStoreIds = isset($incoming['store_ids']) ? (array) $incoming['store_ids'] : array();
        $settings['store_ids'] = array_values(array_unique(array_filter(array_map('sanitize_text_field', (array) $incomingStoreIds))));

        $settings['categories']['enabled'] = !empty($incoming['categories']['enabled']) ? 1 : 0;
        $settings['attributes']['enabled'] = !empty($incoming['attributes']['enabled']) ? 1 : 0;

        $settings['stock']['mode'] = sanitize_text_field(isset($incoming['stock']['mode']) ? (string) $incoming['stock']['mode'] : 'stock_quantity');
        $settings['stock']['meta_key_in_shop'] = sanitize_key(isset($incoming['stock']['meta_key_in_shop']) ? (string) $incoming['stock']['meta_key_in_shop'] : 'v_magazine');
        $settings['stock']['meta_key_preorder'] = sanitize_key(isset($incoming['stock']['meta_key_preorder']) ? (string) $incoming['stock']['meta_key_preorder'] : 'pod_zakaz');
        $settings['stock']['price_zero_to_draft'] = !empty($incoming['stock']['price_zero_to_draft']) ? 1 : 0;
        $settings['stock']['store_roles'] = isset($incoming['stock']['store_roles']) ? (array) $incoming['stock']['store_roles'] : array();
        foreach ($settings['stock']['store_roles'] as $k => $v) {
            $settings['stock']['store_roles'][sanitize_text_field((string) $k)] = sanitize_text_field((string) $v);
        }

        $settings['orders']['enabled'] = !empty($incoming['orders']['enabled']) ? 1 : 0;
        $settings['orders']['send_mode'] = sanitize_text_field(isset($incoming['orders']['send_mode']) ? (string) $incoming['orders']['send_mode'] : 'auto');
        $settings['orders']['auto_minutes'] = (int) (isset($incoming['orders']['auto_minutes']) ? $incoming['orders']['auto_minutes'] : 5);
        $settings['orders']['organization_id'] = sanitize_text_field(isset($incoming['orders']['organization_id']) ? (string) $incoming['orders']['organization_id'] : '');
        $settings['orders']['store_id'] = sanitize_text_field(isset($incoming['orders']['store_id']) ? (string) $incoming['orders']['store_id'] : '');
        $settings['orders']['counterparty_id'] = sanitize_text_field(isset($incoming['orders']['counterparty_id']) ? (string) $incoming['orders']['counterparty_id'] : '');
        $settings['orders']['counterparty_tags'] = sanitize_text_field(isset($incoming['orders']['counterparty_tags']) ? (string) $incoming['orders']['counterparty_tags'] : '');
        $settings['orders']['prefix'] = sanitize_text_field(isset($incoming['orders']['prefix']) ? (string) $incoming['orders']['prefix'] : 'wc');
        $settings['orders']['postfix'] = sanitize_text_field(isset($incoming['orders']['postfix']) ? (string) $incoming['orders']['postfix'] : '');
        $settings['orders']['reserve'] = !empty($incoming['orders']['reserve']) ? 1 : 0;
        $settings['orders']['delivery_sku'] = sanitize_text_field(isset($incoming['orders']['delivery_sku']) ? (string) $incoming['orders']['delivery_sku'] : '');
        $settings['orders']['sync_status_to_ms'] = !empty($incoming['orders']['sync_status_to_ms']) ? 1 : 0;
        $settings['orders']['ms_state_on_payment'] = sanitize_text_field(isset($incoming['orders']['ms_state_on_payment']) ? (string) $incoming['orders']['ms_state_on_payment'] : '');

        update_option(MS_PS_OPTION, $settings, false);
        delete_transient('ms_ps_stores_cache');

        wp_redirect(admin_url('admin.php?page=ms-products-sync&updated=1'));
        exit;
    }

    private static function checkAjax()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'Forbidden'], 403);
        }
        $nonce = isset($_POST['nonce']) ? (string) $_POST['nonce'] : '';
        if (!wp_verify_nonce($nonce, 'ms_ps_ajax')) {
            wp_send_json_error(['message' => 'Bad nonce'], 400);
        }
    }

    public static function ajaxStartSync()
    {
        self::checkAjax();
        $jobId = MsPs_Sync::scheduleStart();
        $job = MsPs_Jobs::get($jobId);

        // Попробуем сразу выполнить первый due action (чтобы стартовал без крона).
        if (class_exists('ActionScheduler') && method_exists('ActionScheduler', 'runner')) {
            try {
                ActionScheduler::runner()->run();
            } catch (\Exception $e) {
                // ignore
            }
        }

        wp_send_json_success(['job' => self::normalizeJob($job)]);
    }

    public static function ajaxStopSync()
    {
        self::checkAjax();
        $jobId = isset($_POST['job_id']) ? (int) $_POST['job_id'] : 0;
        if ($jobId > 0) {
            MsPs_Sync::stop($jobId);
        }
        $job = MsPs_Jobs::get($jobId);
        wp_send_json_success(['job' => self::normalizeJob($job)]);
    }

    public static function ajaxGetJob()
    {
        self::checkAjax();
        $jobId = isset($_POST['job_id']) ? (int) $_POST['job_id'] : 0;
        $job = $jobId > 0 ? MsPs_Jobs::get($jobId) : null;
        wp_send_json_success(['job' => self::normalizeJob($job)]);
    }

    public static function ajaxClearLog()
    {
        self::checkAjax();
        $date = sanitize_text_field(isset($_POST['date']) ? (string) $_POST['date'] : current_time('Y-m-d'));
        MsPs_Log::clear($date);
        wp_send_json_success(['ok' => true]);
    }

    private static function normalizeJob($job)
    {
        if (!$job) {
            return null;
        }
        return [
            'id' => (int) $job['id'],
            'status' => (string) $job['status'],
            'phase' => (string) $job['phase'],
            'offset' => (int) $job['offset'],
            'cursor' => isset($job['cursor']) ? (int) $job['cursor'] : 0,
            'processed' => (int) $job['processed'],
            'total' => $job['total'] !== null ? (int) $job['total'] : null,
            'message' => isset($job['message']) ? (string) $job['message'] : '',
            'last_error' => isset($job['last_error']) ? (string) $job['last_error'] : '',
        ];
    }
}

