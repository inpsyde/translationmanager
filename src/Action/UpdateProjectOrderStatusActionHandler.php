<?php

namespace Translationmanager\Action;

use Brain\Nonces\NonceInterface;
use Translationmanager\Auth\AuthRequest;
use function Translationmanager\Functions\project_global_status;
use function Translationmanager\Functions\redirect_admin_page_network;
use function Translationmanager\Functions\set_unique_term_meta;
use Translationmanager\Notice\TransientNoticeService;
use Translationmanager\Utils\TimeZone;

/**
 * Class UpdateProjectOrderStatusActionHandler
 *
 * @since   1.0.0
 * @package Translationmanager\Action
 */
class UpdateProjectOrderStatusActionHandler implements ActionHandle {

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
			TransientNoticeService::add_notice( esc_html__( 'Request is valid but no data found in it.' ), 'error' );

			return;
		}

		try {
			// Retrieve the project info.
			$project = get_term_by( 'slug', $data['_translationmanager_project_id'], 'translationmanager_project' );

			if ( ! $project instanceof \WP_Term ) {
				TransientNoticeService::add_notice( esc_html__( 'Invalid Project Name.' ), 'error' );

				return;
			}

			// Retrieve the generic status for the translation.
			$status = project_global_status( $project );

			$this->update_project_status( $project, $status );
			$this->update_project_status_request_date( $project );

			if ( 'finished' === strtolower( $status ) ) {
				// Update the translated at meta.
				$this->update_project_translated_at( $project );
			}
		} catch ( \Exception $e ) {
			TransientNoticeService::add_notice( $e->getMessage(), 'error' );
		}

		redirect_admin_page_network( 'admin.php', [
			'translationmanager_project' => get_term_field( 'slug', $project ),
			'post_type'                  => 'project_item',
			'project_status'             => $status,
		] );
	}

	/**
	 * @inheritdoc
	 */
	public function is_valid_request() {

		if ( ! isset( $_POST['translationmanager_action_project_update'] ) ) { // phpcs:ignore
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

	/**
	 * Update Order Status
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Term $project The term object for which update the meta.
	 * @param string   $status  The value to store as meta value.
	 *
	 * @return mixed Whatever the *_term_meta returns
	 */
	private function update_project_status( \WP_Term $project, $status ) {

		return set_unique_term_meta( $project, '_translationmanager_order_status', $status );
	}

	/**
	 * Update Order Status Last Update Date
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Term $project The term object for which update the meta.
	 *
	 * @return mixed Whatever the *_term_meta returns
	 */
	private function update_project_status_request_date( \WP_Term $project ) {

		return set_unique_term_meta(
			$project,
			'_translationmanager_order_status_last_update_request',
			( new \DateTime( 'now', ( new TimeZone() )->value() ) )->getTimestamp()
		);
	}

	/**
	 * Update Project Translated at meta
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Term $project The term object for which update the meta.
	 *
	 * @return mixed Whatever the *_term_meta returns
	 */
	private function update_project_translated_at( \WP_Term $project ) {

		return set_unique_term_meta(
			$project,
			'_translationmanager_order_translated_at',
			( new \DateTime( 'now', ( new TimeZone() )->value() ) )->getTimestamp()
		);
	}
}
