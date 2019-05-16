<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Tests\Unit\Module;

use ArrayIterator;
use Brain\Monkey\Functions;
use Brain\Monkey\Filters;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Translationmanager\Module\ModulesProvider as Testee;
use TranslationmanagerTests\stubs\IntegratorStub;
use TranslationmanagerTests\TestCase;

/**
 * Class ModulesProviderTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class ModulesProviderTest extends TestCase
{
    /**
     * Test Plugins Retrieve Functionality
     */
    public function testPluginsListRetrieve()
    {
        {
            list($testee, $methodReflection) = $this->createTesteeToTestProtectedMethods(
                Testee::class,
                [],
                ['plugins']
            );
        }

        {
            Functions\expect('get_option')
                ->once()
                ->with('active_plugins', [])
                ->andReturn([
                    'integrationstub/integrationstub.php',
                ]);

            Functions\expect('wp_get_active_network_plugins')
                ->once()
                ->withNoArgs()
                ->andReturn([]);

            Functions\expect('function_exists')
                ->once()
                ->with('wp_get_active_network_plugins')
                ->andReturnTrue();
        }

        {
            $response = $methodReflection->invoke($testee);
        }

        {
            self::assertSame(
                ['integrationstub' => 'integrationstub/integrationstub.php'],
                $response
            );
        }
    }

    /**
     * Test Modules that are within the Active Plugins List are Available
     */
    public function testAvailableModules()
    {
        {
            list($testee, $methodReflection) = $this->createTesteeToTestProtectedMethods(
                Testee::class,
                [
                    [
                        'integrationstub' => new IntegratorStub(),
                    ],
                ],
                ['allowedModules']
            );
        }

        {
            $response = $methodReflection->invoke(
                $testee,
                [
                    'integrationstub' => 'integrationstub/integrationstub.php',
                    'pluginthatnotexists' => 'pluginthatnotexists/pluginthatnotexists.php',
                ]
            );
        }

        {
            self::assertSame(['integrationstub'], $response);
        }
    }

    /**
     * Test Get Iterator
     */
    public function testGetIterator()
    {
        {
            $stubIntegrator = new IntegratorStub();
            $pluginsStubList = ['integrationstub' => 'integrationstub/integrationstub.php'];
            $allowedModulesStub = ['integrationstub',];
            $modulesStub = ['integrationstub' => $stubIntegrator];
            $availableModulesStub = ['integrationstub/integrationstub.php' => $stubIntegrator];

            $testee = $this->createTestee([$modulesStub], ['plugins', 'allowedModules']);
        }

        {
            // Get the modules registered plus the third party ones.
            Filters\expectApplied(Testee::FILTER_AVAILABLE_MODULES)
                ->once()
                ->with($modulesStub)
                ->andReturn($modulesStub);

            // Extract active plugins list
            $testee
                ->expects($this->once())
                ->method('plugins')
                ->willReturn($pluginsStubList);

            // Retrieve the list of the allowed modules
            // based on the modules list and the active plugins.
            $testee
                ->expects($this->once())
                ->method('allowedModules')
                ->with($pluginsStubList)
                ->willReturn($allowedModulesStub);
        }

        {
            /** @var ArrayIterator $response */
            $response = $testee->getIterator();
            /** @var ArrayIterator $response2 */
            $response2 = $testee->getIterator();
        }

        {
            self::assertInstanceOf(ArrayIterator::class, $response);
            self::assertInstanceOf(ArrayIterator::class, $response2);

            self::assertSame($availableModulesStub, $response->getArrayCopy());
            self::assertSame($availableModulesStub, $response2->getArrayCopy());
        }
    }

    /**
     * @param array $constructorArgs
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function createTestee(array $constructorArgs, array $methods)
    {
        $mock = $this->getMockBuilder(Testee::class);

        $constructorArgs
            ? $mock->setConstructorArgs($constructorArgs)
            : $mock->disableOriginalConstructor();

        $methods
            ? $mock->setMethods($methods)
            : $mock->setMethodsExcept(['getIterator']);

        return $mock->getMock();
    }
}
