<?php

namespace Translationmanager\Auth;

use Brain\Nonces\NonceInterface;
use WP_User;

/**
 * Class Authable
 *
 * @since   1.0.0
 * @package Translationmanager\Auth
 */
interface Authable
{
    /**
     * Check Against User Capability
     *
     * @param \WP_User $user The user to check against.
     * @param string $capability The capability to check against.
     *
     * @return mixed
     * @since 1.0.0
     */
    public function can(WP_User $user, $capability);

    /**
     * Check if Request is a valid one
     *
     * @param \Brain\Nonces\NonceInterface $nonce The nonce instance to use to check against the request.
     *
     * @return bool true if valid request, false otherwise
     * @since 1.0.0
     */
    public function request_is_valid(NonceInterface $nonce);
}
