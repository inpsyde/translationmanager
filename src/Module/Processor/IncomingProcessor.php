<?php

// -*- coding: utf-8 -*-

namespace Translationmanager\Module\Processor;

use Translationmanager\Translation;

/**
 * Interface IncomingProcessor
 * @package Translationmanager\Module\Processor
 */
interface IncomingProcessor extends Processor
{
    /**
     * Process Incoming
     *
     * @param Translation $translation
     *
     * @return void
     */
    public function processIncoming(Translation $translation);
}
