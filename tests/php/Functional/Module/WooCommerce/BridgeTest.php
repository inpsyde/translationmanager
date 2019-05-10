<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Functional\Module\WooCommerce;

use Translationmanager\Module\Processor\ProcessorBus;
use Translationmanager\Module\WooCommerce\Bridge as Testee;
use TranslationmanagerTests\TestCase;

/**
 * Class BridgeTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class BridgeTest extends TestCase
{
    /**
     * Test Instance Creation
     */
    public function testInstance()
    {
        $processorBus = $this->createMock(ProcessorBus::class);
        $testee = new Testee($processorBus);

        self::assertInstanceOf(Testee::class, $testee);
    }
}
