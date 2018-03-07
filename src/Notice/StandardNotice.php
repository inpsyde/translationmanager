<?php
/**
 * Class StandardNotice
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */

namespace Translationmanager\Notice;

/**
 * Class StandardNotice
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */
class StandardNotice implements Notice {

	/**
	 * Severity
	 *
	 * @since 1.0.0
	 *
	 * @var string The severity for the notice. Can be: success, warning or error.
	 */
	private $severity;

	/**
	 * Message
	 *
	 * @since 1.0.0
	 *
	 * @var string The notice message.
	 */
	private $message;

	/**
	 * StandardNotice constructor
	 *
	 * @since 1.0.0
	 *
	 * @param string $message  The notice message.
	 * @param string $severity The severity for the notice. Can be: success, warning or error.
	 */
	public function __construct( $message, $severity ) {

		$this->message  = $message;
		$this->severity = $severity;
	}

	/**
	 * @inheritdoc
	 */
	public function show() {

		include \Translationmanager\Functions\get_template( '/views/notice/standard.php' );
	}
}
