<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Functional\Module\Processor;

use Translationmanager\Module\Processor\ProcessorBus;
use Translationmanager\Module\Processor\ProcessorBusFactory as Testee;
use TranslationmanagerTests\TestCase;

/**
 * Class ProcessorBusFactoryTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class ProcessorBusFactoryTest extends TestCase
{
    /**
     * Test Instance Creation
     */
    public function testInstance()
    {
        $testee = new Testee();
        self::assertInstanceOf(Testee::class, $testee);
    }

    /**
     * Test ProcessorBus Instance is Created Correctly
     */
    public function testProcessorBusCreation()
    {
        $testee = new Testee();
        $processorBus = $testee->create();

        self::assertInstanceOf(ProcessorBus::class, $processorBus);
    }
}
