<?php

if (!defined('ABSPATH')) {
    exit;
}

final class MsPs_Jobs
{
    public const TABLE = 'ms_ps_jobs';

    public static function tableName()
    {
        global $wpdb;
        return $wpdb->prefix . self::TABLE;
    }

    public static function install()
    {
        global $wpdb;

        $table = self::tableName();
        $charset = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql = "CREATE TABLE {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            job_type VARCHAR(64) NOT NULL,
            status VARCHAR(16) NOT NULL,
            phase VARCHAR(32) NOT NULL DEFAULT 'assortment',
            offset INT(11) NOT NULL DEFAULT 0,
            cursor INT(11) NOT NULL DEFAULT 0,
            page_limit INT(11) NOT NULL DEFAULT 100,
            processed INT(11) NOT NULL DEFAULT 0,
            total INT(11) NULL DEFAULT NULL,
            attempts INT(11) NOT NULL DEFAULT 0,
            last_error LONGTEXT NULL,
            message LONGTEXT NULL,
            created_at DATETIME NOT NULL,
            started_at DATETIME NULL,
            finished_at DATETIME NULL,
            updated_at DATETIME NOT NULL,
            heartbeat_at DATETIME NULL,
            lock_key VARCHAR(64) NULL,
            UNIQUE KEY id (id),
            KEY job_type (job_type),
            KEY status (status),
            KEY phase (phase),
            KEY lock_key (lock_key)
        ) {$charset};";

        dbDelta($sql);
    }

    public static function nowGmt()
    {
        return gmdate('Y-m-d H:i:s');
    }

    public static function create($jobType, $args = array())
    {
        global $wpdb;
        $table = self::tableName();

        $defaults = array(
            'status' => 'running',
            'phase' => 'assortment',
            'offset' => 0,
            'cursor' => 0,
            'page_limit' => 100,
            'processed' => 0,
            'total' => null,
            'attempts' => 0,
            'last_error' => null,
            'message' => null,
            'lock_key' => null,
        );
        $data = array_merge($defaults, $args);

        $now = self::nowGmt();
        $wpdb->insert(
            $table,
            array(
                'job_type' => $jobType,
                'status' => $data['status'],
                'phase' => $data['phase'],
                'offset' => (int) $data['offset'],
                'cursor' => (int) $data['cursor'],
                'page_limit' => (int) $data['page_limit'],
                'processed' => (int) $data['processed'],
                'total' => $data['total'] !== null ? (int) $data['total'] : null,
                'attempts' => (int) $data['attempts'],
                'last_error' => $data['last_error'],
                'message' => $data['message'],
                'created_at' => $now,
                'started_at' => $now,
                'finished_at' => null,
                'updated_at' => $now,
                'heartbeat_at' => $now,
                'lock_key' => $data['lock_key'],
            ),
            array(
                '%s','%s','%s','%d','%d','%d','%d','%d','%d','%s','%s',
                '%s','%s','%s','%s','%s','%s'
            )
        );

        return (int) $wpdb->insert_id;
    }

    public static function get($jobId)
    {
        global $wpdb;
        $table = self::tableName();
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id=%d", (int) $jobId), ARRAY_A);
        return $row ?: null;
    }

    public static function findRunning($jobType)
    {
        global $wpdb;
        $table = self::tableName();
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE job_type=%s AND status=%s ORDER BY id DESC LIMIT 1", (string) $jobType, 'running'),
            ARRAY_A
        );
        return $row ?: null;
    }

    public static function update($jobId, $fields)
    {
        global $wpdb;
        $table = self::tableName();

        $fields['updated_at'] = self::nowGmt();
        $wpdb->update($table, $fields, array('id' => (int) $jobId));
    }

    public static function heartbeat($jobId, $message = null)
    {
        $fields = array(
            'heartbeat_at' => self::nowGmt(),
        );
        if ($message !== null) {
            $fields['message'] = $message;
        }
        self::update($jobId, $fields);
    }

    public static function progress($jobId, $offset, $processed, $total = null)
    {
        $fields = array(
            'offset' => $offset,
            'processed' => $processed,
            'heartbeat_at' => self::nowGmt(),
        );
        if ($total !== null) {
            $fields['total'] = $total;
        }
        self::update($jobId, $fields);
    }

    public static function progressCursor($jobId, $offset, $cursor, $processed, $total = null)
    {
        $fields = array(
            'offset' => $offset,
            'cursor' => $cursor,
            'processed' => $processed,
            'heartbeat_at' => self::nowGmt(),
        );
        if ($total !== null) {
            $fields['total'] = $total;
        }
        self::update($jobId, $fields);
    }

    public static function setPhase($jobId, $phase)
    {
        self::update($jobId, array(
            'phase' => $phase,
            'offset' => 0,
            'cursor' => 0,
        ));
    }

    public static function stop($jobId)
    {
        self::update($jobId, array('status' => 'stopping'));
    }

    public static function fail($jobId, $error)
    {
        self::update($jobId, array(
            'status' => 'failed',
            'last_error' => $error,
            'finished_at' => self::nowGmt(),
        ));
    }

    public static function complete($jobId, $message = null)
    {
        self::update($jobId, array(
            'status' => 'completed',
            'message' => $message,
            'finished_at' => self::nowGmt(),
        ));
    }
}

