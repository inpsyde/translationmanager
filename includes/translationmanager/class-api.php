<?php

namespace Translationmanager;

use Translationmanager\Api\Project;
use Translationmanager\Api\Project_Item;

class Api {
	/**
	 * @var Project
	 */
	protected $project;
	/**
	 * @var Project_Item
	 */
	protected $project_item;
	/**
	 * @var string
	 */
	private $api_key;
	/**
	 * @var string
	 */
	private $plugin_key;
	/**
	 * @var null|string
	 */
	private $base_url;

	/**
	 * Api constructor.
	 *
	 * @param string      $api_key    Key of the customer.
	 * @param string      $plugin_key Key of the plugin.
	 * @param null|string $base_url   URL to the API.
	 */
	public function __construct( $api_key, $plugin_key, $base_url = null ) {
		if ( null === $base_url ) {
			$base_url = 'http://api.eurotext.de/api';
		}
		$this->api_key    = $api_key;
		$this->plugin_key = $plugin_key;
		$this->base_url   = $base_url;
	}

	public function post( $path, $data = array(), $headers = array() ) {
		return $this->request( 'POST', $path, $data, $headers );
	}

	/**
	 * @param string $method
	 * @param string $path
	 * @param string $data
	 * @param string $headers
	 *
	 * @return string[]
	 */
	public function request( $method, $path, $data = array(), $headers = array() ) {
		$url     = $this->get_url( $path );
		$context = [
			// Add headers early to context to keep api key out of it.
			'headers' => $headers
		];

		$headers['Content-Type'] = 'application/json';
		$headers['plugin_key']   = $this->plugin_key;
		$headers['apikey']       = $this->api_key;

		if ( 'GET' !== $method ) {
			$data = json_encode( $data );
		}

		do_action(
			'translationmanager_log',
			[
				'message' => sprintf(
					'%s: %s',
					$method,
					$url
				),
				'context' => $context
			]
		);

		$response = wp_remote_request(
			$url,
			array(
				'method'  => $method,
				'headers' => $headers,
				'body'    => $data,
			)
		);

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( $response_code < 200 || $response_code >= 300 ) {
			do_action(
				'translationmanager_log',
				[
					'message' => 'Request against API failed.',
					'context' => array_merge(
						$context,
						[
							'status' => $response_code,
							'body'   => wp_remote_retrieve_body( $response ),
						]
					),
				]
			);
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	public function get_url( $path ) {
		if ( null !== $path ) {
			$path .= '.json';
		}

		return $this->base_url . '/' . ltrim( $path, '/' );
	}

	public function get( $path, $data = array(), $headers = array() ) {
		return $this->request( 'GET', $path, $data, $headers );
	}

	/**
	 * @return Project
	 */
	public function project() {
		if ( null === $this->project ) {
			$this->project = new Project( $this );
		}

		return $this->project;
	}

	/**
	 * @return Project_Item
	 */
	public function project_item() {
		if ( null === $this->project_item ) {
			$this->project_item = new Project_Item( $this );
		}

		return $this->project_item;
	}
}