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

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * Main Plugin file path
	 *
	 * @since 1.0.0
	 *
	 * @var string The main plugin file path
	 */
	private $file_path;

	/**
	 * Plugin constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->file_path = untrailingslashit( plugin_dir_path( __DIR__ ) ) . '/translationmanager.php';
	}

	/**
	 * Plugin Dir
	 *
	 * @since 1.0.0
	 *
	 * @param string $dir The additional path to append to the plugin dir.
	 *
	 * @return string The requested directory
	 */
	public function dir( $dir = '' ) {

		$path = plugin_dir_path( $this->file_path );

		if ( $dir ) {
			$path = untrailingslashit( $path ) . '/' . trim( $dir, DIRECTORY_SEPARATOR );
		}

		return $path;
	}

	/**
	 * Plugin Url
	 *
	 * @since 1.0.0
	 *
	 * @param string $url The additional url to append to the plugin url.
	 *
	 * @return string The requested url
	 */
	public function url( $url ) {

		return plugins_url( $url, $this->file_path );
	}

	/**
	 * Path to the main plugin file
	 *
	 * @since 1.0.0
	 *
	 * @return string The main plugin file path
	 */
	public function file_path() {

		return $this->file_path;
	}

	/**
	 * Plugin File
	 *
	 * This function returns the dirname/filename to be used with WordPress functions that needs the plugin file in the
	 * form of pluginDirName/pluginFileName.ext
	 *
	 * @return string The plugin file base path
	 */
	public function plugin_file() {

		$basename = basename( $this->file_path );
		$dirname  = explode( DIRECTORY_SEPARATOR, dirname( $this->file_path ) );
		$dirname  = end( $dirname );

		return untrailingslashit( $dirname ) . '/' . $basename;
	}
}
