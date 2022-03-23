<?php

namespace Translationmanager\SystemStatus\Data;

use Translationmanager\SystemStatus\Item\Item;

class Database implements Information
{
    private $collection = [];

    private $title;

    public function __construct()
    {
        $this->title = esc_html__('Database', 'systemstatus');
    }

    public function title()
    {
        return $this->title;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function mysqlVersion()
    {
        global $wpdb;

        if (! $wpdb) {
            return;
        }

        $this->collection['database_version'] = new Item(
            esc_html__('Database Version', 'systemstatus'),
            $wpdb->db_version()
        );
    }
}
