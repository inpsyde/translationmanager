<?php # -*- coding: utf-8 -*-

/*
 * This file is part of the Translation Manager package.
 *
 * (c) Guido Scialfa <dev@guidoscialfa.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Translationmanager\Tests\unit\inc\functions;

use function Brain\Monkey\Filters\expectApplied;
use function Brain\Monkey\Functions\when;
use ReflectionProperty;
use Translationmanager\Api;
use function Translationmanager\Functions\translationmanager_api;
use Translationmanager\Tests\TestCase;

/**
 * Class ApiTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class ApiTest extends TestCase
{
    /**
     * Test Api Url Change if Constant has Value
     */
    public function testApiUrlByConstant()
    {
        /*
         * Setup
         *
         * Define the constant
         */
        define('TRANSLATION_MANAGER_API_URL', 'testee_url');

        /*
         * Set Stubs
         */
        when('get_option')->justReturn('API_KEY');

        /**
         * Execute the Testee Function
         */
        $api = translationmanager_api();

        /*
         * Expectation
         *
         * Since is not possible to set expectation over the constructor,
         * let's set accessible the internal property that store the url value
         * and assert it contains the expected value
         */
        $propertyReflection = new ReflectionProperty(Api::class, 'base_url');
        $propertyReflection->setAccessible(true);

        self::assertEquals('testee_url', $propertyReflection->getValue($api));
    }

    /**
     * Test Api Url Filter is Applied
     */
    public function testApiUrlFilterApplied()
    {
        $expectedUrl = 'http://api.eurotext.de/api/v1';

        /*
         * Set Stubs
         */
        when('get_option')->justReturn('API_KEY');

        /*
         * Expectation
         */
        expectApplied('translationmanager_api_url')
            ->once()
            ->with($expectedUrl)
            ->andReturnFirstArg();

        /*
         * Execute the Testee Function
         */
        $api = translationmanager_api();

        /*
         * Expectation
         *
         * Since is not possible to set expectation over the constructor,
         * let's set accessible the internal property that store the url value
         * and assert it contains the expected value
         */
        $propertyReflection = new ReflectionProperty(Api::class, 'base_url');
        $propertyReflection->setAccessible(true);

        self::assertEquals($expectedUrl, $propertyReflection->getValue($api));
    }

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass()
    {
        require_once getenv('LIBRARY_PATH') . '/inc/functions/api.php';

        parent::setUpBeforeClass();
    }
}
