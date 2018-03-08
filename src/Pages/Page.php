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

	/**
	 * Return the URL of the page
	 *
	 * @since 1.0.0
	 *
	 * @return string The url of the page, may be the url for the current site or the network admin url page.
	 */
	public static function url();
}
