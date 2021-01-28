<?php

/**
 * Class Modal
 *
 * @since   1.0.0
 * @package Translationmanager\View
 */

namespace Translationmanager\View;

use Closure;
use Translationmanager\Plugin;

/**
 * Class Modal
 *
 * @since   1.0.0
 * @package Translationmanager\View
 */
class Modal implements Viewable
{
    /**
     * Title
     *
     * @since 1.0.0
     *
     * @var string The modal title
     */
    private $title;

    /**
     * Icon
     *
     * @since 1.0.0
     *
     * @var string The icon class name to use
     */
    private $icon;

    /**
     * Callback
     *
     * @since 1.0.0
     *
     * @var callable The callback to call to fill the modal content
     */
    private $callback;

    /**
     * Plugin
     *
     * @since 1.0.0
     *
     * @var \Translationmanager\Plugin Instance of the plugin
     */
    private $plugin;

    /**
     * Modal constructor
     *
     * @param string $title The modal title.
     * @param string $icon The icon class name to use.
     * @param callable $callback The callback to call to fill the modal content.
     * @param \Translationmanager\Plugin $plugin Instance of the plugin.
     *
     * @since 1.0.0
     */
    public function __construct($title, $icon, callable $callback, Plugin $plugin)
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->callback = $callback;
        $this->plugin = $plugin;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $bind = (object)[
            'title' => $this->title,
            'attributes' => [
                'class' => [
                    'modal--' . sanitize_title($this->title),
                ],
            ],
            'icon' => [
                'attributes' => [
                    'class' => [
                        'dashicons',
                        $this->icon,
                    ],
                ],
            ],
            'callback' => $this->callback,
        ];

        $path = $this->plugin->dir('/views/modal.php');
        Closure::bind(
            function () use ($path) {

                include $path;
            },
            $bind
        )();

        wp_enqueue_style(
            'translationmanager-modal',
            $this->plugin->url('/assets/css/modal.css'),
            [],
            filemtime($this->plugin->dir('/assets/css/modal.css')),
            'screen'
        );
        wp_enqueue_script(
            'translationmanager-modal',
            $this->plugin->url('/resources/js/modal.js'),
            ['underscore', 'jquery'],
            filemtime($this->plugin->dir('/resources/js/modal.js')),
            true
        );
    }

    /**
     * Print the Modal trigger element
     *
     * @param string $label The label to show to the user.
     *
     * @since 1.0.0
     */
    public function modal_trigger($label)
    {
        ?>
        <a href="#<?= esc_attr(sanitize_title($this->title)) ?>" class="modal-trigger">
            <?= esc_html($label) ?>
        </a>
        <?php
    }
}
