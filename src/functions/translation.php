<?php

namespace Translationmanager\Functions;

/**
 * Add Translation
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_translation_action() {

	static $add_translation = null;

	if ( null === $add_translation ) {
		$add_translation = new \Translationmanager\Pages\PageAddTranslation();
	}

	$add_translation->dispatch();
}

/**
 * Add Translation menu page
 *
 * @todo  Move into PageAddTranslation class?
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_translation_menu_page() {

	add_submenu_page(
		null,
		esc_html__( 'Translations', 'translationmanager' ),
		esc_html__( 'Translations', 'translationmanager' ),
		'read',
		'translationmanager_add_translation',
		'__return_false'
	);
}

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
