<?php

// -*- coding: utf-8 -*-

namespace Translationmanager\Module\Processor;

use SplDoublyLinkedList;
use SplQueue;
use Translationmanager\Translation;

/**
 * Class ProcessorBus
 * @package Translationmanager\Module\Processor
 */
class ProcessorBus
{
    const FILTER_DATA_PROCESSORS = 'translationmanager_data_processors';
    const FILTER_DATA_PROCESSOR_ENABLED = 'translationmanager_mlp_data_processor_enabled';

    /**
     * @var SplQueue
     */
    private $queue;

    /**
     * ProcessorBus constructor
     * @param SplQueue $queue
     */
    public function __construct(SplQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Add a Processor to the Bus
     *
     * @param Processor $processor
     *
     * @return ProcessorBus
     */
    public function pushProcessor(Processor $processor)
    {
        $this->queue->enqueue($processor);

        return $this;
    }

    /**
     * Process
     *
     * @param Translation $data
     */
    public function process(Translation $data)
    {
        $this->queue->setIteratorMode(SplDoublyLinkedList::IT_MODE_KEEP);

        $isIncoming = $data->is_incoming();
        $isOutComing = $data->is_outgoing();

        if (!$isIncoming && !$isOutComing) {
            return;
        }

        /**
         * Fires before processors runs.
         *
         * Use this hook to add processors by calling `push_processor()` on passed bus instance.
         *
         * @param ProcessorBus $processor_bus
         * @param Translation $data
         */
        do_action(self::FILTER_DATA_PROCESSORS, $this, $data);

        if (!$this->queue) {
            return;
        }

        foreach ($this->queue as $processor) {
            $target = $isIncoming ? IncomingProcessor::class : OutgoingProcessor::class;
            $method = $isIncoming ? 'processIncoming' : 'processOutgoing';

            $allowDataProcessor = apply_filters(
                self::FILTER_DATA_PROCESSOR_ENABLED,
                true,
                $processor,
                $data
            );

            if (is_a($processor, $target) & $allowDataProcessor) {
                do_action(
                    'translationmanager_log',
                    [
                        'message' => 'Processing with ' . get_class($processor) . '::' . $method . '()',
                        'context' => [
                            'Target site' => $data->target_site_id(),
                            'Source site' => $data->source_site_id(),
                            'Source post' => $data->source_post_id(),
                            'Target lang' => $data->target_language(),
                        ],
                    ]
                );

                /** @var callable $cb */
                $cb = [$processor, $method];
                $cb($data);
            }
        }
    }
}
