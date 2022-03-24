<?php

namespace Translationmanager\SystemStatus\Data;

use Translationmanager\SystemStatus\Item\Item;

class Php implements Information
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
        $this->title = esc_html__('PHP Environment', 'translationmanager');
    }

    public function title(): string
    {
        return $this->title;
    }

    public function collection(): array
    {
        return $this->collection;
    }

    public function serverSoftware(): void
    {
        $serverInfo = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : ''; // phpcs:ignore

        if (! $serverInfo) {
            return;
        }

        $this->collection['server_info'] = new Item(
            esc_html__('Server Info', 'translationmanager'),
            sanitize_text_field($serverInfo)
        );
    }

    public function phpVersion(): void
    {
        $this->collection['php_version'] = new Item(
            esc_html__('PHP Version', 'translationmanager'),
            PHP_VERSION
        );
    }

    public function maxExecutionTime(): void
    {
        $this->collection['max_execution_time'] = new Item(
            esc_html__('Max Execution Time', 'translationmanager'),
            (string) ini_get('max_execution_time')
        );
    }

    public function maxInputVars(): void
    {
        $this->collection['max_input_vars'] = new Item(
            esc_html__('Max Input Vars', 'translationmanager'),
            (string) ini_get('max_input_vars')
        );
    }

    public function postMaxSize(): void
    {
        $this->collection['post_max_size'] = new Item(
            esc_html__('Post Max Size', 'translationmanager'),
            (string) ini_get('post_max_size')
        );
    }

    public function loadedExtentions(): void
    {
        $this->collection['loaded_extentions'] = new Item(
            esc_html__('Php Loaded Extentions', 'translationmanager'),
            trim(implode(', ', get_loaded_extensions()))
        );
    }
}
