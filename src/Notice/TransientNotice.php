<?php
/**
 * Transient Notice
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */

namespace Translationmanager\Notice;

/**
 * Class TransientNotice
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */
class TransientNotice implements StorableNotice {

	/**
	 * Transient Key
	 *
	 * @since 1.0.0
	 *
	 * @var string The transient key
	 */
	private $key;

	/**
	 * TransientNotice constructor
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The transient key.
	 */
	public function __construct( $key ) {

		$this->key = $key;
	}

	/**
	 * @inheritdoc
	 */
	public function store( $message, $severity ) {

		$severity = sanitize_key( $severity );

		$transient                = $this->transient();
		$transient[ $severity ][] = wp_kses_post( $message );

		return set_transient( $this->key, $transient );
	}

	/**
	 * @inheritdoc
	 */
	public function show() {

		$messages = $this->get_cleaned_transient_messages();

		if ( ! $messages ) {
			return;
		}

		foreach ( $messages as $severity => $list ) {
			$message = '<li>' . join( '</li>', $list ) . '</li>';
			include \Translationmanager\Functions\get_template( '/views/notice/list.php' );
		}

		$this->clean();
	}

	/**
	 * Clean the transient
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function clean() {

		delete_transient( $this->key );
	}

	/**
	 * Get the cleaned version of the messages
	 *
	 * @since 1.0.0
	 *
	 * @uses  wp_kses_post() To clean the message string.
	 * @uses  sanitize_key() To clean the severity key.
	 *
	 * @return array
	 */
	private function get_cleaned_transient_messages() {

		$cleaned = [];

		foreach ( $this->transient() as $severity => $messages ) {
			$severity = sanitize_key( $severity );

			foreach ( $messages as $message ) {
				$cleaned[ $severity ][] = wp_kses_post( $message );
			}
		}

		return $cleaned;
	}

	/**
	 * Get the transient
	 *
	 * @since 1.0.0
	 *
	 * @return array The transient value
	 */
	private function transient() {

		return array_filter( (array) get_transient( $this->key ) );
	}
}
