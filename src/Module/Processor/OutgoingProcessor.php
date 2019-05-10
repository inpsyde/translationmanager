<?php // -*- coding: utf-8 -*-

namespace Translationmanager\Module\Processor;

use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Translatable;

/**
 * Interface OutgoingProcessor
 * @package Translationmanager\Module\Processor
 */
interface OutgoingProcessor extends Processor
{
    /**
     * Process Outgoing
     *
     * @param Translatable $data
     * @param Adapter $adapter
     *
     * @return void
     */
    public function process_outgoing(Translatable $data, Adapter $adapter);
}
