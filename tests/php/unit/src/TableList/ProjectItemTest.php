<?php # -*- coding: utf-8 -*-
// phpcs:disable

namespace Translationmanager\Tests\TableList;

use Brain\Monkey\Functions;
use Translationmanager\TableList\ProjectItem;
use \Translationmanager\Tests\TestCase;

class ProjectItemTest extends TestCase {

	public function testInstance() {

		\Mockery::mock( 'overload:\WP_List_Table' );

		$sut = new ProjectItem();

		$this->assertInstanceOf( 'Translationmanager\\TableList\\ProjectItem', $sut );
	}

	public function testProjectIDByRequestIsValid() {

		\Mockery::mock( 'overload:\Translationmanager\\TableList\\TableList', [
			'get_items_per_page' => 10,
		] );

		$term          = \Mockery::mock( 'WP_Term' );
		$term->term_id = 10;

		Functions\when( 'Translationmanager\\Functions\\filter_input' )
			->justReturn( [
				'translationmanager_project_id' => 10,
			] );

		Functions\expect( 'get_term' )
			->once()
			->with( 10, \Mockery::type( 'string' ) )
			->andReturn( $term );

		Functions\expect( 'Translationmanager\\Functions\\get_project_items' )
			->once()
			->with( 10, \Mockery::type( 'array' ) )
			->andReturn( [] );

		$sut         = new ProjectItem();
		$sut->screen = (object) [
			'id' => 'project_item',
		];

		$sut->items();

		$this->assertTrue( true );
	}

	/**
	 * @expectException \RuntimeException
	 */
	public function testProjectIDRequestThrowExceptionIfWpError() {

		\Mockery::mock( 'overload:\Translationmanager\\TableList\\TableList' );

		Functions\when( 'Translationmanager\\Functions\\filter_input' )
			->justReturn( [
				'translationmanager_project_id' => 10,
			] );

		Functions\expect( 'get_term' )
			->once()
			->with( 10, \Mockery::type( 'string' ) )
			->andReturn( \Mockery::mock( 'WP_Error' ) );

		$sut = new ProjectItem();

		$sut->items();

		$this->assertTrue( true );
	}

	public function testItemsReturnEmptyIfNoProjectIDCanBeRetrievedByRequest() {

		\Mockery::mock( 'overload:\Translationmanager\\TableList\\TableList' );

		Functions\when( 'Translationmanager\\Functions\\filter_input' )
			->justReturn( false );

		$sut = new ProjectItem();

		$response = $sut->items();

		$this->assertSame( [], $response );
	}
}
