<?php

// -*- coding: utf-8 -*-

namespace Translationmanager\Module\Processor;

use Translationmanager\Translation;

/**
 * Interface OutgoingProcessor
 * @package Translationmanager\Module\Processor
 */
interface OutgoingProcessor extends Processor
{
    /**
     * Process Outgoing
     *
     * @param Translation $translation
     *
     * @return void
     */
    public function processOutgoing(Translation $translation);
}
