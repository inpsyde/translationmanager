<?php

namespace Translationmanager\SystemStatus\Data;

use Translationmanager\SystemStatus\Item\Item;

class Database implements Information
{
    /**
     * @var array
     */
    private $collection = [];

    /**
     * @var string
     */
    private $title;

    public function __construct()
    {
        $this->title = esc_html__('Database', 'translationmanager');
    }

    public function title(): string
    {
        return $this->title;
    }

    public function collection(): array
    {
        return $this->collection;
    }

    public function mysqlVersion(): void
    {
        global $wpdb;

        if (! $wpdb) {
            return;
        }

        $this->collection['database_version'] = new Item(
            esc_html__('Database Version', 'translationmanager'),
            $wpdb->db_version()
        );
    }
}
