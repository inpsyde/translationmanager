<?php

function tmwp_add_translation_action() {
	static $add_translation;

	if (!$add_translation) {
		$add_translation = new \Tmwp\Pages\Add_Translation();
	}

	$add_translation->dispatch();
}

/**
 * Manage menu items and pages.
 */
function tmwp_add_translation_menu() {
	// Register admin page route.
	add_submenu_page(
		null,
		__('Translations', 'translationmanager'),
		__('Translations', 'translationmanager'),
		'read',
		'tmwp_add_translation',
		'__return_false'
	);
}

add_action( 'admin_menu', 'tmwp_add_translation_menu' );
add_action('load-dashboard_page_tmwp_add_translation', 'tmwp_add_translation_action');