<?php # -*- coding: utf-8 -*-
// phpcs:disable
namespace Translationmanager\Tests\Unit\Module\Mlp;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Module\Mlp\Processor\IncomingProcessor;
use Translationmanager\Module\Mlp\Processor\OutgoingProcessor;
use Translationmanager\Tests\TestCase;
use Translationmanager\TranslationData;
use Translationmanager\Module\Mlp\ProcessorBus;

class ProcessorBusTest extends TestCase
{

    /**
     * Test Process Actually Fires Hooks
     */
    public function testProcessFiresHooks()
    {

        $bus = new ProcessorBus();

        $data = TranslationData::for_incoming([]);

        $adapter = \Mockery::mock(Adapter::class);

        Filters\expectApplied('translationmanager_mlp_data_processor_enabled')
            ->once()
            ->with(true, \Mockery::type(IncomingProcessor::class), $data)
            ->andReturnFirstArg();

        Actions\expectDone('translationmanager_mlp_data_processors')
            ->once()
            ->with(\Mockery::type(ProcessorBus::class), $data)
            ->whenHappen(function (ProcessorBus $bus) use ($data, $adapter) {

                /** @var IncomingProcessor|\Mockery\MockInterface $processor */
                $processor = \Mockery::mock(IncomingProcessor::class);
                $processor
                    ->shouldReceive('process_incoming')
                    ->once()
                    ->with($data, $adapter)
                    ->andReturnUsing(function () {

                        echo 'Process happened!';
                    });

                $bus->push_processor($processor);
            });

        $this->expectOutputString('Process happened!');

        $bus->process($data, $adapter);
    }

    /**
     * Test Process Process Incoming Does not Execute Outgoing Processors
     */
    public function testProcessIncomingDataDoesNotExecuteOutgoingProcessors()
    {

        /** @var OutgoingProcessor|\Mockery\MockInterface $processor */
        $processor = \Mockery::mock(OutgoingProcessor::class);
        $processor->shouldReceive('process_outgoing')->never();

        $bus = new ProcessorBus();
        $bus->push_processor($processor);

        $data = TranslationData::for_incoming([]);

        $adapter = \Mockery::mock(Adapter::class);

        Filters\expectApplied('translationmanager_mlp_data_processor_enabled')->never();

        Actions\expectDone('translationmanager_mlp_data_processors')
            ->once()
            ->with(\Mockery::type(ProcessorBus::class), $data);

        $bus->process($data, $adapter);
    }

    /**
     * Test Process Can be Skipped Via Filter
     */
    public function testProcessorCanBeSkippedViaFilter()
    {

        /** @var OutgoingProcessor|\Mockery\MockInterface $processor_a */
        $processor_a = \Mockery::mock(IncomingProcessor::class);
        $processor_a->shouldReceive('process_incoming')->never();

        /** @var OutgoingProcessor|\Mockery\MockInterface $processor_b */
        $processor_b = \Mockery::mock(IncomingProcessor::class);
        $processor_b->shouldReceive('process_incoming')->once()->andReturnUsing(function () {

            echo 'Processor B executed!';
        });

        $bus = new ProcessorBus();
        $bus->push_processor($processor_a)->push_processor($processor_b);

        $data = TranslationData::for_incoming([]);

        $adapter = \Mockery::mock(Adapter::class);

        Filters\expectApplied('translationmanager_mlp_data_processor_enabled')
            ->twice()
            ->andReturnUsing(function ($true, $processor) use ($processor_a) {

                return $processor_a === $processor ? false : $true;
            });

        $this->expectOutputString('Processor B executed!');

        $bus->process($data, $adapter);
    }
}
