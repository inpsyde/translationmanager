<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Processor;

use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Module\Mlp\Connector;
use Translationmanager\TranslationData;

class PostDataBuilder implements IncomingProcessor {

	const IS_UPDATE_KEY = 'is-update';

	private static $unwanted_data = [
		'ID'                => '',
		'guid'              => '',
		'ancestors'         => '',
		'page_template'     => '',
		'post_category'     => '',
		'tags_input'        => '',
		'post_modified_gmt' => '',
		'filter'            => '',
	];

	public function process_incoming( TranslationData $data, Adapter $adapter ) {

		$source_post = $data->source_post();

		if ( ! $source_post ) {
			return;
		}

		/** @var array $linked_posts Array with site ID as keys and content ID as values. */
		$linked_posts = $adapter->relations( $data->source_site_id(), $source_post->ID, 'post' );

		$target_site_id = $data->target_site_id();

		switch_to_blog( $data->target_site_id() );

		$linked_post = array_key_exists( $target_site_id, $linked_posts )
			? get_post( $linked_posts[ $target_site_id ] )
			: null;

		restore_current_blog();

		$linked_post_data = $linked_post ? $linked_post->to_array() : [];

		$post_vars = get_object_vars( new \WP_Post( new \stdClass() ) );

		// Let's extract only post data from received translation data
		$translated_data = [];
		foreach ( array_keys( $post_vars ) as $key ) {
			if ( $data->has_value( $key ) ) {
				$translated_data[ $key ] = $data->get_value( $key );
			}
		}

		$source_post_data = $source_post->to_array();
		unset( $source_post_data['post_parent'] );

		// Merge all data we know...
		$post_data = array_merge( $source_post_data, $linked_post_data, $translated_data );
		// ... but remove problematic properties...
		$post_data = array_diff_key( $post_data, self::$unwanted_data );
		// ... and force ID to be existing linked post if exists.
		$linked_post and $post_data['ID'] = $linked_post->ID;
		// Set back all post data in root namespace
		foreach ( $post_data as $key => $value ) {
			$data->set_value( $key, $value );
		}

		$data->set_meta( self::IS_UPDATE_KEY, (bool) $linked_post, Connector::DATA_NAMESPACE );
	}
}