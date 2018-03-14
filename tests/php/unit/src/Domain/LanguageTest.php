<?php # -*- coding: utf-8 -*-
// phpcs:disable
namespace Translationmanager\Tests\Unit\Domain;

use Translationmanager\Domain\Language;
use Translationmanager\Tests\TestCase;

class LanguageTest extends TestCase {

	public function testInstance() {

		$testee = new Language( 'en_US', 'English' );

		$this->assertInstanceOf( 'Translationmanager\\Domain\\Language', $testee );
	}

	public function testGetLabel() {

		$testee = new Language( 'en_US', 'English' );

		$this->assertSame( 'en_US', $testee->get_lang_code() );
	}

	public function testGetLangCode() {

		$testee = new Language( 'en_US', 'English' );

		$this->assertSame( 'English', $testee->get_label() );
	}
}
