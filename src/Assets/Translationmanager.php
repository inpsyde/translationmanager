<?php

/**
 * Asset Translationmanager
 *
 * @since   1.0.0
 * @package Translationmanager\Assets
 */

namespace Translationmanager\Assets;

use Translationmanager\Plugin;

/**
 * Class Translationmanager
 *
 * @since   1.0.0
 * @package Translationmanager\Assets
 */
class Translationmanager
{
    /**
     * Plugin
     *
     * @since 1.0.0
     *
     * @var \Translationmanager\Plugin Instance of the class
     */
    private $plugin;

    /**
     * Translationmanager constructor
     *
     * @param \Translationmanager\Plugin $plugin Instance of the class.
     *
     * @since 1.0.0
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Register Style
     *
     * @return void
     * @since 1.0.0
     */
    public function register_style()
    {
        wp_enqueue_style(
            'translationmanager',
            $this->plugin->url('/assets/css/translationmanager.css'),
            [],
            filemtime($this->plugin->dir('/assets/css/translationmanager.css')),
            'screen'
        );
    }
}
