<?php

namespace Translationmanager;

use Translationmanager\Api\ApiException;
use Translationmanager\Api\Project;
use Translationmanager\Api\ProjectItem;

class Api {
	/**
	 * Project
	 *
	 * @since 1.0.0
	 *
	 * @var Project
	 */
	private $project;

	/**
	 * ProjectItem
	 *
	 * @since 1.0.0
	 *
	 * @var ProjectItem
	 */
	private $project_item;

	/**
	 * Api Key
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Plugin Key
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $plugin_key;

	/**
	 * Base Url for the API
	 *
	 * @since 1.0.0
	 *
	 * @var null|string
	 */
	private $base_url;

	/**
	 * Api constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $api_key    Key of the customer.
	 * @param string      $plugin_key Key of the plugin.
	 * @param null|string $base_url   URL to the API.
	 */
	public function __construct( $api_key, $plugin_key, $base_url = null ) {

		if ( null === $base_url ) {
			$base_url = 'https://api.eurotext.de/api';
		}
		$this->api_key    = $api_key;
		$this->plugin_key = $plugin_key;
		$this->base_url   = $base_url;
	}

	/**
	 * POST Request
	 *
	 * @since 1.0.0
	 *
	 * @throws ApiException In case something with the request went wrong.
	 *
	 * @param string $path    The url to call.
	 * @param array  $data    The data to send.
	 * @param array  $headers The header for the request.
	 *
	 * @return string[] The response data
	 */
	public function post( $path, $data = array(), $headers = [] ) {

		return $this->request( 'POST', $path, $data, $headers );
	}

	/**
	 * GET Request
	 *
	 * @since 1.0.0
	 *
	 * @throws ApiException In case something with the request went wrong.
	 *
	 * @param string $path    The url to call.
	 * @param array  $data    The data to send.
	 * @param array  $headers The header for the request.
	 *
	 * @return string[] The response data
	 */
	public function get( $path, $data = [], $headers = [] ) {

		return $this->request( 'GET', $path, $data, $headers );
	}

	/**
	 * Request
	 *
	 * @since 1.0.0
	 *
	 * @throws ApiException In case something with the request went wrong.
	 *
	 * @param string $method  The method to use for the api.
	 * @param string $path    The url to call.
	 * @param array  $data    The data to send.
	 * @param array  $headers The header for the request.
	 *
	 * @return string[] The response data
	 */
	public function request( $method, $path, $data = [], $headers = [] ) {

		$url     = $this->get_url( $path );
		$context = [
			// Add headers early to context to keep api key out of it.
			'headers' => $headers,
		];

		$headers['Content-Type'] = 'application/json';
		$headers['plugin_key']   = $this->plugin_key;
		$headers['apikey']       = $this->api_key;

		if ( 'GET' !== $method ) {
			$data = wp_json_encode( $data );
		}

		/**
		 * Translation Log Filter
		 *
		 * @since  1.0.0
		 *
		 * @params array Containing a message (method and url) and a context.
		 */
		do_action( 'translationmanager_log', [
			'message' => sprintf( '%s: %s', $method, $url ),
			'context' => $context,
		] );

		$response = wp_remote_request( $url, [
			'method'  => $method,
			'headers' => $headers,
			'body'    => $data,
		] );

		if ( is_wp_error( $response ) ) {
			throw new ApiException( $response->get_error_message(), $response->get_error_code() );
		}

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( '' === $response_code || $response_code < 200 || $response_code >= 300 ) {
			/**
			 * Translation Log Filter
			 *
			 * @since  1.0.0
			 *
			 * @params array Containing a message (method and url) and a context.
			 */
			do_action( 'translationmanager_log', [
				'message' => 'Request against API failed.',
				'context' => array_merge( $context, [
					'status' => $response_code,
					'body'   => wp_remote_retrieve_body( $response ),
				] ),
			] );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * Patch
	 *
	 * @since 1.0.0
	 *
	 * @param string $path    The path for the call.
	 * @param array  $data    The body content.
	 * @param array  $headers The headers for the server.
	 */
	public function patch( $path, $data = [], $headers ) {

		$this->request( 'PATCH', $path, $data, $headers );
	}

	/**
	 * Project
	 *
	 * This function always create the instance once.
	 *
	 * @since 1.0.0
	 *
	 * @return Project A new instance of a project class
	 */
	public function project() {

		if ( null === $this->project ) {
			$this->project = new Project( $this );
		}

		return $this->project;
	}

	/**
	 * Project Item
	 *
	 * This function always create the instance once.
	 *
	 * @since 1.0.0
	 *
	 * @return ProjectItem A new instance of a projectItem class
	 */
	public function project_item() {

		if ( null === $this->project_item ) {
			$this->project_item = new ProjectItem( $this );
		}

		return $this->project_item;
	}

	/**
	 * Url
	 *
	 * @since 1.0.0
	 *
	 * @throws \InvalidArgumentException If the $path parameter isn't a valid string.
	 *
	 * @param string $path Retrieve the url based on request path.
	 *
	 * @return string The url for the request
	 */
	private function get_url( $path ) {

		if ( ! is_string( $path ) || '' === $path ) {
			throw new \InvalidArgumentException( 'Expected string, got something else.' );
		}

		$path = rtrim( $path, '.json' ) . '.json';

		return $this->base_url . '/' . ltrim( $path, '/' );
	}
}
