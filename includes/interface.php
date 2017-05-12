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

/**
 * @return \Tm4mlp\Domain\Language
 */
function tm4mlp_get_current_language() {
	return apply_filters( 'tm4mlp_get_current_language', new \Tm4mlp\Domain\Language(get_locale(), null) );
}

function tm4mlp_get_current_lang_code() {
	$current_language = tm4mlp_get_current_language();

	return $current_language->get_lang_code();
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