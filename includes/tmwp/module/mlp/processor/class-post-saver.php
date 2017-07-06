<?php # -*- coding: utf-8 -*-

namespace Tmwp\Module\Mlp\Processor;

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

		$post_data = $data->get_meta( Post_Data_Builder::POST_DATA_KEY, Connector::DATA_NAMESPACE );

		if ( ! $post_data ) {
			return;
		}

		switch_to_blog( $data->target_site_id() );

		// Save post with all the data
		$target_post_id = wp_update_post( $post_data );

		if (
			! $target_post_id
			|| is_wp_error( $target_post_id )
			|| ! ( $target_post = get_post( $target_post_id ) )
		) {

			restore_current_blog();

			return;
		}

		// If it is a new post creation, link created post with source post
		if ( ! $data->get_meta( Post_Data_Builder::IS_UPDATE_KEY ) ) {
			$content_relations->set_relation(
				$data->source_site_id(),
				$data->target_site_id(),
				$data->source_post_id(),
				$target_post->ID
			);
		}

		$data->set_meta( self::SAVED_POST_KEY, $target_post, Connector::DATA_NAMESPACE );

		restore_current_blog();

	}

	/**
	 * @param Translation_Data $data
	 *
	 * @return bool
	 */
	public function enabled( Translation_Data $data ) {

		return true;
	}
}