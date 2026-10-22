<?php

if (!defined('ABSPATH')) {
    exit;
}

final class MsPs_Log
{
    public static function dir()
    {
        $dir = trailingslashit(WP_CONTENT_DIR) . 'ms-products-sync/logs/';
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
        return $dir;
    }

    public static function filename($date = null)
    {
        if (!$date) {
            $date = current_time('Y-m-d');
        }
        return self::dir() . $date . '.txt';
    }

    public static function log($message, $context = array())
    {
        if (is_array($message) || is_object($message)) {
            $message = wp_json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        if (!empty($context)) {
            $message .= ' ' . wp_json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $line = current_time('d-m-Y H:i:s') . ' ' . (string) $message;

        $file = self::filename();
        $handle = @fopen($file, 'ab');
        if (!$handle) {
            return;
        }
        @fwrite($handle, $line . "%\r\n");
        @fclose($handle);
    }

    public static function read($date)
    {
        $file = self::filename($date);
        $raw = @file_get_contents($file);
        if (!$raw) {
            return [];
        }
        $parts = explode('%', $raw);
        $out = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p !== '') {
                $out[] = $p;
            }
        }
        return $out;
    }

    public static function clear($date = null)
    {
        if (!$date) {
            $date = current_time('Y-m-d');
        }
        $file = self::filename($date);
        if (file_exists($file)) {
            @unlink($file);
        }
    }
}

