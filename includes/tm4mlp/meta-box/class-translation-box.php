<?php

namespace Tm4mlp\Meta_Box;

use Tm4mlp\Domain\Language;

class Translation_Box {

	const ID = 'tm4mlp_translation_box';

	const CONTEXT = 'side';
	protected $projects;

	public function add_meta_box() {
		add_meta_box(
			static::ID,
			__( 'Inquiry for translation', 'tm4mlp' ),
			array( $this, 'dispatch' ),
			apply_filters( 'tm4mlp_translation_box_screen', [ 'post', 'page' ] ),
			self::CONTEXT
		);
	}

	public function dispatch() {
		/** @var string $template */
		$template = tm4mlp_get_template( 'admin/meta-box/translation-box.php' );
		if ( ! $template || ! file_exists( $template ) ) {
			return;
		}

		require $template;
	}

	/**
	 * @todo Fetch real languages.
	 *
	 * @return Language[]
	 */
	public function get_languages() {
		return tm4mlp_get_languages();
	}

	public function get_projects() {
		if ( $this->projects ) {
			return $this->projects;
		}

		/** @var \WP_Term[] $terms */
		$terms = get_terms(
			array(
				'taxonomy'   => TM4MLP_TAX_PROJECT,
				'hide_empty' => false,
				'meta_query' => array(
					array(
						'key'     => '_tm4mlp_order_id',
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
			return __( 'New project', 'tm4mlp' );
		}

		return get_term_field( 'name', $this->get_recent_project_id() );
	}

	/**
	 * @return int|null
	 */
	public function get_recent_project_id() {
		return get_user_meta( get_current_user_id(), 'tm4mlp_project_recent', true );
	}
}