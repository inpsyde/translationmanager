<?php

namespace Translationmanager\SystemStatus\Data;

use Translationmanager\SystemStatus\Item\Item;

class Php implements Information
{
    private $collection = [];

    private $title;

    public function __construct()
    {
        $this->title = esc_html__('PHP Environment', 'systemstatus');
    }

    public function title()
    {
        return $this->title;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function serverSoftware()
    {
        $serverInfo = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : ''; // phpcs:ignore

        if (! $serverInfo) {
            return;
        }

        $this->collection['server_info'] = new Item(
            esc_html__('Server Info', 'systemstatus'),
            sanitize_text_field($serverInfo)
        );
    }

    public function phpVersion()
    {
        $this->collection['php_version'] = new Item(
            esc_html__('PHP Version', 'systemstatus'),
            PHP_VERSION
        );
    }

    public function maxExecutionTime()
    {
        $this->collection['max_execution_time'] = new Item(
            esc_html__('Max Execution Time', 'systemstatus'),
            ini_get('max_execution_time')
        );
    }

    public function maxInputVars()
    {
        $this->collection['max_input_vars'] = new Item(
            esc_html__('Max Input Vars', 'systemstatus'),
            ini_get('max_input_vars')
        );
    }

    public function postMaxSize()
    {
        $this->collection['post_max_size'] = new Item(
            esc_html__('Post Max Size', 'systemstatus'),
            ini_get('post_max_size')
        );
    }

    public function loadedExtentions()
    {
        $this->collection['loaded_extentions'] = new Item(
            esc_html__('Php Loaded Extentions', 'systemstatus'),
            trim(implode(', ', get_loaded_extensions()))
        );
    }
}
