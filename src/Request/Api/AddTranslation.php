<?php

namespace Translationmanager\Request\Api;

use Brain\Nonces\NonceInterface;
use Translationmanager\Request\RequestHandleable;
use Translationmanager\Auth\Authable;
use function Translationmanager\Functions\action_project_add_translation;
use function Translationmanager\Functions\redirect_admin_page_network;
use Translationmanager\Notice\TransientNoticeService;

/**
 * Class AddTranslation
 *
 * @since   1.0.0
 * @package Translationmanager\Request
 */
class AddTranslation implements RequestHandleable {

	/**
	 * Auth
	 *
	 * @since 1.0.0
	 *
	 * @var \Translationmanager\Auth\Authable The instance to use to verify the request
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
	 * AddTranslation constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Auth\Authable $auth  The instance to use to verify the request.
	 * @param \Brain\Nonces\NonceInterface      $nonce The instance to use to verify the request.
	 */
	public function __construct( Authable $auth, NonceInterface $nonce ) {

		$this->auth  = $auth;
		$this->nonce = $nonce;
	}

	/**
	 * Set Hooks
	 *
	 * @since 1.0.0
	 */
	public function init() {

		add_action( 'load-edit.php', [ $this, 'handle' ] );
		add_action( 'load-post.php', [ $this, 'handle' ] );
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

		$updater = new \Translationmanager\ProjectUpdater();
		$updater->init();

		try {
			$project = action_project_add_translation( [
				'translationmanager_language'   => $data['translationmanager_language'],
				'translationmanager_project_id' => $data['translationmanager_project_id'],
				'post_ID'                       => $data['post_ID'],
			] );

			if ( ! $project ) {
				TransientNoticeService::add_notice(
					esc_html__( 'Something went wrong during create a new translation item.', 'translationmanager' ),
					'error'
				);

				return;
			}

			$notice = [
				'message'  => esc_html__( 'New Translation added successfully.', 'translationmanager' ),
				'severity' => 'success',
			];
		} catch ( \Exception $e ) {
			$notice = [
				'message'  => $e->getMessage(),
				'severity' => 'error',
			];
		}

		TransientNoticeService::add_notice( $notice['message'], $notice['severity'] );

		redirect_admin_page_network( 'admin.php', [
			'page'                          => 'translationmanager-project',
			'translationmanager_project_id' => $project,
			'post_type'                     => 'project_item',
			'updated'                       => - 1,
		] );
	}

	/**
	 * @inheritdoc
	 */
	public function is_valid_request() {

		if ( ! isset( $_POST['translationmanager_action_project_add_translation'] ) ) { // phpcs:ignore
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
			'translationmanager_project_id' => FILTER_SANITIZE_NUMBER_INT,
			'post_ID'                       => FILTER_SANITIZE_NUMBER_INT,
			'translationmanager_language'   => [
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_FORCE_ARRAY,
			],
		] );
	}
}
