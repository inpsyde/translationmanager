<?php # -*- coding: utf-8 -*-
// phpcs:disable

namespace Translationmanager\Tests\Integration\Api;

use Translationmanager\Api;
use Translationmanager\Api\ProjectItem;
use Translationmanager\Tests\TestCase;

class ProjectItemTest extends TestCase {

	public function testThatCreateGoesWell() {

		\Brain\Monkey\Functions\when( 'apply_filters' )
			->returnArg( 2 );
		\Brain\Monkey\Functions\when( 'do_action' )
			->justReturn( true );

		\Brain\Monkey\Functions\expect( 'wp_remote_request' )
			->once()
			->andReturnUsing( function ( $url, $data ) {

				$mockedData = json_encode( [
					[
						'post_title'   => 'post_title',
						'post_content' => 'post_content',
						'post_excerpt' => '',
					],
				] );

				if ( 'https://sandbox.api.eurotext.de/api/v1/project/1/item.json' === $url
				     && $data['method'] === 'POST'
				     && empty( array_diff( $data['headers'], [
						'X-Source'        => 'en-us',
						'X-Target'        => 'fr-fr',
						'X-TextType'      => 'marketing',
						'X-System-Module' => 'post',
						'Content-Type'    => 'application/json',
						'public_key'      => 'b37270d25d5b3fccf137f7462774fe76',
						'apikey'          => 'mykey',
					] ) )
				     && $data['body'] === $mockedData
				) {
					return [
						'body'     => '{"id":1}',
						'response' => [
							'code'    => 200,
							'message' => 'OK',
						],
					];
				}

				return \Mockery::mock( 'WP_Error' );
			} );

		$api = new Api(
			'mykey',
			'b37270d25d5b3fccf137f7462774fe76',
			'https://sandbox.api.eurotext.de/api/v1'
		);

		$project = new ProjectItem( $api );

		$response = $project->create( 1, 'post', 'fr_FR', [
			[
				'post_title'   => 'post_title',
				'post_content' => 'post_content',
				'post_excerpt' => '',
			],
		] );

		$this->assertSame( 1, $response );
	}

	/**
	 * Here we test with expectation to `do_action`.
	 */
	public function testThatInvalidResponseCallLogAction() {

		\Brain\Monkey\Functions\when( 'apply_filters' )
			->returnArg( 2 );

		\Brain\Monkey\Functions\expect( 'do_action' )
			->once()
			->with( 'translationmanager_log', [
				'message' => 'Request against API failed.',
				'context' => [
					'headers' => [
						'X-Source'        => 'en-us',
						'X-Target'        => 'fr-fr',
						'X-TextType'      => 'marketing',
						'X-System-Module' => 'post',
					],
					'status'  => '',
					'body'    => '{"id":1}',
				],
			] );

		\Brain\Monkey\Functions\expect( 'wp_remote_request' )
			->once()
			->andReturnUsing( function ( $url, $data ) {

				$mockedData = json_encode( [
					[
						'post_title'   => 'post_title',
						'post_content' => 'post_content',
						'post_excerpt' => '',
					],
				] );

				if ( 'https://sandbox.api.eurotext.de/api/v1/project/1/item.json' === $url
				     && $data['method'] === 'POST'
				     && empty( array_diff( $data['headers'], [
						'X-Source'        => 'en-us',
						'X-Target'        => 'fr-fr',
						'X-TextType'      => 'marketing',
						'X-System-Module' => 'post',
						'Content-Type'    => 'application/json',
						'public_key'      => 'b37270d25d5b3fccf137f7462774fe76',
						'apikey'          => 'mykey',
					] ) )
				     && $data['body'] === $mockedData
				) {
					return [ 'body' => '{"id":1}' ];
				}
			} );

		$api = new Api(
			'mykey',
			'b37270d25d5b3fccf137f7462774fe76',
			'https://sandbox.api.eurotext.de/api/v1'
		);

		$project = new ProjectItem( $api );

		$response = $project->create( 1, 'post', 'fr_FR', [
			[
				'post_title'   => 'post_title',
				'post_content' => 'post_content',
				'post_excerpt' => '',
			],
		] );

		$this->assertTrue( true );
	}

	protected function setUp() {

		parent::setUp();

		require_once getenv( 'TESTS_PATH' ) . '/stubs/commonStubs.php';
		require_once getenv( 'TESTS_PATH' ) . '/stubs/wpRemoteStubs.php';

		\Brain\Monkey\Functions\when( 'esc_html__' )
			->returnArg( 1 );
		\Brain\Monkey\Functions\when( 'esc_html_x' )
			->returnArg( 1 );
		\Brain\Monkey\Functions\when( 'Translationmanager\Functions\current_lang_code' )
			->justReturn( 'en_US' );
	}
}
