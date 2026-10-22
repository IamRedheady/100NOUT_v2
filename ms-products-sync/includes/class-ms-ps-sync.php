<?php

if (!defined('ABSPATH')) {
    exit;
}

final class MsPs_Sync
{
    public const JOB_TYPE_PRODUCTS = 'products_sync';

    public const ACTION_START = 'ms_ps_products_sync_start';
    public const ACTION_STEP = 'ms_ps_products_sync_step';

    public static function init()
    {
        add_action(self::ACTION_START, [self::class, 'handleStart'], 10, 1);
        add_action(self::ACTION_STEP, [self::class, 'handleStep'], 10, 1);
    }

    public static function scheduleStart()
    {
        $running = MsPs_Jobs::findRunning(self::JOB_TYPE_PRODUCTS);
        if ($running) {
            return (int) $running['id'];
        }

        $settings = (array) get_option(MS_PS_OPTION, []);
        $pageLimit = 100;
        if (isset($settings['products']['page_limit'])) {
            $pageLimit = (int) $settings['products']['page_limit'];
        }
        if ($pageLimit < 10) {
            $pageLimit = 10;
        }
        if ($pageLimit > 500) {
            $pageLimit = 500;
        }

        $jobId = MsPs_Jobs::create(self::JOB_TYPE_PRODUCTS, [
            'page_limit' => $pageLimit,
            'phase' => 'assortment',
        ]);

        self::enqueueAsync(self::ACTION_START, array('payload' => array('job_id' => $jobId)));

        MsPs_Log::log('Запущен job синхронизации товаров', ['job_id' => $jobId]);
        return $jobId;
    }

    public static function stop($jobId)
    {
        MsPs_Jobs::stop((int) $jobId);
        MsPs_Log::log('Запрошена остановка job', array('job_id' => (int) $jobId));
    }

    private static function enqueueAsync($hook, $args)
    {
        if (function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action($hook, $args, 'ms-products-sync');
            return;
        }
        // Fallback: WP-Cron single event.
        wp_schedule_single_event(time() + 1, $hook, [$args]);
    }

    private static function scheduleSingle($timestamp, $hook, $args)
    {
        if (function_exists('as_schedule_single_action')) {
            as_schedule_single_action($timestamp, $hook, $args, 'ms-products-sync');
            return;
        }
        wp_schedule_single_event($timestamp, $hook, [$args]);
    }

    public static function handleStart($args)
    {
        $payload = is_array($args) ? $args : array();
        if (isset($payload['payload']) && is_array($payload['payload'])) {
            $payload = $payload['payload'];
        }
        $jobId = isset($payload['job_id']) ? (int) $payload['job_id'] : 0;
        if ($jobId <= 0) {
            return;
        }
        $job = MsPs_Jobs::get($jobId);
        if (!$job || $job['status'] !== 'running') {
            return;
        }
        MsPs_Jobs::heartbeat($jobId, 'start');
        self::scheduleSingle(time() + 1, self::ACTION_STEP, array('payload' => array('job_id' => $jobId)));
    }

