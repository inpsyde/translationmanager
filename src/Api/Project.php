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

		$body = $this->api->post( self::URL, [], $project->to_header_array() );

		if ( ! isset( $body['id'] ) ) {
			throw new ApiException(
				isset( $body['message'] ) ? $body['message'] : esc_html__( 'Unknown exception when create the project.' ),
				isset( $body['code'] ) ? $body['code'] : 501
			);
		}

		return (int) $body['id'];
	}

	/**
	 * Update Status
	 *
	 * @since 1.0.0
	 *
	 * @param int                                $project_id The ID of the project for which update the status.
	 * @param string                             $status     The new status.
	 * @param \Translationmanager\Domain\Project $project    The project data. Without the status.
	 *
	 * @return void
	 */
	public function update_status( $project_id, $status, Domain\Project $project ) {

		$this->api->patch(
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
	 * @return string[] The response data
	 */
	public function get( $project_id ) {

		return $this->api->get( 'project/' . (int) $project_id );
	}
}
