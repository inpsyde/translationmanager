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
class Plugin {

	const VERSION = '1.0.0';

	private $plugin_file;

	public function __construct( $plugin_file ) {

		$this->plugin_file = $plugin_file;
	}

	public function plugin_dir() {

		return plugin_dir_path( $this->plugin_file );
	}

	public function includes_dir() {

		return untrailingslashit( $this->plugin_dir() ) . '/includes';
	}

	public function admin_dir() {

		return untrailingslashit( $this->plugin_dir() ) . '/admin';
	}

	public function public_dir() {

		return untrailingslashit( $this->plugin_dir() ) . '/public';
	}
}
