<?php # -*- coding: utf-8 -*-
// phpcs:disable

namespace Translationmanager\Tests\unit\src;

use Translationmanager\Request\Api\AddTranslation;
use Translationmanager\Tests\TestCase;

class AddTranslationTest extends TestCase {

	public function testHandleCreateANewProject() {

		$_POST['translationmanager_action_project_add_translation'] = true;

		$authMock           = \Mockery::mock( 'Translationmanager\\Auth\\Authable' );
		$nonceMock          = \Mockery::mock( 'Brain\\Nonces\\NonceInterface' );
		$projectUpdaterMock = \Mockery::mock( 'overload:\\Translationmanager\\ProjectUpdater' );
		$projectHandlerMock = \Mockery::mock( 'alias:Translationmanager\\ProjectHandler' );
		$noticeMock         = \Mockery::mock( 'alias:Translationmanager\\Notice\\TransientNoticeService' );

		$authMock->shouldReceive( 'can' )
		         ->andReturn( true );
		$authMock->shouldReceive( 'request_is_valid' )
		         ->andReturn( true );

		$projectUpdaterMock
			->shouldReceive( 'init' )
			->once();

		$projectHandlerMock
			->shouldReceive( 'create_project_using_date' )
			->once()
			->andReturn( 1 );
		$projectHandlerMock
			->shouldReceive( 'add_translation' )
			->once()
			->with( 1, 2, '1' );

		$noticeMock
			->shouldReceive( 'add_notice' )
			->once()
			->with( 'New Translation added successfully.', 'success' );

		\Brain\Monkey\Functions\expect( 'Translationmanager\\Functions\\filter_input' )
			->andReturnUsing( function () {

				return [
					'post_ID'                     => 2,
					'translationmanager_language' => [ '1' ],
				];
			} );

		\Brain\Monkey\Functions\expect( 'Translationmanager\\Functions\\redirect_admin_page_network' )
			->once()
			->with( \Mockery::type( 'string' ), [
				'page'                          => 'translationmanager-project',
				'translationmanager_project_id' => 1,
				'post_type'                     => 'project_item',
				'updated'                       => - 1,
			] );

		\Brain\Monkey\Functions\when( 'apply_filters' )
			->returnArg( 2 );
		\Brain\Monkey\Functions\when( 'wp_get_current_user' )
			->justReturn( \Mockery::mock( '\\WP_User' ) );
		\Brain\Monkey\Functions\when( 'update_user_meta' )
			->justReturn( true );
		\Brain\Monkey\Functions\when( 'get_current_user_id' )
			->justReturn( 1 );

		$translation = new AddTranslation( $authMock, $nonceMock, $projectHandlerMock );

		$translation->handle();

		$this->assertTrue( true );
	}

	public function testHandleInsertIntoAnExistingProject() {

		$_POST['translationmanager_action_project_add_translation'] = true;

		$authMock           = \Mockery::mock( 'Translationmanager\\Auth\\Authable' );
		$nonceMock          = \Mockery::mock( 'Brain\\Nonces\\NonceInterface' );
		$projectUpdaterMock = \Mockery::mock( 'overload:\\Translationmanager\\ProjectUpdater' );
		$projectHandlerMock = \Mockery::mock( 'Translationmanager\\ProjectHandler' );
		$noticeMock         = \Mockery::mock( 'alias:Translationmanager\\Notice\\TransientNoticeService' );

		$authMock
			->shouldReceive( 'can' )
			->andReturn( true );
		$authMock
			->shouldReceive( 'request_is_valid' )
			->andReturn( true );

		$projectUpdaterMock
			->shouldReceive( 'init' )
			->once();

		$projectHandlerMock
			->shouldReceive( 'add_translation' )
			->once()
			->with( 1, 2, '1' );

		$noticeMock
			->shouldReceive( 'add_notice' )
			->once()
			->with( 'New Translation added successfully.', 'success' );

		\Brain\Monkey\Functions\expect( 'Translationmanager\\Functions\\filter_input' )
			->andReturnUsing( function () {

				return [
					'translationmanager_project_id' => 1,
					'post_ID'                       => 2,
					'translationmanager_language'   => [ '1' ],
				];
			} );

		\Brain\Monkey\Functions\expect( 'Translationmanager\\Functions\\redirect_admin_page_network' )
			->once()
			->with( \Mockery::type( 'string' ), [
				'page'                          => 'translationmanager-project',
				'translationmanager_project_id' => 1,
				'post_type'                     => 'project_item',
				'updated'                       => - 1,
			] );

		\Brain\Monkey\Functions\when( 'apply_filters' )
			->returnArg( 2 );
		\Brain\Monkey\Functions\when( 'wp_get_current_user' )
			->justReturn( \Mockery::mock( '\\WP_User' ) );
		\Brain\Monkey\Functions\when( 'update_user_meta' )
			->justReturn( true );
		\Brain\Monkey\Functions\when( 'get_current_user_id' )
			->justReturn( 1 );

		$translation = new AddTranslation( $authMock, $nonceMock, $projectHandlerMock );

		$translation->handle();

		$this->assertTrue( true );
	}

	public function testThatEmptyDataDoesntDoAnything() {

		$_POST['translationmanager_action_project_add_translation'] = true;

		$authMock           = \Mockery::mock( 'Translationmanager\\Auth\\Authable' );
		$nonceMock          = \Mockery::mock( 'Brain\\Nonces\\NonceInterface' );
		$projectHandlerMock = \Mockery::mock( 'Translationmanager\\ProjectHandler' );

		$authMock
			->shouldReceive( 'can' )
			->andReturn( true );
		$authMock
			->shouldReceive( 'request_is_valid' )
			->andReturn( true );

		\Brain\Monkey\Functions\expect( 'Translationmanager\\Functions\\filter_input' )
			->andReturnUsing( '__return_empty_array' );

		\Brain\Monkey\Functions\when( 'wp_get_current_user' )
			->justReturn( \Mockery::mock( '\\WP_User' ) );
		\Brain\Monkey\Functions\when( 'update_user_meta' )
			->justReturn( true );
		\Brain\Monkey\Functions\when( 'get_current_user_id' )
			->justReturn( 1 );

		$translation = new AddTranslation( $authMock, $nonceMock, $projectHandlerMock );

		$response = $translation->handle();

		$this->assertSame( null, $response );
	}

	public function setUp() {

		parent::setUp();

		\Brain\Monkey\Functions\when( 'esc_html__' )
			->returnArg( 1 );
	}
}
