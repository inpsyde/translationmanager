<?php // -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Processor;

use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Module\Mlp\Connector;
use Translationmanager\TranslationData;

/**
 * Class PostThumbSync
 *
 * @package Translationmanager\Module\Mlp\Processor
 */
class PostThumbSync implements IncomingProcessor {

	/**
	 * @param TranslationData $data
	 * @param Adapter         $adapter
	 *
	 * @return void
	 */
	public function process_incoming( TranslationData $data, Adapter $adapter ) {

		$saved_post = $data->get_meta( PostSaver::SAVED_POST_KEY, Connector::DATA_NAMESPACE );

		if ( ! $saved_post || ! post_type_supports( $saved_post->post_type, 'thumbnail' ) ) {
			return;
		}

		$sync_on_update = true;
		if ( $data->get_meta( PostDataBuilder::IS_UPDATE_KEY, Connector::DATA_NAMESPACE ) ) {
			$sync_on_update = apply_filters( 'translationmanager_mlp_module_sync_post_thumb_on_update', true, $data );
		}

		if ( ! $sync_on_update ) {
			return;
		}

		$source_site_id = $data->source_site_id();

		switch_to_blog( $source_site_id );
		$source_thumb_id = get_post_thumbnail_id( $data->source_post_id() );
		restore_current_blog();

		$target_thumb_id = 0;

		if ( $source_thumb_id ) {
			$image_sync      = Connector::utils()->image_sync( $adapter );
			$target_thumb_id = $image_sync->copy_image( $source_thumb_id, $source_site_id, $data->target_site_id() );
		}

		if ( $target_thumb_id ) {
			switch_to_blog( $data->target_site_id() );
			set_post_thumbnail( $saved_post, $target_thumb_id );
			restore_current_blog();
		}
	}
}
