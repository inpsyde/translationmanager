<?php

namespace Tm4mlp;

use Tm4mlp\Api\Project;
use Tm4mlp\Api\Project_Item;

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

	public function put( $path, $data = array(), $headers = array() ) {
		return $this->request( 'PUT', $path, $data, $headers );
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
		$headers['Content-Type'] = 'application/json';
		$headers['plugin_key']   = $this->plugin_key;
		$headers['apikey']       = $this->api_key;

		if ( 'GET' != $method ) {
			$data = json_encode( $data );
		}

		$response = wp_remote_request(
			$this->get_url( $path ),
			array(
				'method'  => $method,
				'headers' => $headers,
				'body'    => $data,
			)
		);

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