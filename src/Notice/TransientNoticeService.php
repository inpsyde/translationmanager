<?php
/**
 * Class TransientNoticeService
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */

namespace Translationmanager\Notice;

/**
 * Class TransientNoticeService
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */
class TransientNoticeService {

	/**
	 * Notice
	 *
	 * @since 1.0.0
	 *
	 * @return \Translationmanager\Notice\TransientNotice Everytime the same instance
	 */
	private static function notice() {

		static $notice = null;

		if ( null === $notice ) {
			$notice = new TransientNotice( 'translationmanager_general_notices' );
		}

		return $notice;
	}

	/**
	 * Add Notice
	 *
	 * @since 1.0.0
	 *
	 * @param string $message  The message to store.
	 * @param string $severity The severity under which the message must be stored.
	 *
	 * @return bool True on success false on failure
	 */
	public static function add_notice( $message, $severity ) {

		return self::notice()->store( $message, $severity );
	}

	/**
	 * Show Messages
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function show() {

		self::notice()->show();
	}
}
