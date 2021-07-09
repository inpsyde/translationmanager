<?php # -*- coding: utf-8 -*-
// phpcs:disable

namespace TranslationmanagerTests\Unit\Inc\Functions;

use \Brain\Monkey\Functions;
use TranslationmanagerTests\TestCase;

/**
 * Class FunctionsTest
 *
 * @package TranslationmanagerTests\Unit\Inc\Functions
 */
class SanitizeStringTest extends TestCase
{

    /**
     * Test Sanitization of String
     */
    function testThatStringIsCorrectlySanitized()
    {

        Functions\when('\\sanitize_html_class')
            ->returnArg(1);

        $response = \Translationmanager\Functions\sanitize_html_class('testing-class');

        $this->assertSame('testing-class', $response);
    }

    /**
     * Test Multiple Classes Space Separated are Correctly Sanitized
     */
    function testThatMultipleClassesAsStringAreCorrectlySanitized()
    {

        Functions\when('\\sanitize_html_class')
            ->returnArg(1);

        $response = \Translationmanager\Functions\sanitize_html_class('testing-class-one testing-class-two');

        $this->assertSame('testing-class-one testing-class-two', $response);
    }

    /**
     * Test an Array of Classes are Correctly Sanitized
     */
    function testThatArrayClassesMakeCorrectString()
    {

        Functions\when('\\sanitize_html_class')
            ->returnArg(1);

        $response = \Translationmanager\Functions\sanitize_html_class([
            'testing-class-one',
            'testing-class-two',
            'testing-class-three',
        ]);

        $this->assertSame('testing-class-one testing-class-two testing-class-three', $response);
    }

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {

        parent::setUp();

        require_once getenv('LIBRARY_PATH') . '/inc/functions/commons.php';
    }
}
