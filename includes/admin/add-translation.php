<?php

function tm4mlp_add_translation_action() {
	static $add_translation;

	if (!$add_translation) {
		$add_translation = new \Tm4mlp\Pages\Add_Translation();
	}

	$add_translation->dispatch();
}

/**
 * Manage menu items and pages.
 */
function tm4mlp_add_translation_menu() {
	// Register admin page route.
	add_submenu_page(
		null,
		__('Translations', 'translationmanager'),
		__('Translations', 'translationmanager'),
		'read',
		'tm4mlp_add_translation',
		'__return_false'
	);
}

add_action( 'admin_menu', 'tm4mlp_add_translation_menu' );
add_action('load-dashboard_page_tm4mlp_add_translation', 'tm4mlp_add_translation_action');