<?php

/**
 * Plugin
 *
 * @since   1.0.0
 * @package Translationmanager
 */

namespace Translationmanager;

/**
 * Class Plugin
 *
 * @since   1.0.0
 * @package Translationmanager
 */
class Plugin
{
    /**
     * Main Plugin file path
     *
     * @since 1.0.0
     *
     * @var string The main plugin file path
     */
    private $file_path;

    /**
     * Plugin Header Data
     *
     * @since 1.0.0
     *
     * @var array The plugin header data
     */
    private $header_data;

    /**
     * Plugin constructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->file_path = untrailingslashit(plugin_dir_path(__DIR__)) . '/translationmanager.php';

        $this->header_data = get_file_data(
            $this->file_path,
            [
                'version' => 'Version',
            ]
        );
    }

    /**
     * Plugin Dir
     *
     * @param string $dir The additional path to append to the plugin dir.
     *
     * @return string The requested directory
     * @since 1.0.0
     */
    public function dir($dir = '')
    {
        $path = plugin_dir_path($this->file_path);

        if ($dir) {
            $path = untrailingslashit($path) . '/' . trim($dir, DIRECTORY_SEPARATOR);
        }

        return $path;
    }

    /**
     * Plugin Url
     *
     * @param string $url The additional url to append to the plugin url.
     *
     * @return string The requested url
     * @since 1.0.0
     */
    public function url($url)
    {
        return plugins_url($url, $this->file_path);
    }

    /**
     * Path to the main plugin file
     *
     * @return string The main plugin file path
     * @since 1.0.0
     */
    public function file_path()
    {
        return $this->file_path;
    }

    /**
     * Version
     *
     * @return string The current plugin version
     * @since 1.0.0
     */
    public function version()
    {
        return isset($this->header_data['version']) ? $this->header_data['version'] : '0.0.0';
    }
}
