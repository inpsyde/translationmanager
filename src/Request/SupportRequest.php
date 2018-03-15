<?php
/**
 * Class SupportRequest
 *
 * @since   1.0.0
 * @package Translationmanager\Setting
 */

namespace Translationmanager\Request;

use Brain\Nonces\NonceInterface;
use Translationmanager\Auth\Authable;
use Translationmanager\Exception\FileUploadException;
use Translationmanager\Notice\StandardNoticeable;
use Translationmanager\Functions;

/**
 * Class SupportRequest
 *
 * @since   1.0.0
 * @package Translationmanager\Setting
 */
class SupportRequest implements RequestHandleable {

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
	 * Destination
	 *
	 * @since 1.0.0
	 *
	 * @var string Destination for email
	 */
	private static $destination = 'patrick.ullrich@eurotext.de';

	/**
	 * Accepted File Types
	 *
	 * @since 1.0.0
	 *
	 * @var array List of accepted mime types
	 */
	private static $accepted_file_types = [
		'image/png',
		'image/jpg',
		'image/jpeg',
		'image/gif',
	];

	/**
	 * SupportRequest constructor
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
	 * @inheritdoc
	 */
	public function handle() {

		if ( ! $this->is_valid_request() ) {
			return null;
		}

		try {
			$data = $this->request_data();

			// Send Email.
			$response = call_user_func_array( 'wp_mail', $this->build_mail_data( $data ) );

			if ( ! $response ) {
				$this->response(
					sprintf(
						esc_html__( 'Sorry seems something went wrong sending your support request please, try again or contact us at %s', 'translationmanager' ),
						self::$destination
					),
					'error'
				);

				return;
			}

			$this->response(
				esc_html__( 'Your support request has been sent successfully.' ),
				'success'
			);
		} catch ( \Exception $e ) {
			$this->response( $e->getMessage(), 'warning' );
		}
	}

	/**
	 * @inheritdoc
	 */
	public function is_valid_request() {

		if ( ! isset( $_POST['support_request'] ) ) { // phpcs:ignore
			return false;
		}

		return $this->auth->can( wp_get_current_user(), self::$capability )
		       && $this->auth->request_is_valid( $this->nonce );
	}

	/**
	 * @inheritdoc
	 */
	public function request_data() {

		$inputs = filter_input_array( INPUT_POST, [
			'support_request_summary'     => FILTER_SANITIZE_STRING,
			'support_request_description' => FILTER_SANITIZE_STRING,
			'support_request_agreement'   => FILTER_VALIDATE_BOOLEAN,
		] );

		if ( ! $inputs['support_request_agreement'] ) {
			throw new \RuntimeException(
				esc_html__( 'You must accept the terms by Eurotext before send a support request.' )
			);
		}

		if ( ! $inputs['support_request_summary'] || ! $inputs['support_request_description'] ) {
			throw new \RuntimeException(
				esc_html__( 'Ops! Seems you\'ve missed to type the summary or the description. Please provide those informations.' )
			);
		}

		$files = $this->validate_upload_files();

		return ( $inputs + [ 'files' => $files ] );
	}

	/**
	 * Build Mail Data
	 *
	 * @since 1.0.0
	 *
	 * @param array $data The submitted data.
	 *
	 * @return array The data to use for the email.
	 */
	private function build_mail_data( array $data ) {

		$user     = wp_get_current_user();
		$userName = Functions\username( $user );

		return [
			'to'          => self::$destination,
			'subject'     => sanitize_text_field( $data['support_request_summary'] ),
			'message'     => wp_strip_all_tags( $data['support_request_description'] ),
			'headers'     => [
				"From: {$userName} <{$user->user_email}>",
			],
			'attachments' => $data['files'],
		];
	}

	/**
	 * Validate Upload Files
	 *
	 * @since 1.0.0
	 *
	 * @return array A list of valid files path.
	 */
	private function validate_upload_files() {

		$list     = [];
		$temp_dir = untrailingslashit( sys_get_temp_dir() );

		if ( empty( $_FILES ) ) { // phpcs:ignore
			return $list;
		}

		if ( ! isset( $_FILES['support_request_upload'] ) ) { // phpcs:ignore
			return $list;
		}

		$num_files = count( $_FILES['support_request_upload']['tmp_name'] ); // phpcs:ignore
		for ( $count = 0; $count < $num_files; ++ $count ) {
			$file_path     = $_FILES['support_request_upload']['tmp_name'][ $count ]; // phpcs:ignore
			$file_name     = $_FILES['support_request_upload']['name'][ $count ]; // phpcs:ignore
			$new_file_path = $temp_dir . '/' . $file_name;

			if ( ! is_uploaded_file( $file_path ) ) {
				continue;
			}

			if ( ! in_array( mime_content_type( $file_path ), self::$accepted_file_types, true ) ) {
				throw new FileUploadException( 'Invalid mime type for the uploaded file.' );
			}

			if ( ( 5 * MB_IN_BYTES ) < $_FILES['support_request_upload']['size'][ $count ] ) { // phpcs:ignore
				throw new FileUploadException( 'The file you are trying to send exceed the 5MB. Please upload a smaller file.' );
			}

			// Move the file temporary.
			move_uploaded_file( $file_path, $new_file_path );

			$list[] = $new_file_path;
		}

		return $list;
	}

	/**
	 * Set Response for the request
	 *
	 * @since 1.0.0
	 *
	 * @see   StandardNoticeable
	 *
	 * @param string $message  The message string.
	 * @param string $severity The severity.
	 *
	 * @return void
	 */
	private function response( $message, $severity ) {

		( new StandardNoticeable( 'translationMANAGER: ' . $message, $severity ) )->show();
	}
}
