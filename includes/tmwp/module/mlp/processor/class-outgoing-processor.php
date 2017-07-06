<?php # -*- coding: utf-8 -*-

namespace Tmwp\Module\Mlp\Processor;

use Tmwp\Translation_Data;

interface Outgoing_Processor extends Processor {

	/**
	 * @param Translation_Data       $data
	 * @param \Mlp_Site_Relations    $site_relations
	 * @param \Mlp_Content_Relations $content_relations
	 *
	 * @return void
	 */
	public function process_outgoing(
		Translation_Data $data,
		\Mlp_Site_Relations $site_relations,
		\Mlp_Content_Relations $content_relations
	);

}