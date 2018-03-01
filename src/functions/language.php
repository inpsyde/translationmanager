<?php

namespace Translationmanager\Functions;

use Translationmanager\Domain\Language;

/**
 * Interface for modules to serve languages.
 *
 * @since 1.0.0
 *
 * @return \Translationmanager\Domain\Language[]
 */
function get_languages() {

	/**
	 * Get Languages
	 *
	 * @since 1.0.0
	 *
	 * @param array An empty array to fill with \Translationmanager\Domain\Language instances.
	 * @param int The current blog ID.
	 */
	return apply_filters( 'translationmanager_languages', [], get_current_blog_id() );
}

/**
 * Get the languages by the site id
 *
 * @since 1.0.0
 *
 * @param int $site_id The id of the site.
 *
 * @return array A list of languages by site.
 */
function get_languages_by_site_id( $site_id ) {

	global $wpdb;
	$languages      = [];
	$site_relations = new \Mlp_Site_Relations( $wpdb, 'mlp_site_relations' );

	$sites = $site_relations->get_related_sites( $site_id );

	foreach ( $sites as $site ) {
		$lang_iso = mlp_get_blog_language( $site, false );

		$languages[ $site ] = new Language( $lang_iso, mlp_get_lang_by_iso( $lang_iso ) );
	}

	return $languages;
}

/**
 * Current Language
 *
 * @since 1.0.0
 *
 * @return \Translationmanager\Domain\Language The instance of the class.
 */
function current_language() {

	/**
	 * Current Language
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Domain\Language The instance of the class.
	 */
	return apply_filters( 'translationmanager_current_language', new \Translationmanager\Domain\Language( get_locale(), null ) );
}

/**
 * Retrieve Current Language Code
 *
 * @since 1.0.0
 *
 * @return mixed Whatever \Translationmanager\Domain\Language::get_lang_code() returns
 */
function current_lang_code() {

	$current_language = current_language();

	return $current_language->get_lang_code();
}

function translationmanager_get_language_label( $lang_code ) {

	$languages = get_languages();

	foreach ( $languages as $language ) {
		if ( $lang_code === $language['lang_code'] ) {
			return $language['label'];
		}
	}

	return '';
}
