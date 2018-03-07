<?php

namespace Translationmanager\Api;

use Translationmanager\Functions;
use Translationmanager\Api;

/**
 * Handling the project endpoint of the API.
 *
 * @package Translationmanager\Api
 */
class ProjectItem {

	/**
	 * URL
	 *
	 * @since 1.0.0
	 *
	 * @var string The endpoint for the project item.
	 */
	const URL = 'project/%d/item';

	/**
	 * API
	 *
	 * @since 1.0.0
	 *
	 * @var Api
	 */
	private $api;

	/**
	 * ProjectItem constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Api $api The Api instance.
	 */
	public function __construct( Api $api ) {

		$this->api = $api;
	}

	/**
	 * Create a new project.
	 *
	 * @since 1.0.0
	 *
	 * @throws \Translationmanager\Api\ApiException In case the project item cannot be created.
	 *
	 * @param int    $project_id
	 * @param string $post_type_name
	 * @param string $target_language
	 * @param array  $data
	 *
	 * @return int|null ID of the new project or NULL on failure.
	 */
	public function create( $project_id, $post_type_name, $target_language, $data = [] ) {

		$body = $this->api->post(
			$this->get_url( $project_id ),
			$data,
			[
				'X-Source'        => $this->normalize_language_code( Functions\current_lang_code() ),
				'X-Target'        => $this->normalize_language_code( $target_language ),
				'X-TextType'      => $this->get_text_type( $post_type_name ),
				'X-System-Module' => $post_type_name,
			]
		);

		if ( ! isset( $body['id'] ) ) {
			throw new ApiException(
				isset( $body['message'] ) ? $body['message'] : esc_html__( 'Unknown exception during Create the project item.' ),
				isset( $body['code'] ) ? $body['code'] : 501
			);
		}

		return (int) $body['id'];
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $project_id Project ID.
	 *
	 * @return string The url for the request
	 */
	private function get_url( $project_id ) {

		return sprintf( self::URL, $project_id );
	}

	/**
	 * Get the Text-Type based on the Post-Type
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type_name The post type name.
	 *
	 * @return string text-type name for REST-API
	 */
	private function get_text_type( $post_type_name ) {

		$text_type_name = str_replace(
			[ 'post', 'page' ],
			[ 'marketing', 'specialized-text' ],
			$post_type_name
		);

		$text_type_name = apply_filters( 'translationmanager_get_text_type', $text_type_name, $post_type_name );

		return $text_type_name;
	}

	/**
	 * Normalize language code for translation manager api request
	 *
	 * @since 1.0.0
	 *
	 * @param string $lang_code The language code to normalize.
	 *
	 * @return string The normalize language code
	 */
	private function normalize_language_code( $lang_code ) {

		return strtolower( str_replace( '_', '-', $lang_code ) );
	}
}
