<?php // -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Processor;

use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Module\Mlp\Connector;
use Translationmanager\TranslationData;

class PostSaver implements IncomingProcessor {

	const SAVED_POST_KEY = 'saved_post';

	/**
	 * @param TranslationData $data
	 * @param Adapter         $adapter
	 *
	 * @return void
	 */
	public function process_incoming( TranslationData $data, Adapter $adapter ) {

		$post_vars = get_object_vars( new \WP_Post( new \stdClass() ) );
		$post_data = [];

		foreach ( $post_vars as $key => $value ) {
			if ( $data->has_value( $key ) ) {
				$post_data[ $key ] = $data->get_value( $key );
			}
		}

		switch_to_blog( $data->target_site_id() );

		$existing_id = array_key_exists( 'ID', $post_data ) ? $post_data['ID'] : 0;

		// Save post with all the data.
		$target_post_id = wp_insert_post( $post_data, true );

		do_action(
			'translationmanager_log',
			[
				'message' => 'Incoming post data from API processed.',
				'context' => [
					'Post data ID'   => $existing_id . ' (should equal "Source post ID")',
					'Source post ID' => $data->source_post_id() . ' (should equal "Post data ID")',
					'Result'         => is_wp_error( $target_post_id )
						? $target_post_id->get_error_message()
						: "Post ID {$target_post_id} saved correctly.",
					'Target lang'    => $data->target_language(),
					'Target site'    => $data->target_site_id(),
					'Source site'    => $data->source_site_id(),
				],
			]
		);

		if ( is_wp_error( $target_post_id ) ) {
			$target_post_id = 0;
		}

		$target_post = $target_post_id ? get_post( $target_post_id ) : null;

		restore_current_blog();

		if ( ! $target_post ) {
			return;
		}

		$sync_on_update = true;
		if ( $data->get_meta( PostDataBuilder::IS_UPDATE_KEY, Connector::DATA_NAMESPACE ) ) {
			$sync_on_update = apply_filters( 'translationmanager_mlp_module_sync_post_relation_on_update', true, $data );
		}

		// If it is a new post creation, link created post with source post.
		if ( $sync_on_update ) {
			$adapter->set_relation(
				$data->source_site_id(),
				$data->target_site_id(),
				$data->source_post_id(),
				$target_post->ID,
				'post'
			);
		}

		$data->set_meta( self::SAVED_POST_KEY, $target_post, Connector::DATA_NAMESPACE );
	}
}
