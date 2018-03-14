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
use Translationmanager\Domain;

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
	public function create( Domain\Project $project ) {

		$response = $this->api->post( self::URL, [], $project->to_header_array() );

		if ( ! isset( $response['id'] ) ) {
			throw new ApiException(
				esc_html_x( 'The server response without any project ID.', 'api-response', 'translationmanager' )
			);
		}

		return (int) $response['id'];
	}

	/**
	 * Update Status
	 *
	 * @since 1.0.0
	 *
	 * @throws ApiException If the response code isn't a valid one.
	 *
	 * @param int    $project_id The ID of the project for which update the status.
	 * @param string $status     The new status.
	 *
	 * @return mixed Depending on the request response.
	 */
	public function update_status( $project_id, $status ) {

		return $this->api->patch(
			'transition/' . self::URL . '/' . $project_id,
			[],
			[ 'X-Item-Status' => $status ]
		);
	}

	/**
	 * Get Project
	 *
	 * @since 1.0.0
	 *
	 * @param string $project_id The ID of the project to retrieve from the server.
	 *
	 * @return mixed Depending on the request response.
	 */
	public function get( $project_id ) {

		return $this->api->get( 'project/' . (int) $project_id );
	}
}
