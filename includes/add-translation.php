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
	global $_registered_pages;

	$menu_slug = 'tm4mlp_add_translation';
	$hook      = get_plugin_page_hookname( $menu_slug, '' );

	if ( ! $hook ) {
		throw new \Exception( 'TM4MLP: WordPress broke the admin logic.' );
	}

	add_action( $hook, 'tm4mlp_add_translation_action' );
	$_registered_pages[ $hook ] = true;
}

add_action( 'admin_menu', 'tm4mlp_add_translation_menu' );