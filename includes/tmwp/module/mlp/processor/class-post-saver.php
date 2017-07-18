<?php # -*- coding: utf-8 -*-

namespace Tmwp\Module\Mlp\Processor;

use Inpsyde\Wonolog\Data\Log;
use Monolog\Logger;
use Tmwp\Module\Mlp\Connector;
use Tmwp\Translation_Data;

class Post_Saver implements Incoming_Processor {

	const SAVED_POST_KEY = 'saved_post';

	/**
	 * @param Translation_Data       $data
	 * @param \Mlp_Site_Relations    $site_relations
	 * @param \Mlp_Content_Relations $content_relations
	 *
	 * @return void
	 */
	public function process_incoming(
		Translation_Data $data,
		\Mlp_Site_Relations $site_relations,
		\Mlp_Content_Relations $content_relations
	) {

		$post_vars = get_object_vars( new \WP_Post( new \stdClass() ) );
		$post_data = array();

		foreach( $post_vars as $key => $value ) {
			if ( $data->has_value( $key )  ) {
				$post_data[ $key ] = $data->get_value( $key );
			}
		}

		switch_to_blog( $data->target_site_id() );

		// Save post with all the data
		$target_post_id = wp_insert_post( $post_data );

		do_action( 'wonolog.log', new Log( __METHOD__ . ': Target post is ' . (int) $target_post_id, Logger::DEBUG ) );

		$target_post = $target_post_id && ! is_wp_error( $target_post_id ) ? get_post( $target_post_id ) : null;

		restore_current_blog();

		if ( ! $target_post) {
			return;
		}

		$sync_on_update = true;
		if ( $data->get_meta( Post_Data_Builder::IS_UPDATE_KEY, Connector::DATA_NAMESPACE ) ) {
			$sync_on_update = apply_filters( 'tmwp_mlp_module_sync_post_relation_on_update', true, $data );
		}

		// If it is a new post creation, link created post with source post
		if ( $sync_on_update ) {
			$content_relations->set_relation(
				$data->source_site_id(),
				$data->target_site_id(),
				$data->source_post_id(),
				$target_post->ID
			);
		}

		$data->set_meta( self::SAVED_POST_KEY, $target_post, Connector::DATA_NAMESPACE );
	}
}