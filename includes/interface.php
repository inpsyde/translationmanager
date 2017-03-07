<?php

/**
 * Interface for modules to serve languages.
 *
 * @return \Tm4mlp\Domain\Language[]
 */
function tm4mlp_get_languages() {
	global $current_site;

	return apply_filters( 'tm4mlp_get_languages', array(), $current_site->id );
}

function tm4mlp_get_languages_ids() {
	return array_keys( tm4mlp_get_languages() );
}

function tm4mlp_get_language_label( $lang_code ) {
	$languages = tm4mlp_get_languages();

	foreach ( $languages as $language ) {
		if ( $lang_code == $language['lang_code'] ) {
			return $language['label'];
		}
	}

	return '';
}