<?php

# -*- coding: utf-8 -*-

namespace Translationmanager\Module\Processor;

use SplQueue;

/**
 * Class ProcessorBusFactory
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class ProcessorBusFactory
{
    /**
     * Create Instance of a ProcessorBus
     *
     * @return ProcessorBus
     */
    public function create()
    {
        return new ProcessorBus(new SplQueue());
    }
}
