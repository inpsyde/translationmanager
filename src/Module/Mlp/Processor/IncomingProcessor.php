<?php // -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Processor;

use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\TranslationData;

interface IncomingProcessor extends Processor {

	/**
	 * Process Incoming
	 *
	 * @param TranslationData                        $data
	 * @param \Translationmanager\Module\Mlp\Adapter $adapter
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function process_incoming( TranslationData $data, Adapter $adapter );
}
