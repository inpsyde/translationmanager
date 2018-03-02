<?php

namespace Translationmanager\Auth;

/**
 * Class AuthRequest
 *
 * @since   1.0.0
 * @package Translationmanager\Auth
 */
interface AuthRequest {

	/**
	 * Check Against User Capability
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_User $user       The user to check against.
	 * @param string   $capability The capability to check against.
	 *
	 * @return mixed
	 */
	public function can( \WP_User $user, $capability );

	/**
	 * Check if Request is a valid one
	 *
	 * @since 1.0.0
	 *
	 * @param \Brain\Nonces\NonceInterface $nonce The nonce instance to use to check against the request.
	 *
	 * @return bool true if valid request, false otherwise
	 */
	public function request_is_valid( \Brain\Nonces\NonceInterface $nonce );
}
