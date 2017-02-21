<?php

function tm4mlp_translation_box() {
	static $translation_box;

	if ( get_current_screen()
	     && 'add' == get_current_screen()->action
	) {
		// There shall be no translation option while creating a new entry.
		return;
	}

	if ( ! $translation_box ) {
		$translation_box = new \Tm4mlp\Meta_Box\Translation_Box();
	}

	$translation_box->add_meta_box();

	remove_meta_box(
		\Tm4mlp\Meta_Box\Translation_Box::ID,
		TM4MLP_TRANS_STATUS,
		\Tm4mlp\Meta_Box\Translation_Box::CONTEXT
	);
}

add_action( 'add_meta_boxes', 'tm4mlp_translation_box' );
