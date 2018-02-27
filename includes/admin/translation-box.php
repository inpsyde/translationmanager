<?php

function translationmanager_translation_box() {
	static $translation_box;

	if ( get_current_screen()
	     && 'add' === get_current_screen()->action
	) {
		// There shall be no translation option while creating a new entry.
		return;
	}

	if ( ! $translation_box ) {
		$translation_box = new \Translationmanager\Meta_Box\Translation_Box();
	}

	$translation_box->add_meta_box();
}

add_action( 'add_meta_boxes', 'translationmanager_translation_box' );
