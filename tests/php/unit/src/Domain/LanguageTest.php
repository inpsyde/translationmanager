<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Tests\Unit\Domain;

use Translationmanager\Domain\Language;
use Translationmanager\Tests\TestCase;

/**
 * Class LanguageTest
 *
 * @package Translationmanager\Tests\Unit\Domain
 */
class LanguageTest extends TestCase
{

    /**
     * Test Instance Creation
     */
    public function testInstance()
    {

        $testee = new Language('en_US', 'English');

        $this->assertInstanceOf(Language::class, $testee);
    }

    /**
     * Test Language get Label
     */
    public function testGetLabel()
    {

        $testee = new Language('en_US', 'English');

        $this->assertSame('en_US', $testee->get_lang_code());
    }

    /**
     * Test Language get Code
     */
    public function testGetLangCode()
    {

        $testee = new Language('en_US', 'English');

        $this->assertSame('English', $testee->get_label());
    }
}
