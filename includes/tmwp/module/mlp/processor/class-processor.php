<?php # -*- coding: utf-8 -*-

namespace Tmwp\Module\Mlp\Processor;

use Tmwp\Translation_Data;

interface Processor {

	/**
	 * @param Translation_Data $data
	 *
	 * @return bool
	 */
	public function enabled( Translation_Data $data );

}