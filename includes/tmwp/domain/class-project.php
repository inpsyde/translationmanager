<?php

namespace Tmwp\Domain;

class Project {
	/**
	 * @var string
	 */
	private $system;
	/**
	 * @var string
	 */
	private $system_version;
	/**
	 * @var string
	 */
	private $plugin;
	/**
	 * @var string
	 */
	private $plugin_version;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var null
	 */
	private $callback;

	/**
	 * Project constructor.
	 *
	 * @param string $system         Current CMS or Framework.
	 * @param string $system_version Version of the CMS or Framework.
	 * @param string $plugin         Plugin or extension allowing API communication.
	 * @param string $plugin_version Version of this plugin.
	 * @param string $type           Could be "order" or "quote".
	 * @param null   $callback       URL to trigger after translation is completely done.
	 */
	public function __construct( $system, $system_version, $plugin, $plugin_version, $type = 'quote', $callback = null ) {
		$this->system         = $system;
		$this->system_version = $system_version;
		$this->plugin         = $plugin;
		$this->plugin_version = $plugin_version;
		$this->type           = $type;
		$this->callback       = $callback;
	}

	public function to_header_array() {
		return array(
			'X-System'         => $this->get_system(),
			'X-System-Version' => $this->get_system_version(),
			'X-Plugin'         => $this->get_plugin(),
			'X-Plugin-Version' => $this->get_plugin_version(),
			'X-Type'           => $this->get_type(),
			'X-Callback'       => $this->get_callback(),
		);
	}

	/**
	 * @return string
	 */
	public function get_system() {
		return $this->system;
	}

	/**
	 * @return string
	 */
	public function get_system_version() {
		return $this->system_version;
	}

	/**
	 * @return string
	 */
	public function get_plugin() {
		return $this->plugin;
	}

	/**
	 * @return string
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * @return null|string
	 */
	public function get_callback() {
		return $this->callback;
	}
}