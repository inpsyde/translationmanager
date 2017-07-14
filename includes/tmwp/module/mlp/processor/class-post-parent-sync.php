<?php # -*- coding: utf-8 -*-

namespace Tmwp\Module\Mlp\Processor;

use Tmwp\Module\Mlp\Connector;
use Tmwp\Translation_Data;

class Post_Parent_Sync implements Incoming_Processor {

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

		$source_post = $data->source_post();
		$post_data   = $data->get_meta( Post_Data_Builder::POST_DATA_KEY, Connector::DATA_NAMESPACE );

		if ( ! $source_post || ! $post_data || ! $source_post->post_parent ) {
			return;
		}

		$sync_on_update = true;
		if ( $data->get_meta( Post_Data_Builder::IS_UPDATE_KEY, Connector::DATA_NAMESPACE ) ) {
			$sync_on_update = apply_filters( 'tmwp_mlp_module_sync_post_parent_on_update', true, $data );
		}

		if ( ! $sync_on_update ) {
			return;
		}

		$target_site_id = $data->target_site_id();
		$source_site_id = $data->source_site_id();

		$related_parents = $content_relations->get_relations( $source_site_id, $source_post->post_parent, 'post' );

		$post_data[ 'post_parent' ] = array_key_exists( $target_site_id, $related_parents )
			? $related_parents[ $target_site_id ]
			: 0;

		$data->set_value( Post_Data_Builder::POST_DATA_KEY, $post_data, Connector::DATA_NAMESPACE );
	}
}