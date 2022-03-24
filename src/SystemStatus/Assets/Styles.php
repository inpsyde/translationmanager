<?php

namespace Translationmanager\SystemStatus\Assets;

class Styles
{
    /**
     * @var string
     */
    private $assetsUrl;

    public function __construct(string $assetsUrl)
    {
        $this->assetsUrl = $assetsUrl;
    }

    public function init(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'register']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue'], 20);
    }

    public function register(): void
    {
        // TODO: should retrieve and use the current plugin version
        wp_register_style(
            'main',
            untrailingslashit($this->assetsUrl) . '/system-status.css',
            [],
            '1.4.0',
            'screen'
        );
    }

    public function enqueue(): void
    {
        if (wp_style_is('main', 'registered')) {
            wp_enqueue_style('main');
        }
    }
}
