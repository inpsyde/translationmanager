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
            $modulesProvider = $this
                ->getMockBuilder(ModulesProvider::class)
                ->disableOriginalConstructor()
                ->setMethods(['getIterator'])
                ->getMock();

            $testee = $this->createTestee([$modulesProvider]);
        }

        {
            $modulesProvider
                ->expects($this->once())
                ->method('getIterator')
                ->willReturn(new ArrayIterator([
                    'integratorstub/integratorstub.php' => IntegratorStub::class,
                ]));
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
