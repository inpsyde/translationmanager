<?php # -*- coding: utf-8 -*-
// phpcs:disable

namespace Translationmanager\Tests\Unit\Inc\Functions;

use Translationmanager\Tests\TestCase;

class Test extends TestCase {

	function testThatStringIsCorrectlySanitized() {

		\Brain\Monkey\Functions\when( '\\sanitize_html_class' )
			->returnArg( 1 );

		$response = \Translationmanager\Functions\sanitize_html_class( 'testing-class' );

		$this->assertSame( 'testing-class', $response );
	}

	function testThatMultipleClassesAsStringAreCorrectlySanitized() {

		\Brain\Monkey\Functions\when( '\\sanitize_html_class' )
			->returnArg( 1 );

		$response = \Translationmanager\Functions\sanitize_html_class( 'testing-class-one testing-class-two' );

		$this->assertSame( 'testing-class-one testing-class-two', $response );
	}

	function testThatArrayClassesMakeCorrectString() {

		\Brain\Monkey\Functions\when( '\\sanitize_html_class' )
			->returnArg( 1 );

		$response = \Translationmanager\Functions\sanitize_html_class( [
			'testing-class-one',
			'testing-class-two',
			'testing-class-three',
		] );

		$this->assertSame( 'testing-class-one testing-class-two testing-class-three', $response );
	}

	public function setUp() {

		parent::setUp();

		require_once getenv( 'LIBRARY_PATH' ) . '/inc/functions/commons.php';
	}
}
