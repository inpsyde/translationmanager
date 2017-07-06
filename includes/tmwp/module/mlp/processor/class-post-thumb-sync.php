<?php # -*- coding: utf-8 -*-

namespace Tmwp\Module\Mlp\Processor;

use Tmwp\Module\Mlp\Connector;
use Tmwp\Module\Mlp\Utils;
use Tmwp\Translation_Data;

class Post_Thumb_Sync implements Incoming_Processor {

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

		$saved_post = $data->get_meta( Post_Saver::SAVED_POST_KEY, Connector::DATA_NAMESPACE );
		$is_update  = $data->get_meta( Post_Data_Builder::IS_UPDATE_KEY, Connector::DATA_NAMESPACE );

		if ( ! $saved_post || $is_update ) {
			return;
		}

		$source_site_id = $data->source_site_id();


		switch_to_blog( $source_site_id );
		$source_thumb_id = get_post_thumbnail_id( $data->source_post_id() );

		$target_thumb_id = 0;

		if ($source_thumb_id) {
			$image_sync = new Utils\Image_Sync();
			$target_thumb_id = $image_sync->copy_image( $source_thumb_id, $source_site_id, $data->target_site_id() );
		}

		restore_current_blog();

		if ($target_thumb_id) {
			set_post_thumbnail( $saved_post, $target_thumb_id );
		}

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