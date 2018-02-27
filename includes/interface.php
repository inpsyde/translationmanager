<?php

/**
 * Interface for modules to serve languages.
 *
 * @return \Translationmanager\Domain\Language[]
 */
function translationmanager_get_languages() {

	return apply_filters( 'translationmanager_get_languages', array(), get_current_blog_id() );
}

/**
 * @return \Translationmanager\Domain\Language
 */
function translationmanager_get_current_language() {
	return apply_filters( 'translationmanager_get_current_language', new \Translationmanager\Domain\Language(get_locale(), null) );
}

function translationmanager_get_current_lang_code() {
	$current_language = translationmanager_get_current_language();

	return $current_language->get_lang_code();
}

function translationmanager_get_languages_ids() {
	return array_keys( translationmanager_get_languages() );
}

function translationmanager_get_language_label( $lang_code ) {
	$languages = translationmanager_get_languages();

	foreach ( $languages as $language ) {
		if ( $lang_code === $language['lang_code'] ) {
			return $language['label'];
		}
	}

	return '';
}