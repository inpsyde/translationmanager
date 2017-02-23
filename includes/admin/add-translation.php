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
	add_submenu_page(
		null,
		__('Translations', 'tm4mlp'),
		__('Translations', 'tm4mlp'),
		'read',
		'tm4mlp_add_translation',
		'tm4mlp_add_translation_action'
	);
}

add_action( 'admin_menu', 'tm4mlp_add_translation_menu' );