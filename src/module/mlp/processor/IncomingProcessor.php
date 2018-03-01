<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Processor;

use Translationmanager\TranslationData;

interface IncomingProcessor extends Processor {

	/**
	 * Process Incoming
	 *
	 * @since 1.0.0
	 *
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
	);

}
