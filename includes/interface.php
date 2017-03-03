<?php

function tm4mlp_get_languages() {
	return apply_filters( 'tm4mlp_get_languages', array() );
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