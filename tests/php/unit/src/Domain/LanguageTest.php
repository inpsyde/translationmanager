<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Unit\Domain;

use Translationmanager\Domain\Language;
use TranslationmanagerTests\TestCase;

/**
 * Class LanguageTest
 *
 * @package TranslationmanagerTests\Unit\Domain
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
