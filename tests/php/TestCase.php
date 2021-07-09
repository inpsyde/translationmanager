<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests;

use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Brain\Monkey;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class TestCase
 * @package TranslationmanagerTests
 */
class TestCase extends PhpUnitTestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Create Testee Instance To be Able to Tests Protected Methods
     *
     * @param $className
     * @param $constructArgs
     * @param array $methods
     * @return array
     * @throws ReflectionException
     */
    protected function createTesteeToTestProtectedMethods(
        $className,
        array $constructArgs,
        array $methods
    ) {

        $reflectionClass = new ReflectionClass($className);

        $testee = $constructArgs
            ? $reflectionClass->newInstanceArgs($constructArgs)
            : $reflectionClass->newInstanceWithoutConstructor();

        foreach ($methods as $method) {
            $methodReflection = new ReflectionMethod($className, $method);
            $methodReflection->setAccessible(true);
        }

        return [
            $testee,
            $methodReflection,
        ];
    }
}
