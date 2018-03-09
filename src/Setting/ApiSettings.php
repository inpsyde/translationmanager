<?php
/**
 * Class ApiSettings
 *
 * @since   1.0.0
 * @package Translationmanager\Setting
 */

namespace Translationmanager\Setting;

use Translationmanager\Functions;

/**
 * Class ApiSettings
 *
 * @since   1.0.0
 * @package Translationmanager\Setting
 */
class ApiSettings {

	/**
	 * API URL
	 *
	 * @since 1.0.0
	 *
	 * @var string The api option key
	 */
	const URL = 'translationmanager_api_url';

	/**
	 * Token
	 *
	 * @since 1.0.0
	 *
	 * @var string The api option key
	 */
	const TOKEN = 'translationmanager_api_token';

	/**
	 * API Url Option
	 *
	 * @param bool $context If we want to get the site option first and fallback to the current site option.
	 * @param bool $default The default value for the option if not set.
	 *
	 * @return mixed Depending on the option stored
	 */
	public static function url( $context = true, $default = false ) {

		return self::option( self::URL, $default, $context );
	}

	/**
	 * API Token Option
	 *
	 * @param bool $context If we want to get the site option first and fallback to the current site option.
	 * @param bool $default The default value for the option if not set.
	 *
	 * @return mixed Depending on the option stored
	 */
	public static function token( $context = true, $default = false ) {

		return self::option( self::TOKEN, $default, $context );
	}

	/**
	 * Option
	 *
	 * If `context` is `false` the function try to get the option value from the current site and fallback to the site
	 * option. Note that the context is valid only outside of the network admin pages.
	 *
	 * Also, the default option in case of context is set to false will affect only the site option not the option for
	 * the current site.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name    The name of the option.
	 * @param bool   $default The default value for the option, not used if `context` is true.
	 * @param bool   $context True to retrieve the option contextualized or false to get the site option and fallback
	 *                        to the current site option.
	 *
	 * @return mixed Depending on the option value
	 */
	private static function option( $name, $default = false, $context = true ) {

		if ( ! $context ) {
			$option = get_option( $name, false );

			if ( ! $option && Functions\is_plugin_active_for_network() ) {
				$option = get_site_option( $name, $default );
			}

			return $option;
		}

		// Only if the plugin is active in the network, otherwise use the current site option.
		if ( is_network_admin() ) {
			return get_site_option( $name, $default );
		}

		return get_option( $name, $default );
	}
}
