<?php
/**
 * Containing class that handles pixxio options page.
 *
 * @package tm4mlp
 */

namespace Tm4mlp\Admin;

/**
 * Controller / Model for tm4mlp options page.
 *
 * @package Tm4mlp\Admin
 */
class Options_Page {
	const OPTION_GROUP = 'tm4mlp_api';
	const USERNAME = 'tm4mlp_api_username';
	const PASSWORD = 'tm4mlp_api_password';
	const SECTION_CREDENTIALS = 'tm4mlp_api_credentials';
	const URL = 'tm4mlp_api_url';
	const REFRESH_TOKEN = 'tm4mlp_api_token';
	const TRANSIENT_CATEGORIES = 'tm4mlp_categories';
	const SELECTED_CATEGORIES = 'tm4mlp_sync_categories';
	const SLUG = 'translationmanager';

	/**
	 * Allowed actions
	 *
	 * If this is a field in the post data,
	 * then the equally names function will be called with the post data.
	 * Usually those is a list of buttons that can be pressed on the options page.
	 *
	 * @var string[]
	 */
	protected $actions = array(
		'fetch_files',
		'save',
		'save_categories',
		'update_categories'
	);

	/**
	 * Add in WordPress settings.
	 */
	public function add_options_page() {
		add_options_page(
			__( 'Translations', 'tm4mpl' ),
			__( 'Translations', 'tm4mpl' ),
			'manage_options',
			self::SLUG,
			array( $this, 'dispatch' )
		);

		add_filter( 'option_page_capability_' . static::OPTION_GROUP, array( $this, 'filter_capabilities' ) );
	}

	/**
	 * Fetch token.
	 *
	 * Only / Best current point to hook in settings update process it the option page cap filter.
	 *
	 * @param string[] $capabilities
	 *
	 * @return \string[]
	 */
	public function filter_capabilities( $capabilities ) {
		if ( ! check_admin_referer( static::OPTION_GROUP . '-options' ) ) {
			// Seems like some other page so we won't do stuff.
			return $capabilities;
		}

		$login_data = $_REQUEST; // input var okay

		// Buttons the user can press.
		$chosen_action = array_intersect( array_keys( $login_data ), $this->actions );

		if ( $chosen_action ) {
			$chosen_action = current( $chosen_action ) . '_action';
			$this->$chosen_action( $login_data );
		}

		return $capabilities;
	}

	/**
	 * Create input field for option.
	 *
	 * @param array $field Must have a "name" key with the actual option name/id as its value.
	 */
	public function dispatch_input_text( $field ) {
		$prefix = $suffix = '';
		extract( $field, EXTR_OVERWRITE );

		require tm4mlp_get_template( 'admin/options-page/input-field.php' );
	}

	/**
	 * Register all settings.
	 */
	public function register_setting() {
		add_settings_section(
			self::SECTION_CREDENTIALS,
			__( 'Credentials', 'tm4mlp_api' ),
			'__return_false',
			self::OPTION_GROUP
		);

		add_filter( 'sanitize_option_' . static::URL, 'trim' );
		add_filter( 'sanitize_option_' . static::URL, array( $this, 'sanitize_url' ) );

		// Base URL of the API.
		$this->_add_settings_field(
			static::URL,
			__( 'URL', 'tm4mlp-api' ),
			array( $this, 'dispatch_input_text' ),
			static::OPTION_GROUP,
			static::SECTION_CREDENTIALS,
			array(
				'value' => get_option(
					static::URL,
					// Context: User is in the backend, did not yet fetched a token and finds instructions below.
					__( 'Not set', 'tm4mlp-api' )
				)
			)
		);

		// Token
		$this->_add_settings_field(
			static::REFRESH_TOKEN,
			__( 'Token', 'tm4mlp-api' ),
			array( $this, 'dispatch_input_text' ),
			static::OPTION_GROUP,
			static::SECTION_CREDENTIALS,
			array(
				'value' => get_option(
					static::REFRESH_TOKEN,
					// Context: User is in the backend, did not yet fetched a token and finds instructions below.
					__( 'Not set', 'tm4mlp-api' )
				)
			)
		);

		add_filter( 'sanitize_option_' . static::REFRESH_TOKEN, 'trim' );
		add_filter( 'sanitize_option_' . static::URL, 'trim' );
	}

	/**
	 * Simplify adding setting fields.
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
	protected function _add_settings_field( $id, $title, $callback, $page, $section, $args = array() ) {
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

	/**
	 * Trim common mistakes from this field.
	 *
	 * - Empty spaces.
	 * - Parts of URL surrounding.
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public function sanitize_url( $url ) {
		return trim( trim( $url ), '://.' );
	}

	/**
	 * Send output to client.
	 *
	 * @see ::render()
	 */
	public function dispatch() {
		print $this->render(); // WPCS: XSS OK.
	}

	/**
	 * Render the options page.
	 *
	 * @return string Content of the options page.
	 */
	public function render() {
		ob_start();
		require_once tm4mlp_get_template( '/admin/options-page.php' );

		return ob_get_clean();
	}

	public function has_refresh_token() {
		return (bool) get_option( static::REFRESH_TOKEN, FALSE );
	}


	protected function fetch_files_action() {
		tm4mlp_api_fetch_all();
	}

	protected function update_categories_action() {
		$collections = tm4mlp_api_collections_get();

		$collectionTransient = array();
		foreach ( $collections as $collection ) {
			$collectionTransient[ $collection['id'] ] = $collection;
		}

		set_transient( static::TRANSIENT_CATEGORIES, $collectionTransient, DAY_IN_SECONDS );
	}

	public function enqueue_style() {
		if ( ! get_current_screen() || 'settings_page_tm4mlp' !== get_current_screen()->id ) {
			return;
		}

		wp_register_style(
			'tm4mlp-options-page',
			plugins_url( 'public/css/style.css', 'tm4mlp/tm4mlp.php' )
		);

		wp_enqueue_style( 'tm4mlp-options-page' );
	}

	public function enqueue_script() {
		wp_enqueue_script('jquery-ui-tabs');
	}
}
