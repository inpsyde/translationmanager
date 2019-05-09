<?php

namespace Translationmanager\Activation;

use Translationmanager\Plugin;

/**
 * Class Activator
 *
 * @since   1.0.0
 * @package Translationmanager\Activator
 */
class Activator
{
    /**
     * Plugin
     *
     * @since 1.0.0
     *
     * @var \Translationmanager\Plugin The plugin instance
     */
    private $plugin;

    /**
     * Activate constructor
     *
     * @param \Translationmanager\Plugin $plugin The plugin instance.
     *
     * @since 1.0.0
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Activate plugin
     *
     * @throws \Exception In case the plugin version isn't set.
     * @since 1.0.0
     */
    public function store_version()
    {
        $setup_files = glob($this->plugin->dir('/inc/plugin-activate/*.php'));
        natsort($setup_files);

        $current_version = get_option('translationmanager_version', '0.0.0');

        foreach ($setup_files as $setup_script) {
            $file_version = strtok(basename($setup_script), '-');

            if (version_compare($current_version, $file_version) >= 0) {
                // Current version is bigger than or equal to file version so we skip it.
                continue;
            }

            if (version_compare($file_version, $this->plugin->version()) > 0) {
                // File version is bigger than plugin version so we skip future scripts.
                continue;
            }

            require_once $setup_script;

            update_option('translationmanager_version', $file_version);
        }
    }
}
