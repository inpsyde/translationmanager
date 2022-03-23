<?php

namespace Translationmanager\SystemStatus\Assets;

class Styles
{
    private $assetsUrl;

    public function __construct($assetsUrl)
    {
        $this->assetsUrl = $assetsUrl;
    }

    public function init()
    {
        add_action('admin_enqueue_scripts', [$this, 'register']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue'], 20);
    }

    public function register()
    {
        wp_register_style(
            'main',
            untrailingslashit($this->assetsUrl) . '/system-status.css',
            [],
            false,
            'screen'
        );
    }

    public function enqueue()
    {
        if (wp_style_is('main', 'registered')) {
            wp_enqueue_style('main');
        }
    }
}
