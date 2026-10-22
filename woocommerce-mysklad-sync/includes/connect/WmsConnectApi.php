<?php
if (!defined('ABSPATH'))
    exit;

/**
 * Class WmsConnectApi
 */
class WmsConnectApi
{
    /**
     * @var
     */
    private $password = false;
    /**
     * @var bool
     */
    private $login = false;
    /**
     * @var
     */
    private static $instance;


    /**
     * WmsConnectApi constructor.
     *
     */
    private function __construct()
    {
        $option = $this->get_connect_option();
        $this->set_api_password($option['wms_pass']);
        $this->set_api_login($option['wms_login']);
    }

    /**
     * Возвращает экземпляр себя
     *
     * @return self
     */
    public static function get_instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function __clone()
    {
        throw new \Exception("Cannot clone a singleton.");
    }

    /**
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * @param null $url
     * @param string $type GET POST PUT DELETE
     * @param null $databody
     * @param bool $block
     * @return bool|mixed
     * @throws Exception
     */
    public function send_request($url = null, $type = 'GET', $databody = null, $block = true)
    {
        // wmsLogs::set_logs('=== WmsConnectApi::send_request() НАЧАЛО ===', true);
        // // wmsLogs::set_logs('URL: ' . $url, true);
        // wmsLogs::set_logs('Type: ' . $type . ', Block: ' . ($block ? 'true' : 'false'), true);

        $requestStartTime = microtime(true);

        if ($databody != null) {
            $databody = json_encode($databody, JSON_UNESCAPED_UNICODE);
        }

        $data = $this->get_connect_api($url, $type, $databody, $block);

        $requestEndTime = microtime(true);
        $requestExecutionTime = round($requestEndTime - $requestStartTime, 2);
        WmsLogs::set_logs('get_connect_api() завершен за ' . $requestExecutionTime . ' сек', true);

        if ($data === false) {
            WmsLogs::set_logs('=== WmsConnectApi::send_request() ВЕРНУЛ false ===', true);
            return false;
        }

        $data = json_decode($data, true);

        if (isset($data['errors'])) {
            WmsLogs::set_logs('Ошибка API: ' . $data['errors'][0]['error'], true);
            WmsLogs::set_logs('=== WmsConnectApi::send_request() ОШИБКА ===', true);
            return false;
        }

        // wmsLogs::set_logs('=== WmsConnectApi::send_request() УСПЕШНО завершен ===', true);
        return $data;

    }


    /**
     * @return array
     */
    private function get_connect_option()
    {
        $option = get_option('wms_settings_auth');

        if (!is_array($option)) {
            $option = [
                'wms_pass' => '1',
                'wms_login' => '1@1.ru'
            ];
        }

        return $option;
    }


