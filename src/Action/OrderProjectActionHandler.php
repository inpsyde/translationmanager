<?php

namespace Translationmanager\Action;

use Brain\Nonces\NonceInterface;
use Translationmanager\Auth\AuthRequest;
use function Translationmanager\Functions\create_project_order;
use function Translationmanager\Functions\redirect_admin_page_network;
use function Translationmanager\Functions\update_project_order_meta;

/**
 * Class OrderProjectActionHandler
 *
 * @since   1.0.0
 * @package Translationmanager\Action
 */
class OrderProjectActionHandler implements ActionHandle {

	/**
	 * Auth
	 *
	 * @since 1.0.0
	 *
	 * @var \Translationmanager\Auth\AuthRequest The instance to use to verify the request
	 */
	private $auth;

	/**
	 * Nonce
	 *
	 * @since 1.0.0
	 *
	 * @var \Brain\Nonces\NonceInterface The instance to use to verify the request
	 */
	private $nonce;

	/**
	 * User Capability
	 *
	 * @since 1.0.0
	 *
	 * @var string The capability needed by the user to be able to perform the request
	 */
	private static $capability = 'manage_options';

	/**
	 * AddTranslationActionHandler constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Auth\AuthRequest $auth  The instance to use to verify the request.
	 * @param \Brain\Nonces\NonceInterface         $nonce The instance to use to verify the request.
	 */
	public function __construct( AuthRequest $auth, NonceInterface $nonce ) {

		$this->auth  = $auth;
		$this->nonce = $nonce;
	}

	/**
	 * Set Hooks
	 *
	 * @since 1.0.0
	 */
	public function init() {

		add_action( 'admin_post_translationmanager_order_or_update_projects', [ $this, 'handle' ] );
	}

	/**
	 * @inheritdoc
	 */
	public function handle() {

		if ( ! $this->is_valid_request() ) {
			return;
		}

		$data = $this->request_data();

		if ( ! $data ) {
			throw new ActionException( 'Request is valid but no data found in it.' );
		}

		$term = get_term_by( 'slug', $data['_translationmanager_project_id'], 'translationmanager_project' );

		if ( ! create_project_order( $term ) ) {
			throw new ActionException( sprintf(
				'Impossible to update the project order meta for project %s',
				esc_html( $term->name )
			) );
		}

		redirect_admin_page_network( 'edit.php?', [
			'translationmanager_project' => $data['_translationmanager_project_id'],
			'post_type'                  => 'project_item',
		] );
	}

	/**
	 * @inheritdoc
	 */
	public function is_valid_request() {

		if ( ! isset( $_POST['translationmanager_action_project_order'] ) ) { // phpcs:ignore
			return false;
		}

		return $this->auth->can( wp_get_current_user(), self::$capability )
		       && $this->auth->request_is_valid( $this->nonce );
	}

	/**
	 * @inheritdoc
	 */
	public function request_data() {

		return filter_input_array( INPUT_POST, [
			'_translationmanager_project_id' => FILTER_SANITIZE_STRING,
		] );
	}
}
