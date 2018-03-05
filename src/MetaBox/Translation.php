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
 * Class Translation
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */
class Translation implements Metabox {

	/**
	 * The Metabox ID
	 *
	 * @since 1.0.0
	 *
	 * @var string The metabox ID
	 */
	const ID = 'translationmanager_translation_box';

	/**
	 * The Context
	 *
	 * @since 1.0.0
	 *
	 * @var string The metabox position
	 */
	const CONTEXT = 'side';

	/**
	 * Set Hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
	}

	/**
	 * @inheritdoc
	 */
	public function add_meta_box() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		// There shall be no translation option while creating a new entry.
		if ( 'add' === $screen->action ) {
			return;
		}

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
			[ $this, 'render_template' ],
			$box_screen,
			self::CONTEXT
		);
	}

	/**
	 * @inheritdoc
	 */
	public function render_template() {

		$template = Functions\get_template( 'views/meta-box/translation-box.php' );

		if ( ! $template || ! file_exists( $template ) ) {
			return;
		}

		require $template;
	}

	/**
	 * @inheritdoc
	 */
	public function nonce() {

		return new \Brain\Nonces\WpNonce( 'add_translation' );
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
}
