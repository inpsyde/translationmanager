<?php // -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Processor;

use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\TranslationData;

interface OutgoingProcessor extends Processor
{

    /**
     * Process Outgoing
     *
     * @param TranslationData $data
     * @param Adapter $adapter
     *
     * @return void
     */
    public function process_outgoing(TranslationData $data, Adapter $adapter);
}
