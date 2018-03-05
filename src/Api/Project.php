<?php
/**
 * Handling the project endpoint of the API.
 *
 * @since   1.0.0
 *
 * @package Translationmanager\Api
 */

namespace Translationmanager\Api;

use Translationmanager\Api;

/**
 * Class Project
 *
 * @since   1.0.0
 *
 * @package Translationmanager\Api
 */
class Project {

	/**
	 * Endpoint Url
	 *
	 * @since 1.0.0
	 *
	 * @var string The endpoint for the project
	 */
	const URL = 'project';

	/**
	 * Api
	 *
	 * @since 1.0.0
	 *
	 * @var Api The instance of the api
	 */
	private $api;

	/**
	 * Project constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Api $api he instance of the api.
	 */
	public function __construct( Api $api ) {

		$this->api = $api;
	}

	/**
	 * Create a new project.
	 *
	 * @since 1.0.0
	 *
	 * @throws ApiException In case the project cannot be created.
	 *
	 * @param \Translationmanager\Domain\Project $project The project info needed to create the project in the server.
	 *
	 * @return int|null ID of the new project or NULL on failure.
	 */
	public function create( \Translationmanager\Domain\Project $project ) {

		$body = $this->api->post( self::URL, [], $project->to_header_array() );

		if ( ! isset( $body['id'] ) ) {
			throw new ApiException(
				isset( $body['message'] ) ? $body['message'] : esc_html__( 'Unknown exception during Create the project.' ),
				isset( $body['code'] ) ? $body['code'] : 501
			);
		}

		return (int) $body['id'];
	}

	/**
	 * Get Project
	 *
	 * @since 1.0.0
	 *
	 * @param string $project_id The ID of the project to retrieve from the server.
	 *
	 * @return string[] The response data
	 */
	public function get( $project_id ) {

		return $this->api->get( 'project/' . (int) $project_id );
	}
}
