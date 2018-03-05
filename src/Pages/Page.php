<?php
/**
 * Page Interface
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */

namespace Translationmanager\Pages;

/**
 * Class Page
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */
interface Page {

	/**
	 * Register Page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_page();

	/**
	 * Render Page Template
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_template();
}
