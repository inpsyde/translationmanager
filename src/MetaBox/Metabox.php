<?php
/**
 * MetaBox
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */

namespace Translationmanager\MetaBox;

/**
 * Interface Metabox
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */
interface Metabox {

	/**
	 * Add Metabox
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_meta_box();

	/**
	 * Render Meta Box Template
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_template();

	/**
	 * Create Nonce Instance
	 *
	 * @since 1.0.0
	 *
	 * @return \Brain\Nonces\WpNonce The nonce instance
	 */
	public function nonce();
}
