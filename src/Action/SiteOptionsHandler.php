<?php
/**
 * Site Options Handler
 *
 * @since   1.0.0
 * @package Translationmanager\Action
 */

namespace Translationmanager\Action;

use Brain\Nonces\NonceInterface;
use Translationmanager\Auth\AuthRequest;
use Translationmanager\Notice\StandardNotice;

/**
 * Class SiteOptionsHandler
 *
 * @since   1.0.0
 * @package Translationmanager\Action
 */
class SiteOptionsHandler implements ActionHandle {

	/**
	 * Auth
	 *
	 * @since 1.0.0
	 *
	 * @var \Translationmanager\Auth\AuthRequest
	 */
	private $auth;

	/**
	 * Nonce
	 *
	 * @since 1.0.0
	 *
	 * @var \Brain\Nonces\NonceInterface
	 */
	private $nonce;

	/**
	 * Capability
	 *
	 * This is for admin network only.
	 *
	 * @var string
	 */
	private static $capability = 'manage_network_options';

	/**
	 * Allowed Options
	 *
	 * @since 1.0.0
	 *
	 * @var array A list of allowed options where the key is the option name and the value the sanitizer ID
	 */
	private static $options = [
		'translationmanager_api_url'   => FILTER_SANITIZE_URL,
		'translationmanager_api_token' => FILTER_SANITIZE_STRING,
	];

	/**
	 * SiteOptionsHandler constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Auth\AuthRequest $auth  The instance to check for capabilities.
	 * @param \Brain\Nonces\NonceInterface         $nonce The instance to check for valid nonce.
	 */
	public function __construct( AuthRequest $auth, NonceInterface $nonce ) {

		$this->auth  = $auth;
		$this->nonce = $nonce;
	}

	/**
	 * @inheritdoc
	 */
	public function handle() {

		if ( ! $this->is_valid_request() ) {
			return;
		}

		$data = $this->request_data();

		foreach ( $data as $key => $value ) {
			update_site_option( $key, $value );
		}

		( new StandardNotice(
			esc_html__( 'Settings Updated.', 'translationmanager' ),
			'success'
		) )->show();
	}

	/**
	 * @inheritdoc
	 */
	public function is_valid_request() {

		if ( ! isset( $_POST['translationmanager_save_settings'] ) ) { // phpcs:ignore
			return false;
		}

		return $this->auth->can( wp_get_current_user(), self::$capability )
		       && $this->auth->request_is_valid( $this->nonce );
	}

	/**
	 * @inheritdoc
	 */
	public function request_data() {

		$data = [];
		foreach ( self::$options as $key => $sanitize_type ) {
			$data[ $key ] = filter_input( INPUT_POST, $key, $sanitize_type );
		}

		return array_filter( $data );
	}
}
