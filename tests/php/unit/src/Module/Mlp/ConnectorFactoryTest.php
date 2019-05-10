<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Unit\Module\Mlp;

use Mockery;
use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Module\Mlp\Connector;
use Translationmanager\Module\Mlp\ConnectorFactory as Testee;
use Translationmanager\Module\Processor\Processor;
use Translationmanager\Module\Processor\ProcessorBus;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use TranslationmanagerTests\TestCase;

/**
 * Class ConnectorFactoryTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class ConnectorFactoryTest extends TestCase
{
    /**
     * Test Instance Creation
     */
    public function testInstance()
    {
        $processorBusFactory = Mockery::mock(ProcessorBusFactory::class);
        $testee = new Testee($processorBusFactory);

        self::assertInstanceOf(Testee::class, $testee);
    }

    /**
     * Test Create Connector
     */
    public function testCreateConnector()
    {
        {
            $processorBus = Mockery::mock(ProcessorBus::class);
            $adapter = Mockery::mock(Adapter::class);
            $processorBusFactory = Mockery::mock(ProcessorBusFactory::class);
            $testee = new Testee($processorBusFactory);
        }

        {
            $processorBusFactory
                ->shouldReceive('create')
                ->once()
                ->andReturn($processorBus);

            $processorBus
                ->shouldReceive('pushProcessor')
                ->atLeast()
                ->with(Mockery::type(Processor::class))
                ->andReturn($processorBus);
        }

        {
            $instance = $testee->create($adapter);
        }

        {
            self::assertInstanceOf(Connector::class, $instance);
        }
    }
}
