<?php
/**
 * Translation Box
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */

namespace Translationmanager\MetaBox;

use Translationmanager\Pages\PageOptions;
use Translationmanager\Functions;
use Translationmanager\Domain\Language;

/**
 * Class TranslationBox
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */
class TranslationBox {

	/**
	 * @since 1.0.0
	 */
	const ID = 'translationmanager_translation_box';

	/**
	 * @since 1.0.0
	 */
	const CONTEXT = 'side';

	/**
	 * @var @since 1.0.0
	 */
	private $projects;

	/**
	 * @since 1.0.0
	 */
	public function add_meta_box() {

		/**
		 * Define where the translation box shall be shown.
		 *
		 * Add or remove post-types from the array.
		 * By default it will be shown on 'post' and 'page'.
		 * The value goes right in the `add_meta_box` screen argument.
		 *
		 * @see add_meta_box()
		 *
		 * @var array Screens for `add_meta_box()`.
		 *
		 * @return array The post types list
		 */
		$box_screen = apply_filters( 'translationmanager_translation_box_screen', get_post_types( [
			'show_ui'  => true,
			'_builtin' => true,
		] ) );

		add_meta_box(
			static::ID,
			esc_html__( 'Inquiry for translation', 'translationmanager' ),
			[ $this, 'dispatch' ],
			$box_screen,
			self::CONTEXT
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function dispatch() {

		$template = Functions\get_template( 'views/meta-box/translation-box.php' );

		if ( ! $template || ! file_exists( $template ) ) {
			return;
		}

		require $template;
	}

	/**
	 * Customer Key
	 *
	 * @since 1.0.0
	 *
	 * Actually used within the translation-box.php only, don't remove it.
	 *
	 * @return mixed Whatever the get_option() returns.
	 */
	public function get_customer_key() {

		return get_option( PageOptions::REFRESH_TOKEN );
	}

	/**
	 *
	 * @since 1.0.0
	 * @todo  Fetch real languages.
	 *
	 * @return Language[]
	 */
	public function get_languages() {

		return Functions\get_languages();
	}

	/**
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_projects() {

		return \Translationmanager\Functions\projects();
	}

	/**
	 *
	 * @since 1.0.0
	 * @return int|null|string|\WP_Error
	 */
	public function get_recent_project_name() {

		if ( ! $this->get_recent_project_id() ) {
			return esc_html__( 'New project', 'translationmanager' );
		}

		return get_term_field( 'name', $this->get_recent_project_id() );
	}

	/**
	 *
	 * @since 1.0.0
	 * @return int|null
	 */
	public function get_recent_project_id() {

		return get_user_meta( get_current_user_id(), 'translationmanager_project_recent', true );
	}

	/**
	 * Nonce Instance
	 *
	 * @since 1.0.0
	 *
	 * @return \Brain\Nonces\WpNonce The nonce instance
	 */
	public function nonce() {

		return new \Brain\Nonces\WpNonce( 'add_translation' );
	}
}
