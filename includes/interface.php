<?php

/**
 * Interface for modules to serve languages.
 *
 * @return \Tmwp\Domain\Language[]
 */
function tmwp_get_languages() {
	global $current_site;

	return apply_filters( 'tmwp_get_languages', array(), $current_site->id );
}

/**
 * @return \Tmwp\Domain\Language
 */
function tmwp_get_current_language() {
	return apply_filters( 'tmwp_get_current_language', new \Tmwp\Domain\Language(get_locale(), null) );
}

function tmwp_get_current_lang_code() {
	$current_language = tmwp_get_current_language();

	return $current_language->get_lang_code();
}

function tmwp_get_languages_ids() {
	return array_keys( tmwp_get_languages() );
}

function tmwp_get_language_label( $lang_code ) {
	$languages = tmwp_get_languages();

	foreach ( $languages as $language ) {
		if ( $lang_code == $language['lang_code'] ) {
			return $language['label'];
		}
	}

	return '';
}