<?php

namespace Tm4mlp\Api;

use Tm4mlp\Api;

/**
 * Handling the project endpoint of the API.
 *
 * @package Tm4mlp\Api
 */
class Project {
	const URL = 'project';
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
	 * @param array $data
	 * @param array $headers
	 *
	 * @return int|null ID of the new project or NULL on failure.
	 */
	public function create( \Tm4mlp\Domain\Project $project ) {
		$body = $this->get_api()->put( self::URL, array(), $project->to_header_array() );

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
}