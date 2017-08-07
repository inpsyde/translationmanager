<?php

namespace Translationmanager\Api;

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
	 * @param       $project_id
	 * @param array $data
	 *
	 * @return int|null ID of the new project or NULL on failure.
	 */
	public function create( $project_id, $data = array() ) {
		$body = $this->get_api()->put(
			$this->get_url( $project_id ),
			$data,
			array(
				'X-Source' => translationmanager_get_current_lang_code(),
				'X-Target' => $data['__meta']['target_language'],
			)
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
}