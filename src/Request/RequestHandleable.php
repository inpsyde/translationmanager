<?php

namespace Translationmanager\Request;

/**
 * Class RequestHandleable
 *
 * @since   1.0.0
 * @package Translationmanager\Request
 */
interface RequestHandleable
{
    /**
     * Handle Request Request
     *
     * @return void
     * @since 1.0.0
     */
    public function handle();

    /**
     * Check if request is valid
     *
     * @return bool True if a valid request, false otherwise
     * @since 1.0.0
     */
    public function is_valid_request();

    /**
     * Retrieve the requested data
     *
     * @return array A list of data retrieved by the request
     * @since 1.0.0
     */
    public function request_data();
}
