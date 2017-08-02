<?php

function translationmanager_add_translation_action() {
	static $add_translation;

	if (!$add_translation) {
		$add_translation = new \Translationmanager\Pages\Add_Translation();
	}

	$add_translation->dispatch();
}

/**
 * Manage menu items and pages.
 */
function translationmanager_add_translation_menu() {
	// Register admin page route.
	add_submenu_page(
		null,
		__('Translations', 'translationmanager'),
		__('Translations', 'translationmanager'),
		'read',
		'translationmanager_add_translation',
		'__return_false'
	);
}

add_action( 'admin_menu', 'translationmanager_add_translation_menu' );
add_action('load-dashboard_page_translationmanager_add_translation', 'translationmanager_add_translation_action');