    /**
     * @param $url
     * @param string $type GET, POST, PUT, DELETE
     * @param null $databody
     * @param bool $block
     * @param int $redirection
     * @param bool $is_auth
     * @return mixed
     * @throws Exception
     */
    public function get_connect_api($url, $type = 'GET', $databody = null, $block = true, $redirection = 5, $is_auth = true)
    {
        // wmsLogs::set_logs('=== get_connect_api() НАЧАЛО ===', true);

        $curlConnectTime = apply_filters('wcstores_ms_check_curl_connect_time', MINUTE_IN_SECONDS * 3);
        // wmsLogs::set_logs('curlConnectTime: ' . $curlConnectTime . ' сек', true);

        if (apply_filters('wcstores_ms_is_block_connect', $block) && !$this->is_timeout($url, $curlConnectTime)) {
            WmsLogs::set_logs('Запрос заблокирован из-за таймаута', true);
            return false;
        }

        $url = $this->clearUrl($url);
        $headers = $this->get_connect_api_headers($is_auth);

        if ($headers === false) {
            WmsLogs::set_logs('Ошибка: не удалось получить заголовки авторизации', true);
            return false;
        }

        $timeout = apply_filters('wms_get_connect_api_timeout', 120);
        // wmsLogs::set_logs('Таймаут запроса: ' . $timeout . ' сек', true);

        $default_args = array(
            'method' => $type,
            'timeout' => $timeout,
            'headers' => $headers,
            'decompress' => true,
            'sslverify' => false,
            'redirection' => $redirection
        );

        if ($type !== 'GET') {
            $default_args['body'] = $databody;
        }

        // wmsLogs::set_logs('Отправляем wp_remote_request() в ' . date('H:i:s'), true);
        $wpRequestStartTime = microtime(true);

        $response = wp_remote_request($url, $default_args);

        $wpRequestEndTime = microtime(true);
        $wpRequestExecutionTime = round($wpRequestEndTime - $wpRequestStartTime, 2);
        // wmsLogs::set_logs('wp_remote_request() завершен за ' . $wpRequestExecutionTime . ' сек в ' . date('H:i:s'), true);

        do_action('wms_get_curl_info', $response);

        if (is_wp_error($response)) {
            $errorMessage = $response->get_error_message();
            WmsLogs::set_logs('ОШИБКА wp_remote_request: ' . $errorMessage, true);
            set_transient('wcstores_ms_check_curl_connect', time(), $curlConnectTime);
            throw new \Exception('Попытка подключения к ' . $url . ' провалилась из за ошибок ' . 'Ошибка curl: ' . $errorMessage);

        } elseif (wp_remote_retrieve_response_code($response) > 300 && wp_remote_retrieve_response_code($response) < 399) {
            $responseCode = wp_remote_retrieve_response_code($response);
            // wmsLogs::set_logs('Редирект: код ответа ' . $responseCode, true);

            $response_headers = wp_remote_retrieve_headers($response);

            if (isset($response_headers['location'])) {
                return $response_headers['location'];
            }

            return false;

        } elseif (wp_remote_retrieve_response_code($response) > 299) {
            $responseCode = wp_remote_retrieve_response_code($response);
            $responseBody = wp_remote_retrieve_body($response);
            WmsLogs::set_logs('ОШИБКА HTTP: код ' . $responseCode, true);
            // WmsLogs::set_logs('URL: ' . $url, true);
            WmsLogs::set_logs('Ответ: ' . substr($responseBody, 0, 500), true);

            return false;
        }

        $responseCode = wp_remote_retrieve_response_code($response);
        $responseBody = wp_remote_retrieve_body($response);
        $responseSize = strlen($responseBody);

        // wmsLogs::set_logs('Успешный ответ: код ' . $responseCode . ', размер ' . round($responseSize / 1024, 2) . ' KB', true);

        delete_transient('wcstores_ms_check_curl_connect');

        // wmsLogs::set_logs('=== get_connect_api() УСПЕШНО завершен ===', true);
        return $responseBody;

    }

    /**
     * @param $url
     * @param $curlConnectTime
     * @return false
     */
    private function is_timeout($url, $curlConnectTime)
    {
        if (!$timeoutError = get_transient('wcstores_ms_check_curl_connect')) {
            return true;
        }

        $timeHasPassed = time() - $timeoutError;

        if ($timeHasPassed % 5 == 0) {
            WmsLogs::set_logs('Попытка подключения к ' . $url . ' Плодключения временно заблокированы из за таймаута, подождите', true);
        }

        return false;

    }

    /**
     * @param $url
     * @return bool|string
     * @throws Exception
     */
    public function download_image($url)
    {
        if ($response = $this->get_connect_api($url, 'GET', null, false, 0, true)) {
            return $this->get_connect_api($response, 'GET', null, false, 0, false);
        }

        return $response;
    }


    /**
     * @param $url
     * @return string|string[]
     */
    private function clearUrl($url)
    {

        $aReplace = array(
            '/1.1/' => '/1.2/',
            '/1.2//' => '/1.2/',
            '/1.2///' => '/1.2/',
            'online.moysklad.ru' => 'api.moysklad.ru',
        );

        foreach ($aReplace as $key => $value) {
            $url = str_replace($key, $value, $url);
        }

        return apply_filters('wcstores_ms_connect_url', $url);

    }


    /**
     * @param bool $is_auth
     * @return array|false
     */
    private function get_connect_api_headers($is_auth = true)
    {
        if (!$is_auth) {
            return array();
        }

        if ($this->login === false or $this->password === false) {
            return false;
        }

        return array(
            "Authorization" => "Basic " . base64_encode($this->login . ":" . $this->password),
            "Content-Type" => "application/json",
        );

    }

    /**
     * @param $password
     */
    function set_api_password($password)
    {
        if (trim($password)) {
            $this->password = $password;
        }


    }

    /**
     * @param $login
     */
    function set_api_login($login)
    {
        if (trim($login)) {
            $this->login = $login;
        }
    }

    /**
     * @param mixed $url
     */
    public function set_url($url)
    {
        $this->url = $url;
    }


}
