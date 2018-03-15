<?php

namespace Translationmanager\Request\Api;

use Brain\Nonces\NonceInterface;
use Translationmanager\ProjectHandler;
use Translationmanager\Request\RequestHandleable;
use Translationmanager\Auth\Authable;
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
	 * Project Handler
	 *
	 * @since 1.0.0
	 *
	 * @var \Translationmanager\ProjectHandler The instance of the class
	 */
	private $project_handler;

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
	public function __construct( Authable $auth, NonceInterface $nonce, ProjectHandler $project_handler ) {

		$this->auth            = $auth;
		$this->nonce           = $nonce;
		$this->project_handler = $project_handler;
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

		$data = (object) $this->request_data();

		if ( ! $data ) {
			TransientNoticeService::add_notice( esc_html__( 'Request is valid but no data found in it.' ), 'error' );

			return;
		}

		// @todo What about using `ProjectUpdater` directly instead of via hook?
		$updater = new \Translationmanager\ProjectUpdater();
		$updater->init();

		try {
			$project = isset( $data->translationmanager_project_id ) ? $data->translationmanager_project_id : '-1';

			if ( '-1' === $project ) {
				$project = ProjectHandler::create_project_using_date();
			}

			/**
			 * Runs before adding translations to the project.
			 *
			 * You might add other things to the project before the translations kick in
			 * or check against some other things (like account balance) to stop adding things to the project
			 * and show some error message.
			 *
			 * For those scenarios this filter allows turn it's value into false.
			 * In that case it will neither add things to the project/project
			 * nor redirect to the project- / project-view.
			 *
			 * @see wp_insert_post() actions and filter to access each single transation that is added to project.
			 *
			 * @param bool  $valid                       Initially true and can be torn to false to stop adding items to the project.
			 * @param int   $project                     ID of the project (actually a term ID).
			 * @param int   $post_ID                     The post ID for the post to translate.
			 * @param array $translationmanager_language The language in which translate the post.
			 */
			$valid = apply_filters(
				'translationmanager_filter_before_add_to_project',
				true,
				$project,
				$data->post_ID,
				$data->translationmanager_language
			);

			if ( true !== $valid ) {
				return;
			}

			// Remember the last manipulated project.
			update_user_meta( get_current_user_id(), 'translationmanager_project_recent', $project );

			// Iterate translations.
			foreach ( $data->translationmanager_language as $lang_id ) {
				$this->project_handler->add_translation( $project, (int) $data->post_ID, $lang_id );
			}

			/**
			 * Action
			 *
			 * After adding posts to a project / project it will redirect to this project.
			 * One last time you can filter to which project it will redirect (by using the ID)
			 * or if should'nt redirect at all (by setting the value to "false").
			 *
			 * @param int   $project   ID of the project (actually a term ID).
			 * @param int   $post_id   ID of the post that will be added to the project.
			 * @param int[] $languages IDs of the target languages (assoc pair).
			 *
			 * @see \Translationmanager\Functions\action_project_add_translation() where this filter resides.
			 * @see \Translationmanager\Functions\get_languages() how languages are gathered.
			 */
			do_action(
				'translationmanager_action_project_add_translation',
				$project,
				$data->post_ID,
				$data->translationmanager_language
			);

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

		return array_filter( filter_input_array( INPUT_POST, [
			'translationmanager_project_id' => FILTER_SANITIZE_NUMBER_INT,
			'post_ID'                       => FILTER_SANITIZE_NUMBER_INT,
			'translationmanager_language'   => [
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_FORCE_ARRAY,
			],
		] ) );
	}
}
