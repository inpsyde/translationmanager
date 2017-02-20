<?php

function tm4mlp_translation_box() {
	static $translation_box;

	if ( ! $translation_box ) {
		$translation_box = new \Tm4mlp\Meta_Box\Translation_Box();
	}

	$translation_box->add_meta_box();
}

add_action( 'add_meta_boxes', 'tm4mlp_translation_box' );