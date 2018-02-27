<?php
/**
 * Requirements
 *
 * @since 1.0.0
 * @package Translationmanager
 */

namespace Translationmanager;

/**
 * Class Requirements
 *
 * @since 1.0.0
 * @package Translationmanager
 */
class Requirements {

	/**
	 * PHP Min Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The minimum required php version
	 */
	const PHP_MIN_VERSION = '5.6';

	/**
	 * PHP Current Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The current php version
	 */
	const PHP_CURR_VERSION = PHP_VERSION;

	/**
	 * Check PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the current version is less than the minimum required version
	 */
	public function is_php_version_ok() {

		return version_compare( self::PHP_MIN_VERSION, self::PHP_CURR_VERSION, '<=' );
	}
}
