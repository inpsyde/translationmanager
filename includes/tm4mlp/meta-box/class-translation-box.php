<?php

namespace Tm4mlp\Meta_Box;

class Translation_Box {

	const ID = 'tm4mlp_translation_box';

	public function add_meta_box() {
		add_meta_box(
			static::ID,
			__( 'Inquiry for translation', 'tm4mlp' ),
			array( $this, 'dispatch' ),
			apply_filters( 'tm4mlp_translation_box_screen', null ),
			'side'
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
}