<?php

namespace Translationmanager\Functions;

/**
 * Add Translation Meta Box
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_translation_meta_box() {

	$screen = get_current_screen();

	if ( ! $screen ) {
		return;
	}

	// There shall be no translation option while creating a new entry.
	if ( 'add' === $screen->action ) {
		return;
	}

	( new \Translationmanager\MetaBox\TranslationBox() )->add_meta_box();
}
