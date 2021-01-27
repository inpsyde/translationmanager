<?php

/**
 * Project
 *
 * @since   1.0.0
 * @package Translationmanager\Domain
 */

namespace Translationmanager\Domain;

/**
 * Class Project
 *
 * @since   1.0.0
 * @package Translationmanager\Domain
 */
class Project
{
    /**
     * System
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $system;

    /**
     * System Version
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $system_version;

    /**
     * Plugin
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $plugin;

    /**
     * Plugin Version
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $plugin_version;

    /**
     * Name
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $name;

    /**
     * Type
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $type;

    /**
     * Callback
     *
     * @since 1.0.0
     *
     * @var null
     */
    private $callback;

    /**
     * Project constructor.
     *
     * @param string $system Current CMS or Framework.
     * @param string $system_version Version of the CMS or Framework.
     * @param string $plugin Plugin or extension allowing API communication.
     * @param string $plugin_version Version of this plugin.
     * @param string $name Project Name.
     * @param string $type Could be "order" or "quote".
     * @param null $callback URL to trigger after translation is completely done.
     *
     * @since 1.0.0
     */
    public function __construct(
        $system,
        $system_version,
        $plugin,
        $plugin_version,
        $name = '',
        $type = 'quote',
        $callback = null
    ) {

        $this->system = $system;
        $this->system_version = $system_version;
        $this->plugin = $plugin;
        $this->plugin_version = $plugin_version;
        $this->name = $name;
        $this->type = $type;
        $this->callback = $callback;
    }

    /**
     * @return array
     * @since 1.0.0
     */
    public function to_header_array()
    {
        return [
            'X-System' => $this->get_system(),
            'X-System-Version' => $this->get_system_version(),
            'X-Plugin' => $this->get_plugin(),
            'X-Plugin-Version' => $this->get_plugin_version(),
            'X-Name' => $this->get_name(),
            'X-Type' => $this->get_type(),
            'X-Callback' => $this->get_callback(),
        ];
    }

    /**
     * @return string
     * @since 1.0.0
     */
    public function get_system()
    {
        return $this->system;
    }

    /**
     * @return string
     * @since 1.0.0
     */
    public function get_system_version()
    {
        return $this->system_version;
    }

    /**
     * @return string
     * @since 1.0.0
     */
    public function get_plugin()
    {
        return $this->plugin;
    }

    /**
     * @return string
     * @since 1.0.0
     */
    public function get_plugin_version()
    {
        return $this->plugin_version;
    }

    /**
     * @return string
     * @since 1.0.0
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @return string
     * @since 1.0.0
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * @return null|string
     * @since 1.0.0
     */
    public function get_callback()
    {
        return $this->callback;
    }
}
