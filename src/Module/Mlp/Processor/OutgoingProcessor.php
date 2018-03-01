<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Processor;

use Translationmanager\TranslationData;

interface OutgoingProcessor extends Processor {

	/**
	 * Process Outgoing
	 *
	 * @param TranslationData       $data
	 * @param \Mlp_Site_Relations    $site_relations
	 * @param \Mlp_Content_Relations $content_relations
	 *
	 * @return void
	 */
	public function process_outgoing(
		TranslationData $data,
		\Mlp_Site_Relations $site_relations,
		\Mlp_Content_Relations $content_relations
	);

}