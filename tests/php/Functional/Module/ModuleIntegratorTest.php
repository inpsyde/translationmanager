<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Tests\Functionals\Module;

use ArrayIterator;
use PHPUnit_Framework_MockObject_MockObject;
use Translationmanager\Module\ModuleIntegrator as Testee;
use Translationmanager\Module\ModulesProvider;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use Translationmanager\Plugin;
use TranslationmanagerTests\stubs\IntegratorStub;
use TranslationmanagerTests\TestCase;

/**
 * Class LoaderTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class ModuleIntegratorTest extends TestCase
{
    /**
     * Test Integrate
     */
    public function testIntegrate()
    {
        {
            $plugin = $this
                ->getMockBuilder(Plugin::class)
                ->disableOriginalConstructor()
                ->setMethods(['dir'])
                ->getMock();

            $modulesProvider = $this
                ->getMockBuilder(ModulesProvider::class)
                ->disableOriginalConstructor()
                ->setMethods(['getIterator'])
                ->getMock();

            $busProcessorFactory = $this
                ->getMockBuilder(ProcessorBusFactory::class)
                ->disableOriginalConstructor()
                ->getMock();

            $testee = $this->createTestee([$plugin, $modulesProvider, $busProcessorFactory]);
        }

        {
            $modulesProvider
                ->expects($this->once())
                ->method('getIterator')
                ->willReturn(new ArrayIterator([
                    'integratorstub/integratorstub.php' => IntegratorStub::class,
                ]));

            $plugin
                ->expects($this->exactly(1))
                ->method('dir')
                ->willReturn('/wp-content/plugins/plugin');
        }

        {
            $testee->integrate();
        }
    }

    /**
     * Create Testee Instance
     *
     * @param array $constructArgs
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function createTestee($constructArgs)
    {
        return $this
            ->getMockBuilder(Testee::class)
            ->setConstructorArgs($constructArgs)
            ->setMethodsExcept(['integrate'])
            ->getMock();
    }
}
