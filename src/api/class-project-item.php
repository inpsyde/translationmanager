<?php

namespace Translationmanager\Api;

use Translationmanager\Functions;
use Translationmanager\Api;

/**
 * Handling the project endpoint of the API.
 *
 * @package Translationmanager\Api
 */
class Project_Item {
	const URL = 'project/%d/item';
	/**
	 * @var Api
	 */
	private $api;

	public function __construct( Api $api ) {
		$this->api = $api;
	}

	/**
	 * Create a new project.
	 *
	 * @param        $project_id
	 * @param string $post_type_name
	 * @param array  $data
	 *
	 * @return int|null ID of the new project or NULL on failure.
	 */
	public function create( $project_id, $post_type_name, $target_language, $data = [] ) {
		$body = $this->get_api()->post(
			$this->get_url( $project_id ),
			$data,
			[
				'X-Source' => Functions\current_lang_code(),
				'X-Target' => $target_language,
				'X-TextType' => $this->get_text_type( $post_type_name ),
				'X-System-Module' => $post_type_name,
			]
		);

		if ( ! isset( $body['id'] ) ) {
			return null;
		}

		return (int) $body['id'];
	}

	/**
	 * @return Api
	 */
	protected function get_api() {
		return $this->api;
	}

	/**
	 * @return string
	 */
	protected function get_url( $project_id ) {
		return sprintf( self::URL, $project_id );
	}

	/**
	 * Get the Text-Type based on the Post-Type
	 *
	 * @param string $post_type_name
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
}