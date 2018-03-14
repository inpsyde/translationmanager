<?php
/**
 * Import Project Action Handler
 *
 * @since   1.0.0
 * @package Translationmanager\Action
 */

namespace Translationmanager\Action\Api;

use Brain\Nonces\NonceInterface;
use Translationmanager\Action\ActionHandle;
use Translationmanager\Api\ApiException;
use Translationmanager\Auth\AuthRequest;
use Translationmanager\Notice\TransientNoticeService;

/**
 * Class ImportProject
 *
 * @since   1.0.0
 * @package Translationmanager\Action
 */
class ImportProject implements ActionHandle {

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
	 * ImportProject constructor
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

		add_action( 'admin_post_translationmanager_import_project', [ $this, 'handle' ] );
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
			TransientNoticeService::add_notice( esc_html__( 'Request is valid but no data found in it.' ), 'error' );

			return;
		}

		$project = get_term_by( 'slug', $data['_translationmanager_project_id'], 'translationmanager_project' );

		if ( ! $project instanceof \WP_Term ) {
			TransientNoticeService::add_notice( esc_html__( 'Invalid Project Name.' ), 'error' );

			return;
		}

		try {
			\Translationmanager\Functions\project_update( $project );

			$notice = [
				'message'  => esc_html__( 'Translation has been imported correctly.', 'translationmanager' ),
				'severity' => 'success',
			];
		} catch ( ApiException $e ) {
			$notice = [
				'message'  => sprintf(
					esc_html__( 'translatioinMANAGER: %s', 'translationmanager' ),
					$e->getMessage()
				),
				'severity' => 'error',
			];
		}

		TransientNoticeService::add_notice( $notice['message'], $notice['severity'] );

		\Translationmanager\Functions\redirect_admin_page_network( 'admin.php', [
			'page'                       => 'translationmanager-project',
			'translationmanager_project' => $data['_translationmanager_project_id'],
			'post_type'                  => 'project_item',
		] );
	}

	/**
	 * @inheritdoc
	 */
	public function is_valid_request() {

		if ( ! isset( $_POST['translationmanager_import_project_translation'] ) ) { // phpcs:ignore
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
