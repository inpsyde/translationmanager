<?php
/**
 * Handling the project endpoint of the API.
 *
 * @since 1.0.0
 *
 * @package Translationmanager\Api
 */

namespace Translationmanager\Api;

use Translationmanager\Api;

/**
 * Class Project
 *
 * @since 1.0.0
 *
 * @package Translationmanager\Api
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
	 * @param \Translationmanager\Domain\Project $project
	 *
	 * @return int|null ID of the new project or NULL on failure.
	 */
	public function create( \Translationmanager\Domain\Project $project ) {

		$body = $this->get_api()->post( self::URL, [], $project->to_header_array() );

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
