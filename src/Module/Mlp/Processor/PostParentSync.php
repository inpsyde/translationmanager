<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Processor;

use Translationmanager\Module\Mlp\Connector;
use Translationmanager\TranslationData;

class PostParentSync implements IncomingProcessor {

	/**
	 * @param TranslationData       $data
	 * @param \Mlp_Site_Relations    $site_relations
	 * @param \Mlp_Content_Relations $content_relations
	 *
	 * @return void
	 */
	public function process_incoming(
		TranslationData $data,
		\Mlp_Site_Relations $site_relations,
		\Mlp_Content_Relations $content_relations
	) {

		$source_post = $data->source_post();

		if ( ! $source_post || ! $source_post->post_parent ) {
			return;
		}

		$sync_on_update = true;
		if ( $data->get_meta( PostDataBuilder::IS_UPDATE_KEY, Connector::DATA_NAMESPACE ) ) {
			$sync_on_update = apply_filters( 'translationmanager_mlp_module_sync_post_parent_on_update', true, $data );
		}

		if ( ! $sync_on_update ) {
			return;
		}

		$target_site_id = $data->target_site_id();
		$source_site_id = $data->source_site_id();

		$related_parents = $content_relations->get_relations( $source_site_id, $source_post->post_parent, 'post' );

		$parent = array_key_exists( $target_site_id, $related_parents )
			? $related_parents[ $target_site_id ]
			: 0;

		$data->set_value( 'post_parent', $parent );
	}
}