<?php // -*- coding: utf-8 -*-

namespace Translationmanager\Module\Processor;

use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Translatable;

interface IncomingProcessor extends Processor
{
    /**
     * Process Incoming
     *
     * @param Translatable $data
     * @param Adapter $adapter
     *
     * @return void
     * @since 1.0.0
     */
    public function process_incoming(Translatable $data, Adapter $adapter);
}
