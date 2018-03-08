<?php
/**
 * Plugin Settings
 *
 * @since   1.0.0
 * @package Translationmanager\Setting
 */

namespace Translationmanager\Setting;

use Translationmanager\Functions;

/**
 * Class PluginSettings
 *
 * @since   1.0.0
 * @package Translationmanager\Setting
 */
class PluginSettings {

	/**
	 * Options Group
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const OPTION_GROUP = 'translationmanager_api';

	/**
	 * Section Credentials
	 *
	 * @since 1.0.0
	 *
	 * @var string The section ID for the API Credentials
	 */
	const SECTION_CREDENTIALS = 'translationmanager_api_credentials';

	/**
	 * Register all settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_setting() {

		add_settings_section(
			self::SECTION_CREDENTIALS,
			esc_html__( 'Credentials', 'translationmanager' ),
			'__return_false',
			self::OPTION_GROUP
		);

		add_filter( 'sanitize_option_' . ApiSettings::URL, 'trim' );
		add_filter( 'sanitize_option_' . ApiSettings::URL, 'esc_url_raw' );

		// Base URL of the API.
		$this->add_settings_field(
			ApiSettings::URL,
			esc_html__( 'URL', 'translationmanager' ),
			[ $this, 'dispatch_input_text' ],
			self::OPTION_GROUP,
			self::SECTION_CREDENTIALS,
			[
				'value' => ApiSettings::url(),
			]
		);

		// Token.
		$this->add_settings_field(
			ApiSettings::TOKEN,
			esc_html__( 'Token', 'translationmanager' ),
			[ $this, 'dispatch_input_text' ],
			self::OPTION_GROUP,
			self::SECTION_CREDENTIALS,
			[
				'value' => ApiSettings::token(),
			]
		);

		add_filter( 'sanitize_option_' . ApiSettings::TOKEN, 'trim' );
		add_filter( 'sanitize_option_' . ApiSettings::URL, 'trim' );
	}

	/**
	 * Create input field for option.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Must have a "name" key with the actual option name/id as its value.
	 */
	public function dispatch_input_text( $field ) {

		$bind = (object) $field;

		unset( $field );

		require Functions\get_template( 'views/type/default.php' );
	}

	/**
	 * Has Fresh Token
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_refresh_token() {

		return (bool) ApiSettings::token();
	}

	/**
	 * Simplify adding setting fields.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $id        Slug-name to identify the field. Used in the 'id' attribute of tags.
	 * @param string   $title     Formatted title of the field. Shown as the label for the field
	 *                            during output.
	 * @param callable $callback  Function that fills the field with the desired form inputs. The
	 *                            function should echo its output.
	 * @param string   $page      The slug-name of the settings page on which to show the section
	 *                            (general, reading, writing, ...).
	 * @param string   $section   Optional. The slug-name of the section of the settings page
	 *                            in which to show the box. Default 'default'.
	 * @param array    $args      {
	 *                            Optional. Extra arguments used when outputting the field.
	 *
	 * @type string    $label_for When supplied, the setting title will be wrapped
	 *                             in a `<label>` element, its `for` attribute populated
	 *                             with this value.
	 * @type string    $class     CSS Class to be added to the `<tr>` element when the
	 *                             field is output.
	 * }
	 */
	private function add_settings_field( $id, $title, $callback, $page, $section, $args = [] ) {

		if ( ! isset( $args['name'] ) ) {
			$args['name'] = $id;
		}

		if ( ! isset( $args['type'] ) ) {
			$args['type'] = 'text';
		}

		if ( ! isset( $args['value'] ) ) {
			$args['value'] = get_option( $args['name'] );
		}

		register_setting( $page, $args['name'] );

		add_settings_field( $id, $title, $callback, $page, $section, $args );
	}
}