    public static function handleStep($args)
    {
        $payload = is_array($args) ? $args : array();
        if (isset($payload['payload']) && is_array($payload['payload'])) {
            $payload = $payload['payload'];
        }
        $jobId = isset($payload['job_id']) ? (int) $payload['job_id'] : 0;
        if ($jobId <= 0) {
            return;
        }

        $job = MsPs_Jobs::get($jobId);
        if (!$job) {
            return;
        }

        if ($job['status'] === 'stopping') {
            MsPs_Jobs::complete($jobId, 'stopped');
            MsPs_Log::log('Job остановлен', ['job_id' => $jobId]);
            return;
        }
        if ($job['status'] !== 'running') {
            return;
        }

        $settings = (array) get_option(MS_PS_OPTION, []);
        $api = MsPs_Api::fromSettings($settings);
        if (!$api->isConfigured()) {
            MsPs_Jobs::fail($jobId, 'API не настроен (логин/пароль пустые)');
            MsPs_Log::log('Ошибка: API не настроен', ['job_id' => $jobId]);
            return;
        }

        $timeLimit = 20;
        if (isset($settings['products']['step_time_limit_sec'])) {
            $timeLimit = (int) $settings['products']['step_time_limit_sec'];
        }
        if ($timeLimit < 5) {
            $timeLimit = 5;
        }
        if ($timeLimit > 25) {
            $timeLimit = 25;
        }

        $pageLimit = (int) $job['page_limit'];
        $offset = (int) $job['offset'];
        $cursor = isset($job['cursor']) ? (int) $job['cursor'] : 0;
        $processed = (int) $job['processed'];

        $start = microtime(true);
        MsPs_Jobs::heartbeat($jobId, $job['phase'] . ' offset=' . $offset);

        try {
            if ($job['phase'] === 'assortment') {
                $didMore = self::stepAssortment($api, $settings, $jobId, $offset, $cursor, $pageLimit, $processed, $start, $timeLimit);
                if ($didMore) {
                    self::scheduleSingle(time() + 1, self::ACTION_STEP, array('payload' => array('job_id' => $jobId)));
                }
                return;
            }

            if ($job['phase'] === 'stock') {
                $didMore = self::stepStock($api, $settings, $jobId, $offset, $cursor, $pageLimit, $processed, $start, $timeLimit);
                if ($didMore) {
                    self::scheduleSingle(time() + 1, self::ACTION_STEP, array('payload' => array('job_id' => $jobId)));
                }
                return;
            }

            MsPs_Jobs::fail($jobId, 'Unknown phase: ' . (string) $job['phase']);
        } catch (\Exception $e) {
            MsPs_Jobs::fail($jobId, $e->getMessage());
            MsPs_Log::log('Фатальная ошибка шага', ['job_id' => $jobId, 'error' => $e->getMessage()]);
        }
    }

    private static function stepAssortment($api, $settings, $jobId, $offset, $cursor, $limit, $processed, $start, $timeLimit)
    {
        $storeIds = array();
        if (isset($settings['store_ids']) && is_array($settings['store_ids'])) {
            $storeIds = array_values(array_filter($settings['store_ids'], 'strlen'));
        }
        $storeFilters = [];
        foreach ($storeIds as $id) {
            $storeFilters[] = 'stockStore=' . MS_PS_API_BASE . 'entity/store/' . $id;
        }

        $filterParts = [
            'archived=false',
        ];
        if (!empty($storeFilters)) {
            $filterParts[] = 'stockMode=positiveonly';
            $filterParts = array_merge($filterParts, $storeFilters);
        }
        $filter = implode(';', $filterParts);

        $expand = 'product,country,productFolder,components,images';
        if (isset($settings['products']['expand'])) {
            $expand = (string) $settings['products']['expand'];
        }

        MsPs_Log::log('Assortment: запрос', ['job_id' => $jobId, 'offset' => $offset, 'limit' => $limit]);

        $res = $api->getAssortment($offset, $limit, $filter, $expand);
        if (is_wp_error($res)) {
            $attempts = 0;
            if (isset($settings['runtime']['assortment_attempts'])) {
                $attempts = (int) $settings['runtime']['assortment_attempts'];
            }
            $attempts++;
            $settings['runtime']['assortment_attempts'] = $attempts;
            update_option(MS_PS_OPTION, $settings, false);

            $delay = min(300, (int) pow(2, min(8, $attempts)));
            MsPs_Log::log('Assortment: ошибка API, ретрай', ['job_id' => $jobId, 'delay_sec' => $delay, 'error' => $res->get_error_message()]);
            $errData = $res->get_error_data();
            $prevAttempts = 0;
            if (is_array($errData) && isset($errData['attempts'])) {
                $prevAttempts = (int) $errData['attempts'];
            }
            MsPs_Jobs::update($jobId, array(
                'attempts' => $prevAttempts + 1,
                'last_error' => $res->get_error_message(),
            ));
            self::scheduleSingle(time() + $delay, self::ACTION_STEP, array('payload' => array('job_id' => $jobId)));
            return false;
        }

        $rows = (isset($res['rows']) && is_array($res['rows'])) ? (array) $res['rows'] : array();
        $total = isset($res['meta']['size']) ? (int) $res['meta']['size'] : null;

        if (empty($rows)) {
            MsPs_Log::log('Assortment: конец, переходим к stock', ['job_id' => $jobId, 'processed' => $processed]);
            MsPs_Jobs::setPhase($jobId, 'stock');
            MsPs_Jobs::progressCursor($jobId, 0, 0, $processed, $total);
            return true;
        }

        if ($cursor < 0) {
            $cursor = 0;
        }

        $existingMap = self::prefetchProductIds($rows);
        $count = count($rows);

        $idx = $cursor;
        for (; $idx < $count; $idx++) {
            if ((microtime(true) - $start) > $timeLimit) {
                break;
            }
            self::upsertFromAssortmentRow($rows[$idx], $settings, $existingMap);
            $processed++;
        }

        $isPageDone = ($idx >= $count);
        $isLastPage = ($count < $limit);

        if ($isPageDone && $isLastPage) {
            MsPs_Log::log('Assortment: последняя страница, переходим к stock', ['job_id' => $jobId, 'processed' => $processed]);
            MsPs_Jobs::setPhase($jobId, 'stock');
            MsPs_Jobs::progressCursor($jobId, 0, 0, $processed, $total);
            return true;
        }

        $nextOffset = $isPageDone ? ($offset + $limit) : $offset;
        $nextCursor = $isPageDone ? 0 : $idx;

        MsPs_Jobs::progressCursor($jobId, $nextOffset, $nextCursor, $processed, $total);
        MsPs_Log::log('Assortment: шаг выполнен', ['job_id' => $jobId, 'processed' => $processed, 'offset' => $nextOffset, 'cursor' => $nextCursor, 'total' => $total]);

        return true;
    }

