<?php

if (!defined('ABSPATH')) {
    exit;
}

final class MsPs_Api
{
    /** @var string */
    private $baseUrl;
    /** @var string */
    private $login;
    /** @var string */
    private $password;

    public function __construct($login, $password, $baseUrl = MS_PS_API_BASE)
    {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
        $this->login = (string) $login;
        $this->password = (string) $password;
    }

    public static function fromSettings($settings = null)
    {
        if ($settings === null) {
            $settings = (array) get_option(MS_PS_OPTION, []);
        }

        $login = '';
        if (isset($settings['auth']['login'])) {
            $login = (string) $settings['auth']['login'];
        }

        $password = '';
        if (isset($settings['auth']['password'])) {
            $password = (string) $settings['auth']['password'];
        }

        return new self($login, $password);
    }

    public function isConfigured()
    {
        return $this->login !== '' && $this->password !== '';
    }

    private function authHeader()
    {
        return 'Basic ' . base64_encode($this->login . ':' . $this->password);
    }

    public function request($method, $pathOrUrl, $query = array(), $body = null, $timeout = 25)
    {
        $pathOrUrl = (string) $pathOrUrl;
        $url = $this->baseUrl . ltrim($pathOrUrl, '/');
        if (strpos($pathOrUrl, 'http://') === 0 || strpos($pathOrUrl, 'https://') === 0) {
            $url = $pathOrUrl;
        }

        if (!empty($query)) {
            $url = add_query_arg($query, $url);
        }

        $args = array(
            'method' => strtoupper((string) $method),
            'timeout' => (int) $timeout,
            'redirection' => 0,
            'headers' => array(
                'Authorization' => $this->authHeader(),
                'Accept' => 'application/json',
            ),
        );

        if ($body !== null) {
            $args['headers']['Content-Type'] = 'application/json; charset=utf-8';
            $args['body'] = is_string($body) ? $body : wp_json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $res = wp_remote_request($url, $args);
        if (is_wp_error($res)) {
            return $res;
        }

        $code = (int) wp_remote_retrieve_response_code($res);
        $raw = (string) wp_remote_retrieve_body($res);
        $data = null;
        if ($raw !== '') {
            $data = json_decode($raw, true);
        }

        if ($code < 200 || $code >= 300) {
            $msg = is_array($data) && isset($data['errors']) ? wp_json_encode($data['errors'], JSON_UNESCAPED_UNICODE) : $raw;
            return new WP_Error('ms_ps_api_http', 'HTTP ' . $code . ': ' . $msg, array('code' => $code, 'url' => $url));
        }

        return $data;
    }

    public function getStores($limit = 100, $offset = 0)
    {
        return $this->request('GET', 'entity/store', array(
            'limit' => $limit,
            'offset' => $offset,
        ));
    }

    public function getAssortment($offset, $limit, $filter, $expand)
    {
        return $this->request('GET', 'entity/assortment', array(
            'offset' => $offset,
            'limit' => $limit,
            'expand' => $expand,
            'filter' => $filter,
        ));
    }

    public function getStockReportAll($offset, $limit, $filter)
    {
        // В API у старого кода встречается /report/stock/all?..., используем без trailing slash.
        return $this->request('GET', 'report/stock/all', array(
            'offset' => $offset,
            'limit' => $limit,
            'filter' => $filter,
        ), null, 40);
    }

    public function createCustomerOrder($payload)
    {
        return $this->request('POST', 'entity/customerorder', array(), $payload, 40);
    }

    public function updateCustomerOrder($id, $payload)
    {
        return $this->request('PUT', 'entity/customerorder/' . rawurlencode((string) $id), array(), $payload, 40);
    }

    public function createCustomerOrderPositions($orderHref, $positions)
    {
        $url = rtrim((string) $orderHref, '/') . '/positions';
        // В старом плагине приводили 1.1→1.2; у нас всегда 1.2, оставляем как есть.
        return $this->request('POST', $url, array(), $positions, 40);
    }

    public function searchAssortmentBySku($sku)
    {
        $sku = trim((string) $sku);
        if ($sku === '') {
            return new WP_Error('ms_ps_sku_empty', 'Empty SKU');
        }
        return $this->request('GET', 'entity/assortment', array(
            'filter' => 'search=' . $sku,
            'limit' => 10,
        ));
    }

    public function searchCounterparty($search, $limit = 10)
    {
        $search = trim((string) $search);
        if ($search === '') {
            return new WP_Error('ms_ps_search_empty', 'Empty search');
        }
        return $this->request('GET', 'entity/counterparty', array(
            'filter' => 'search=' . $search,
            'limit' => (int) $limit,
        ));
    }

    public function createCounterparty($payload)
    {
        return $this->request('POST', 'entity/counterparty', array(), $payload, 40);
    }
}

