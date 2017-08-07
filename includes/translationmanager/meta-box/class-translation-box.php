<?php

namespace Translationmanager\Meta_Box;

use Translationmanager\Admin\Options_Page;
use Translationmanager\Domain\Language;

class Translation_Box {

	const ID = 'translationmanager_translation_box';

	const CONTEXT = 'side';
	protected $projects;

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
		 * @return array
		 */
		$translationmanager_translation_box_screen = apply_filters(
			'translationmanager_translation_box_screen',
			get_post_types( array( 'show_ui' => true, '_builtin' => true ) )
		);

		add_meta_box(
			static::ID,
			__( 'Inquiry for translation', 'translationmanager' ),
			array( $this, 'dispatch' ),
			$translationmanager_translation_box_screen,
			self::CONTEXT
		);
	}

	public function dispatch() {
		/** @var string $template */
		$template = translationmanager_get_template( 'admin/meta-box/translation-box.php' );
		if ( ! $template || ! file_exists( $template ) ) {
			return;
		}

		require $template;
	}

	protected function get_customer_key() {
		return get_option(Options_Page::REFRESH_TOKEN);
	}

	/**
	 * @todo Fetch real languages.
	 *
	 * @return Language[]
	 */
	public function get_languages() {
		return translationmanager_get_languages();
	}

	public function get_projects() {
		if ( $this->projects ) {
			return $this->projects;
		}

		/** @var \WP_Term[] $terms */
		$terms = get_terms(
			array(
				'taxonomy'   => TRANSLATIONMANAGER_TAX_PROJECT,
				'hide_empty' => false,
				'meta_query' => array(
					array(
						'key'     => '_tmanager_order_id',
						'compare' => 'NOT EXISTS',
						'value'   => '',
					),
				)
			)
		);

		$projects = array();
		foreach ( $terms as $term ) {
			$projects[ $term->term_id ] = $term->name;
		}

		return $projects;
	}

	public function get_recent_project_name() {
		if ( ! $this->get_recent_project_id() ) {
			return __( 'New project', 'translationmanager' );
		}

		return get_term_field( 'name', $this->get_recent_project_id() );
	}

	/**
	 * @return int|null
	 */
	public function get_recent_project_id() {
		return get_user_meta( get_current_user_id(), 'translationmanager_project_recent', true );
	}
}