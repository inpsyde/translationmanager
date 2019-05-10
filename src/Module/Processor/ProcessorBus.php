<?php // -*- coding: utf-8 -*-

namespace Translationmanager\Module\Processor;

use SplQueue;
use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Translatable;

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
     * @param Translatable $data
     * @param Adapter $adapter
     */
    public function process(Translatable $data, Adapter $adapter)
    {
        $is_incoming = $data->is_incoming();

        if (!$is_incoming && !$data->is_outgoing()) {
            return;
        }

        /**
         * Fires before processors runs.
         *
         * Use this hook to add processors by calling `push_processor()` on passed bus instance.
         *
         * @param ProcessorBus $processor_bus
         * @param Translatable $data
         */
        do_action(self::FILTER_DATA_PROCESSORS, $this, $data);

        if (!$this->queue) {
            return;
        }

        while ($this->queue->count()) {
            /** @var IncomingProcessor|OutgoingProcessor $processor */
            $processor = $this->queue->dequeue();

            $target = $is_incoming ? IncomingProcessor::class : OutgoingProcessor::class;
            $method = $is_incoming ? 'process_incoming' : 'process_outgoing';

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
                $cb($data, $adapter);
            }
        }
    }
}
