<?php

namespace Tmwp\Api;

use Tmwp\Api;

/**
 * Handling the project endpoint of the API.
 *
 * @package Tmwp\Api
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
	 * @param \Tmwp\Domain\Project $project
	 *
	 * @return int|null ID of the new project or NULL on failure.
	 */
	public function create( \Tmwp\Domain\Project $project ) {
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

	public function get( $project_id ) {
		return $this->get_api()->get( 'project/' . (int) $project_id );
	}
}