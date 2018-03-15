<?php
/**
 * Class ValidateAuthRequest
 *
 * @since   1.0.0
 * @package Translationmanager\Request
 */

namespace Translationmanager\Auth;

use Brain\Nonces\RequestGlobalsContext;

/**
 * Class ValidateAuthRequest
 *
 * @since   1.0.0
 * @package Translationmanager\Request
 */
class AuthRequestValidator implements AuthRequest {

	/**
	 * @inheritdoc
	 */
	public function can( \WP_User $user, $capability ) {

		return user_can( $user, $capability );
	}

	/**
	 * @inheritdoc
	 */
	public function request_is_valid( \Brain\Nonces\NonceInterface $nonce ) {

		return $nonce->validate( new RequestGlobalsContext() );
	}
}
