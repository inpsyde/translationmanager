<?php # -*- coding: utf-8 -*-
// phpcs:disable
namespace TranslationmanagerTests\Unit\Module\Processor;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Mockery;
use SplQueue;
use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Module\Processor\IncomingProcessor;
use Translationmanager\Module\Processor\OutgoingProcessor;
use Translationmanager\Translatable;
use TranslationmanagerTests\TestCase;
use Translationmanager\Translation;
use Translationmanager\Module\Processor\ProcessorBus as Testee;

/**
 * Class ProcessorBusTest
 * @package TranslationmanagerTests\Unit\Module\Processor
 */
class ProcessorBusTest extends TestCase
{
    /**
     * Test Process Actually Fires Hooks
     */
    public function testProcessFiresHooks()
    {

        $testee = new Testee(new SplQueue());

        $data = Translation::for_incoming([]);

        $adapter = Mockery::mock(Adapter::class);

        Filters\expectApplied(Testee::FILTER_DATA_PROCESSOR_ENABLED)
            ->once()
            ->with(true, Mockery::type(IncomingProcessor::class), $data)
            ->andReturnFirstArg();

        Actions\expectDone(Testee::FILTER_DATA_PROCESSORS)
            ->once()
            ->with(Mockery::type(Testee::class), $data)
            ->whenHappen(function (Testee $testee) use ($data, $adapter) {

                /** @var IncomingProcessor|\Mockery\MockInterface $processor */
                $processor = Mockery::mock(IncomingProcessor::class);
                $processor
                    ->shouldReceive('process_incoming')
                    ->once()
                    ->with($data, $adapter)
                    ->andReturnUsing(function () {

                        echo 'Process happened!';
                    });

                $testee->pushProcessor($processor);
            });

        $this->expectOutputString('Process happened!');

        $testee->process($data, $adapter);
    }

    /**
     * Test Process Process Incoming Does not Execute Outgoing Processors
     */
    public function testProcessIncomingDataDoesNotExecuteOutgoingProcessors()
    {

        /** @var OutgoingProcessor|\Mockery\MockInterface $processor */
        $processor = Mockery::mock(OutgoingProcessor::class);
        $processor->shouldReceive('process_outgoing')->never();

        $testee = new Testee(new SplQueue());
        $testee->pushProcessor($processor);

        $data = Translation::for_incoming([]);

        $adapter = Mockery::mock(Adapter::class);

        Actions\expectDone(Testee::FILTER_DATA_PROCESSORS)
            ->once()
            ->with(Mockery::type(Testee::class), $data);

        $testee->process($data, $adapter);
    }

    /**
     * Test Process Can be Skipped Via Filter
     */
    public function testProcessorCanBeSkippedViaFilter()
    {

        /** @var OutgoingProcessor|\Mockery\MockInterface $processor_a */
        $processor_a = Mockery::mock(IncomingProcessor::class);
        $processor_a->shouldReceive('process_incoming')->never();

        /** @var OutgoingProcessor|\Mockery\MockInterface $processor_b */
        $processor_b = Mockery::mock(IncomingProcessor::class);
        $processor_b->shouldReceive('process_incoming')->once()->andReturnUsing(function () {

            echo 'Processor B executed!';
        });

        $testee = new Testee(new SplQueue());
        $testee->pushProcessor($processor_a)->pushProcessor($processor_b);

        $data = Translation::for_incoming([]);

        $adapter = Mockery::mock(Adapter::class);

        Filters\expectApplied(Testee::FILTER_DATA_PROCESSOR_ENABLED)
            ->twice()
            ->andReturnUsing(function ($true, $processor) use ($processor_a) {

                return $processor_a === $processor ? false : $true;
            });

        $this->expectOutputString('Processor B executed!');

        $testee->process($data, $adapter);
    }

    /**
     * Test No Processor is Executed Because no Processors in Queue
     */
    public function testNoProcessorIsExecutedBecauseNoProcessorInQueue()
    {
        {
            $translationData = Mockery::mock(Translatable::class);
            $adapter = Mockery::mock(Adapter::class);
            $splQueue = Mockery::spy(SplQueue::class);
            $testee = new Testee(new SplQueue());
        }

        {
            $translationData
                ->shouldReceive('is_incoming')
                ->andReturn(true);

            $translationData
                ->shouldReceive('is_outgoing')
                ->andReturn(false);
        }

        {
            $testee->process($translationData, $adapter);
        }

        {
            $splQueue->shouldNotHaveReceived('count');
        }
    }
}
