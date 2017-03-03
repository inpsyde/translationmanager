<?php

namespace Tm4mlp\Meta_Box;

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
	 * @return mixed|void
	 */
	public function get_languages() {
		return apply_filters(
			'tm4mlp_get_languages',
			array(
				array(
					'lang_code' => 'de-DE',
					'label' => 'Deutsch'
				),
				array(
					'lang_code' => 'en-GB',
					'label' => 'English'
				),
			)
		);
	}
}