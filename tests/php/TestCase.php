<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests;

use PHPUnit_Framework_TestCase;
use Brain\Monkey;

class TestCase extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {

        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown()
    {

        Monkey\tearDown();
        parent::tearDown();
    }
}