    private static function stepStock($api, $settings, $jobId, $offset, $cursor, $limit, $processed, $start, $timeLimit)
    {
        $storeIds = array();
        if (isset($settings['store_ids']) && is_array($settings['store_ids'])) {
            $storeIds = array_values(array_filter($settings['store_ids'], 'strlen'));
        }
        $storeFilters = [];
        foreach ($storeIds as $id) {
            $storeFilters[] = 'store=' . MS_PS_API_BASE . 'entity/store/' . $id;
        }
        $filterParts = ['stockMode=all'];
        if (!empty($storeFilters)) {
            $filterParts = array_merge($filterParts, $storeFilters);
        }
        $filter = implode(';', $filterParts);

        MsPs_Log::log('Stock: запрос', ['job_id' => $jobId, 'offset' => $offset, 'limit' => $limit]);
        $res = $api->getStockReportAll($offset, $limit, $filter);
        if (is_wp_error($res)) {
            $delay = 30;
            MsPs_Log::log('Stock: ошибка API, ретрай', ['job_id' => $jobId, 'delay_sec' => $delay, 'error' => $res->get_error_message()]);
            self::scheduleSingle(time() + $delay, self::ACTION_STEP, array('payload' => array('job_id' => $jobId)));
            return false;
        }

        $rows = (isset($res['rows']) && is_array($res['rows'])) ? (array) $res['rows'] : array();
        $total = isset($res['meta']['size']) ? (int) $res['meta']['size'] : null;

        if (empty($rows)) {
            MsPs_Jobs::complete($jobId, 'ok');
            MsPs_Log::log('Stock: завершено', ['job_id' => $jobId, 'processed' => $processed]);
            return false;
        }

        if ($cursor < 0) {
            $cursor = 0;
        }

        $stockMap = self::prefetchProductIdsByMsIdsFromStockRows($rows);
        $count = count($rows);

        $idx = $cursor;
        for (; $idx < $count; $idx++) {
            if ((microtime(true) - $start) > $timeLimit) {
                break;
            }
            self::applyStockRow($rows[$idx], $settings, $stockMap);
            $processed++;
        }

        $isPageDone = ($idx >= $count);
        $isLastPage = ($count < $limit);

        if ($isPageDone && $isLastPage) {
            MsPs_Jobs::complete($jobId, 'ok');
            MsPs_Log::log('Stock: завершено (последняя страница)', ['job_id' => $jobId, 'processed' => $processed]);
            return false;
        }

        $nextOffset = $isPageDone ? ($offset + $limit) : $offset;
        $nextCursor = $isPageDone ? 0 : $idx;
        MsPs_Jobs::progressCursor($jobId, $nextOffset, $nextCursor, $processed, $total);
        MsPs_Log::log('Stock: шаг выполнен', ['job_id' => $jobId, 'processed' => $processed, 'offset' => $nextOffset, 'cursor' => $nextCursor, 'total' => $total]);
        return true;
    }

    private static function extractIdFromHref($href)
    {
        if (!$href) {
            return null;
        }
        $href = rtrim($href, '/');
        $parts = explode('/', $href);
        $last = end($parts);
        return $last ?: null;
    }

