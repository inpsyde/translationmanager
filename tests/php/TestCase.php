<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests;

use PHPUnit_Framework_TestCase;
use Brain\Monkey;

/**
 * Class TestCase
 * @package TranslationmanagerTests
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp()
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
}
