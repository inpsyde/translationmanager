<?php
/**
 * Containing the class loader.
 *
 * @package translationmanager
 */

namespace Translationmanager;

/**
 * Handles the autoloader for classes.
 *
 * @package Translationmanager
 */
class Loader {
	/**
	 * Load a class.
	 *
	 * It won't try to load classes other than those in its own namespace.
	 *
	 * @param string $class_name The class to load / search for.
	 */
	public function load_class( $class_name ) {

		if ( false === strpos( $class_name, __NAMESPACE__ ) ) {
			// Not our scope => ignore.
			return;
		}

		$file_name = __DIR__ . '/' . ltrim( $this->class_to_file( $class_name ), DIRECTORY_SEPARATOR );

		if ( ! is_readable( $file_name ) ) {
			// not found => do nothing.
			return;
		}

		require_once $file_name;
	}

	/**
	 * Turn class name into file path.
	 *
	 * For example "\Foo\Bar\Bar" will turn into "foo/bar/class-bar.php".
	 *
	 * @see https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/
	 *
	 * @param string $class_name Class name that shall be resolved to a file name.
	 *
	 * @return string Relative path to a class by using WP Coding Standards.
	 */
	protected function class_to_file( $class_name ) {

		$file_name = $class_name;
		$file_name = strtolower( $file_name );
		$file_name = strtr( $file_name, [
			'\\'                        => DIRECTORY_SEPARATOR,
			'_'                         => '-',
			strtolower( __NAMESPACE__ ) => '',
		] );
		$file_name .= '.php';

		return dirname( $file_name ) . '/class-' . basename( $file_name );
	}
}
