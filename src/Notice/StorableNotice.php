<?php
/**
 * Storable Notice
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */

namespace Translationmanager\Notice;

/**
 * Class StorableNotice
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */
interface StorableNotice {

	/**
	 * Store Notice
	 *
	 * @since 1.0.0
	 *
	 * @param string $message  The notice message to store.
	 * @param string $severity The severity of the message.
	 *
	 * @return bool true if stored, false otherwise
	 */
	public function store( $message, $severity );

	/**
	 * Show Messages
	 *
	 * @since 1.0.0
	 *
	 * @return void All of the messages will be showed
	 */
	public function show();
}
