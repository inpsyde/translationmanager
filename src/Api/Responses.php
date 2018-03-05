<?php
/**
 * API Responses
 *
 * @since   1.0.0
 * @package Translationmanager\Api
 */

namespace Translationmanager\Api;

/**
 * Class Responses
 *
 * @since   1.0.0
 * @package Translationmanager\Api
 */
class Responses {

	/**
	 * Response List
	 *
	 * @since 1.0.0
	 *
	 * @var array The list of all server response.
	 */
	private $list;

	/**
	 * Responses constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->list = [
			403 => esc_html__( 'Forbidden. Seems you haven\'t setup the Credentials correctly.', 'translationmanager' ),
		];
	}

	/**
	 * Get Response by ID
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The response code.
	 *
	 * @return string The response message or a default one if not found
	 */
	public function response_by_id( $id ) {

		return isset( $this->list[ $id ] ) ? $this->list[ $id ] : esc_html__( 'Unknown response', 'translationmanager' );
	}
}
