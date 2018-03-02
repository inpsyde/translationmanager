<?php

namespace Translationmanager\Action;

/**
 * Class ActionHandle
 *
 * @since   1.0.0
 * @package Translationmanager\Action
 */
interface ActionHandle {

	/**
	 * Handle Action Request
	 *
	 * @since 1.0.0
	 *
	 * @throws ActionException In case the request cannot be handled correctly.
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
