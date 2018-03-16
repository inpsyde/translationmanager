<?php

namespace Translationmanager\Request;

/**
 * Class RequestHandleable
 *
 * @since   1.0.0
 * @package Translationmanager\Request
 */
interface RequestHandleable {

	/**
	 * Handle Request Request
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function handle();

	/**
	 * Check if request is valid
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if a valid request, false otherwise
	 */
	public function is_valid_request();

	/**
	 * Retrieve the requested data
	 *
	 * @since 1.0.0
	 *
	 * @return array A list of data retrieved by the request
	 */
	public function request_data();
}