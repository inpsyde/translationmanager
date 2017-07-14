<?php

namespace Tmwp\Module;

use Tmwp\Translation_Data;

class WordPress_Seo {

	const _NAMESPACE = 'wordpress_seo';

	/**
	 * Store WordPress SEO meta fields related to source post into translation data, using meta for fields that should
	 * not be translated.
	 *
	 * @param Translation_Data $data
	 */
	public function prepare_outgoing( Translation_Data $data ) {

		if ( ! class_exists( 'WPSEO_Meta' ) || ! $data->is_valid() ) {
			return;
		}

		$source_post_id = $data->source_post_id();

		$to_translate = array(
			'title',
			'metadesc',
			'metakeywords',
			'bctitle',
		);

		$to_not_translate = array(
			'meta-robots-noindex',
			'meta-robots-nofollow',
			'meta-robots-adv',
		);

		foreach ( $to_translate as $key ) {
			$field = get_post_meta( $source_post_id, \WPSEO_Meta::$meta_prefix . $key, true );
			$data->set_value( $key, $field, self::_NAMESPACE );
		}

		foreach ( $to_not_translate as $key ) {
			$field = get_post_meta( $source_post_id, \WPSEO_Meta::$meta_prefix . $key, true );
			$data->set_meta( $key, $field, self::_NAMESPACE );
		}
	}

	/**
	 * After a translation post has been updated, updates its meta merging translated data and meta data that were set
	 * on API request.
	 *
	 * @wp-hook tmwp_updated_post
	 *
	 * @param \WP_Post         $translated_post
	 * @param Translation_Data $data
	 */
	public function update_translation( \WP_Post $translated_post, Translation_Data $data ) {

		if ( ! $data->is_valid() ) {
			return;
		}

		$not_translated = $data->get_meta( self::_NAMESPACE );
		$translated     = $data->get_value( self::_NAMESPACE );
		$all_meta       = array_filter( array_merge( $not_translated, $translated ) );

		foreach ( $all_meta as $key => $value ) {
			$exists = get_post_meta( $translated_post->ID, \WPSEO_Meta::$meta_prefix . $key );

			// Existent non-translated data are not updated
			if ( ! $exists && isset( $translated[ $key ] ) ) {
				update_post_meta( $translated_post->ID, \WPSEO_Meta::$meta_prefix . $key, $value );
			}
		}
	}
}