    private static function findWcProductIdByMsIdentifiers($identifiers)
    {
        $identifiers = array_values(array_filter(array_map('strval', $identifiers), 'strlen'));
        if (empty($identifiers)) {
            return null;
        }
        $metaKeys = ['_id_ms', '_externalCode', 'ms_externalCode', 'ms_article', 'ms_code'];

        foreach ($metaKeys as $key) {
            $products = wc_get_products(array(
                'limit' => 1,
                'return' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => $key,
                        'value' => $identifiers,
                        'compare' => 'IN',
                    ),
                ),
            ));
            if (!empty($products)) {
                return (int) $products[0];
            }
        }

        return null;
    }

    private static function upsertFromAssortmentRow($a, $settings, $existingMap)
    {
        $msId = isset($a['id']) ? (string) $a['id'] : '';
        if ($msId === '') {
            return;
        }

        $externalCode = isset($a['externalCode']) ? (string) $a['externalCode'] : '';
        $article = isset($a['article']) ? (string) $a['article'] : '';
        $code = isset($a['code']) ? (string) $a['code'] : '';

        $productId = self::lookupExistingId($existingMap, $msId, $externalCode, $article, $code);

        // Архивные скрываем (но archived=false в фильтре, на всякий случай).
        if (!empty($a['archived'])) {
            if ($productId) {
                wp_update_post(array('ID' => $productId, 'post_status' => 'draft'));
            }
            return;
        }

        $postStatus = 'publish';
        if (isset($settings['products']['new_post_status'])) {
            $postStatus = (string) $settings['products']['new_post_status'];
        }
        if (!in_array($postStatus, ['publish', 'pending', 'draft'], true)) {
            $postStatus = 'publish';
        }

        $type = 'product';
        if (isset($a['meta']['type'])) {
            $type = (string) $a['meta']['type'];
        }
        if ($type === 'variant') {
            self::upsertVariation($a, $settings, $existingMap);
            return;
        }

        $product = $productId ? wc_get_product($productId) : new WC_Product();
        if (!$product) {
            return;
        }

        $isNew = !$productId;

        $name = isset($a['name']) ? (string) $a['name'] : '';
        if ($name !== '') {
            $product->set_name($name);
        }

        if ($isNew) {
            $product->set_status($postStatus);
        }

        // SKU: настраиваемо (code/article). По умолчанию code.
        $skuSource = 'code';
        if (isset($settings['products']['sku_source'])) {
            $skuSource = (string) $settings['products']['sku_source'];
        }
        $sku = $skuSource === 'article' ? $article : $code;
        if ($sku !== '') {
            $product->set_sku($sku);
        }

        // Meta идентификаторы
        $product->update_meta_data('_id_ms', $msId);
        if ($externalCode !== '') {
            $product->update_meta_data('_externalCode', $externalCode);
            $product->update_meta_data('ms_externalCode', $externalCode);
        }
        if ($article !== '') {
            $product->update_meta_data('ms_article', $article);
        }
        if ($code !== '') {
            $product->update_meta_data('ms_code', $code);
        }
        if (!empty($a['updated'])) {
            $product->update_meta_data('_ms_updated', (string) $a['updated']);
        }

        // Для таксономий нужны term assignments по ID → сначала сохраняем новый товар.
        if ($isNew) {
            $productId = $product->save();
            $product = wc_get_product($productId);
            if (!$product) {
                return;
            }
        }

        self::updatePrices($product, $a, $settings);
        self::updateCategories($product, $a, $settings);
        self::updateAttributes($product, $a, $settings);

        $product->save();
    }

    private static function updatePrices($product, $a, $settings)
    {
        $salePrices = array();
        if (isset($a['salePrices']) && is_array($a['salePrices'])) {
            $salePrices = (array) $a['salePrices'];
        }
        if (empty($salePrices)) {
            return;
        }

        $priceTypeId = isset($settings['products']['price_type_id']) ? (string) $settings['products']['price_type_id'] : '';

        $picked = null;
        foreach ($salePrices as $sp) {
            if (!is_array($sp)) {
                continue;
            }
            if ($priceTypeId !== '') {
                $ptHref = isset($sp['priceType']['meta']['href']) ? (string) $sp['priceType']['meta']['href'] : '';
                $ptId = self::extractIdFromHref($ptHref);
                if ($ptId && $ptId === $priceTypeId) {
                    $picked = $sp;
                    break;
                }
            } else {
                $picked = $sp;
                break;
            }
        }
        if (!$picked) {
            return;
        }
        $value = isset($picked['value']) ? (float) $picked['value'] : 0;
        if ($value <= 0) {
            return;
        }
        $price = $value / 100;
        $product->set_regular_price((string) $price);
        $product->set_price((string) $price);
    }

    private static function updateCategories($product, $a, $settings)
    {
        $enabled = true;
        if (isset($settings['categories']['enabled'])) {
            $enabled = (bool) $settings['categories']['enabled'];
        }
        if (!$enabled) {
            return;
        }

        if (!isset($a['productFolder']) || !is_array($a['productFolder'])) {
            return;
        }

        $pf = (array) $a['productFolder'];
        $pathName = isset($pf['pathName']) ? (string) $pf['pathName'] : '';
        $leafName = isset($pf['name']) ? (string) $pf['name'] : '';
        $leafMsCatId = isset($pf['id']) ? (string) $pf['id'] : '';

        $parts = array();
        if ($pathName !== '') {
            $parts = array_values(array_filter(array_map('trim', explode('/', $pathName)), 'strlen'));
        } elseif ($leafName !== '') {
            $parts = array($leafName);
        }
        if (empty($parts)) {
            return;
        }

        static $termCache = array(); // "parentId|name" => term_id
        static $msCatCache = array(); // ms_cat_id => term_id

        $parent = 0;
        $termId = 0;
        foreach ($parts as $pName) {
            $lower = function_exists('mb_strtolower') ? mb_strtolower($pName) : strtolower($pName);
            $cacheKey = $parent . '|' . $lower;
            if (isset($termCache[$cacheKey])) {
                $termId = (int) $termCache[$cacheKey];
                $parent = $termId;
                continue;
            }

            $found = term_exists($pName, 'product_cat', $parent);
            if (is_array($found) && isset($found['term_id'])) {
                $termId = (int) $found['term_id'];
            } else {
                $created = wp_insert_term($pName, 'product_cat', array('parent' => $parent));
                if (is_wp_error($created) || !isset($created['term_id'])) {
                    return;
                }
                $termId = (int) $created['term_id'];
            }

            $termCache[$cacheKey] = $termId;
            $parent = $termId;
        }

        if ($termId > 0) {
            if ($leafMsCatId !== '') {
                update_term_meta($termId, 'ms_cat_id', $leafMsCatId);
                $msCatCache[$leafMsCatId] = $termId;
            }
            $product->set_category_ids(array($termId));
        }
    }

    private static function updateAttributes($product, $a, $settings)
    {
        $enabled = true;
        if (isset($settings['attributes']['enabled'])) {
            $enabled = (bool) $settings['attributes']['enabled'];
        }
        if (!$enabled) {
            return;
        }

        $attrs = array();
        if (isset($a['attributes']) && is_array($a['attributes'])) {
            $attrs = (array) $a['attributes'];
        }
        if (empty($attrs)) {
            return;
        }

        if (!$product->get_id()) {
            return;
        }

        $productAttributes = array();
        foreach ($attrs as $attr) {
            if (!is_array($attr) || empty($attr['name'])) {
                continue;
            }
            $name = (string) $attr['name'];
            if ($name === 'wms-no') {
                continue;
            }
            $value = '';
            if (isset($attr['value']['name'])) {
                $value = (string) $attr['value']['name'];
            } elseif (isset($attr['value'])) {
                $value = (string) $attr['value'];
            }
            $value = trim($value);
            if ($value === '') {
                continue;
            }

            $taxName = wc_sanitize_taxonomy_name($name);
            $taxonomy = wc_attribute_taxonomy_name($taxName); // pa_xxx

            if (!taxonomy_exists($taxonomy)) {
                $attrId = wc_attribute_taxonomy_id_by_name($taxName);
                if (!$attrId) {
                    $createdId = wc_create_attribute(array(
                        'name' => $name,
                        'slug' => $taxName,
                        'type' => 'select',
                        'order_by' => 'menu_order',
                        'has_archives' => false,
                    ));
                    if (is_wp_error($createdId)) {
                        continue;
                    }
                    delete_transient('wc_attribute_taxonomies');
                }
                register_taxonomy(
                    $taxonomy,
                    apply_filters('woocommerce_taxonomy_objects_' . $taxonomy, array('product')),
                    apply_filters(
                        'woocommerce_taxonomy_args_' . $taxonomy,
                        array(
                            'labels' => array('name' => $name),
                            'hierarchical' => true,
                            'show_ui' => false,
                            'query_var' => true,
                            'rewrite' => false,
                        )
                    )
                );
            }

            $term = term_exists($value, $taxonomy);
            if (!is_array($term)) {
                $term = wp_insert_term($value, $taxonomy);
            }
            if (is_wp_error($term) || !is_array($term) || empty($term['term_id'])) {
                continue;
            }
            $termId = (int) $term['term_id'];
            wp_set_object_terms($product->get_id(), array($termId), $taxonomy, true);

            $pa = new WC_Product_Attribute();
            $pa->set_id(wc_attribute_taxonomy_id_by_name($taxName));
            $pa->set_name($taxonomy);
            $pa->set_options(array($termId));
            $pa->set_visible(true);
            $pa->set_variation(false);
            $productAttributes[] = $pa;
        }

        if (!empty($productAttributes)) {
            $product->set_attributes($productAttributes);
        }
    }

    private static function upsertVariation($a, $settings, $existingMap)
    {
        // Упрощённо: создаём вариацию под родителем product.id (из expand=product)
        if (!isset($a['product']['id'])) {
            return;
        }
        $parentMsId = (string) $a['product']['id'];
        $parentExternal = isset($a['product']['externalCode']) ? (string) $a['product']['externalCode'] : '';
        $parentId = self::lookupExistingId($existingMap, $parentMsId, $parentExternal, '', '');
        if (!$parentId) {
            $parentProduct = new WC_Product_Variable();
            $parentName = isset($a['product']['name']) ? (string) $a['product']['name'] : (isset($a['name']) ? (string) $a['name'] : '');
            if ($parentName !== '') {
                $parentProduct->set_name($parentName);
            }
            $parentProduct->set_status(isset($settings['products']['new_post_status']) ? (string) $settings['products']['new_post_status'] : 'publish');
            $parentProduct->update_meta_data('_id_ms', $parentMsId);
            if ($parentExternal !== '') {
                $parentProduct->update_meta_data('_externalCode', $parentExternal);
            }
            $parentId = $parentProduct->save();
        }

        $variantMsId = isset($a['id']) ? (string) $a['id'] : '';
        if ($variantMsId === '') {
            return;
        }

        // Найти существующую вариацию по _id_ms
        $variationId = self::findWcProductIdByMsIdentifiers(array($variantMsId));
        $variation = $variationId ? wc_get_product($variationId) : new WC_Product_Variation();
        if (!$variation) {
            return;
        }
        $variation->set_parent_id($parentId);
        if (!$variationId) {
            $variation->set_status('publish');
        }

        // SKU
        $article = isset($a['article']) ? (string) $a['article'] : '';
        $code = isset($a['code']) ? (string) $a['code'] : '';
        $skuSource = isset($settings['products']['sku_source']) ? (string) $settings['products']['sku_source'] : 'code';
        $sku = $skuSource === 'article' ? $article : $code;
        if ($sku !== '') {
            $variation->set_sku($sku);
        }

        $variation->update_meta_data('_id_ms', $variantMsId);
        if (isset($a['externalCode'])) {
            $variation->update_meta_data('_externalCode', (string) $a['externalCode']);
        }

        // Атрибуты вариации
        $chars = isset($a['characteristics']) && is_array($a['characteristics']) ? (array) $a['characteristics'] : array();
        $varAttrs = array();
        foreach ($chars as $ch) {
            if (!is_array($ch) || empty($ch['name'])) {
                continue;
            }
            $name = (string) $ch['name'];
            $value = isset($ch['value']) ? (string) $ch['value'] : '';
            $value = trim($value);
            if ($value === '') {
                continue;
            }

            $taxName = wc_sanitize_taxonomy_name($name);
            $taxonomy = wc_attribute_taxonomy_name($taxName);
            if (!taxonomy_exists($taxonomy)) {
                $attrId = wc_attribute_taxonomy_id_by_name($taxName);
                if (!$attrId) {
                    $createdId = wc_create_attribute(array(
                        'name' => $name,
                        'slug' => $taxName,
                        'type' => 'select',
                        'order_by' => 'menu_order',
                        'has_archives' => false,
                    ));
                    if (is_wp_error($createdId)) {
                        continue;
                    }
                    delete_transient('wc_attribute_taxonomies');
                }
            }

            $term = term_exists($value, $taxonomy);
            if (!is_array($term)) {
                $term = wp_insert_term($value, $taxonomy);
            }
            if (is_wp_error($term) || !is_array($term) || empty($term['term_id'])) {
                continue;
            }
            $termObj = get_term((int) $term['term_id'], $taxonomy);
            if ($termObj && !is_wp_error($termObj)) {
                $varAttrs[$taxonomy] = $termObj->slug;
            }
        }
        if (!empty($varAttrs)) {
            $variation->set_attributes($varAttrs);
        }

        self::updatePrices($variation, $a, $settings);
        $variation->save();

        // Обеспечиваем, что у родителя атрибуты отмечены как вариационные
        if (!empty($varAttrs)) {
            $parent = wc_get_product($parentId);
            if ($parent && $parent->is_type('variable')) {
                $parentAttrs = $parent->get_attributes();
                foreach ($varAttrs as $tax => $slug) {
                    if (isset($parentAttrs[$tax])) {
                        $pa = $parentAttrs[$tax];
                        if ($pa instanceof WC_Product_Attribute) {
                            $pa->set_variation(true);
                            $parentAttrs[$tax] = $pa;
                        }
                    } else {
                        $pa = new WC_Product_Attribute();
                        $pa->set_id(wc_attribute_taxonomy_id_by_name(str_replace('pa_', '', $tax)));
                        $pa->set_name($tax);
                        $pa->set_options(array());
                        $pa->set_visible(true);
                        $pa->set_variation(true);
                        $parentAttrs[$tax] = $pa;
                    }
                }
                $parent->set_attributes($parentAttrs);
                $parent->save();
            }
        }
    }

    private static function prefetchProductIds($rows)
    {
        global $wpdb;
        $values = array();
        foreach ((array) $rows as $r) {
            if (!is_array($r)) {
                continue;
            }
            foreach (array('id', 'externalCode', 'article', 'code') as $k) {
                if (isset($r[$k]) && $r[$k] !== '') {
                    $values[] = (string) $r[$k];
                }
            }
            if (isset($r['product']['id']) && $r['product']['id'] !== '') {
                $values[] = (string) $r['product']['id'];
            }
        }
        $values = array_values(array_unique(array_filter($values, 'strlen')));
        if (empty($values)) {
            return array();
        }

        $keys = array('_id_ms', '_externalCode', 'ms_externalCode', 'ms_article', 'ms_code');
        $placeholders = implode(',', array_fill(0, count($values), '%s'));
        $keyPlaceholders = implode(',', array_fill(0, count($keys), '%s'));

        $sql = "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key IN ({$keyPlaceholders}) AND meta_value IN ({$placeholders})";
        $params = array_merge($keys, $values);
        $prepared = $wpdb->prepare($sql, $params);
        $rowsDb = $wpdb->get_results($prepared, ARRAY_A);

        $map = array();
        foreach ((array) $rowsDb as $m) {
            if (!isset($m['meta_value'], $m['post_id'])) {
                continue;
            }
            $val = (string) $m['meta_value'];
            if ($val !== '' && !isset($map[$val])) {
                $map[$val] = (int) $m['post_id'];
            }
        }
        return $map;
    }

    private static function lookupExistingId($existingMap, $msId, $externalCode, $article, $code)
    {
        foreach (array($msId, $externalCode, $article, $code) as $v) {
            if ($v !== '' && isset($existingMap[$v])) {
                return (int) $existingMap[$v];
            }
        }
        return null;
    }

    private static function applyStockRow($row, $settings, $stockMap = null)
    {
        $href = null;
        if (isset($row['meta']['href'])) {
            $href = $row['meta']['href'];
        }
        $msId = self::extractIdFromHref(is_string($href) ? $href : null);
        if (!$msId) {
            return;
        }

        $productId = null;
        if (is_array($stockMap) && isset($stockMap[$msId])) {
            $productId = (int) $stockMap[$msId];
        } else {
            $productId = self::findWcProductIdByMsIdentifiers(array($msId));
        }
        if (!$productId) {
            return;
        }
        $product = wc_get_product($productId);
        if (!$product) {
            return;
        }

        $stockByStore = array();
        if (isset($row['stockByStore']) && is_array($row['stockByStore'])) {
            $stockByStore = (array) $row['stockByStore'];
        }

        $roles = array();
        if (isset($settings['stock']['store_roles']) && is_array($settings['stock']['store_roles'])) {
            $roles = (array) $settings['stock']['store_roles'];
        }
        $metaKeyShop = isset($settings['stock']['meta_key_in_shop']) ? (string) $settings['stock']['meta_key_in_shop'] : 'v_magazine';
        $metaKeyPre = isset($settings['stock']['meta_key_preorder']) ? (string) $settings['stock']['meta_key_preorder'] : 'pod_zakaz';

        $totalQty = 0.0;
        $inShopQty = 0.0;
        $preorderQty = 0.0;
        $materialQty = 0.0;

        foreach ($stockByStore as $s) {
            $storeHref = null;
            if (isset($s['meta']['href'])) {
                $storeHref = $s['meta']['href'];
            } elseif (isset($s['store']['meta']['href'])) {
                $storeHref = $s['store']['meta']['href'];
            }
            $storeId = self::extractIdFromHref(is_string($storeHref) ? $storeHref : null);
            $role = $storeId && isset($roles[$storeId]) ? (string) $roles[$storeId] : 'total';

            $stockVal = isset($s['stock']) ? (float) $s['stock'] : 0.0;
            $reserveVal = isset($s['reserve']) ? (float) $s['reserve'] : 0.0;
            $qty = $stockVal - $reserveVal;
            if ($qty < 0) {
                $qty = 0;
            }

            if ($role === 'in_shop') {
                $inShopQty += $qty;
            } elseif ($role === 'preorder') {
                $preorderQty += $qty;
            } elseif ($role === 'material') {
                $materialQty += $qty;
            } else {
                $totalQty += $qty;
            }
        }

        $mode = 'stock_quantity';
        if (isset($settings['stock']['mode'])) {
            $mode = (string) $settings['stock']['mode'];
        }
        $priceZeroToDraft = !empty($settings['stock']['price_zero_to_draft']);

        $hasSellable = (($inShopQty + $preorderQty) > 0) && ((float) $product->get_price() > 0);
        $onlyMaterial = ($materialQty > 0) && !$hasSellable;

        if ($onlyMaterial || ($priceZeroToDraft && ((float) $product->get_price() <= 0))) {
            wp_update_post(['ID' => $productId, 'post_status' => 'draft']);
        } else {
            wp_update_post(['ID' => $productId, 'post_status' => 'publish']);
        }

        update_post_meta($productId, $metaKeyShop, (float) $inShopQty);
        update_post_meta($productId, $metaKeyPre, (float) $preorderQty);

        if ($mode === 'stock_status') {
            $product->set_manage_stock('no');
            $product->set_stock_status(($totalQty + $inShopQty + $preorderQty) > 0 ? 'instock' : 'outofstock');
        } else {
            $qtyAll = $totalQty + $inShopQty + $preorderQty;
            $product->set_manage_stock('yes');
            $product->set_stock_quantity($qtyAll);
            $product->set_stock_status($qtyAll > 0 ? 'instock' : 'outofstock');
        }

        $product->set_date_modified(time());
        $product->save();
    }

    private static function prefetchProductIdsByMsIdsFromStockRows($rows)
    {
        global $wpdb;
        $msIds = array();
        foreach ((array) $rows as $r) {
            if (!is_array($r)) {
                continue;
            }
            if (isset($r['meta']['href'])) {
                $id = self::extractIdFromHref((string) $r['meta']['href']);
                if ($id) {
                    $msIds[] = $id;
                }
            }
        }
        $msIds = array_values(array_unique(array_filter($msIds, 'strlen')));
        if (empty($msIds)) {
            return array();
        }
        $placeholders = implode(',', array_fill(0, count($msIds), '%s'));
        $sql = "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value IN ({$placeholders})";
        $params = array_merge(array('_id_ms'), $msIds);
        $prepared = $wpdb->prepare($sql, $params);
        $rowsDb = $wpdb->get_results($prepared, ARRAY_A);
        $map = array();
        foreach ((array) $rowsDb as $m) {
            if (!isset($m['meta_value'], $m['post_id'])) {
                continue;
            }
            $val = (string) $m['meta_value'];
            if ($val !== '' && !isset($map[$val])) {
                $map[$val] = (int) $m['post_id'];
            }
        }
        return $map;
    }
}

