<?php

namespace Tm4mlp\Meta_Box;

use Tm4mlp\Domain\Language;

class Translation_Box {

	const ID = 'tm4mlp_translation_box';

	const CONTEXT = 'side';

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
		return array();
	}
}