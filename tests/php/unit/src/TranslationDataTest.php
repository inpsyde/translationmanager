<?php # -*- coding: utf-8 -*-
// phpcs:disable
namespace Translationmanager\Tests\Unit;

use Brain\Monkey\Functions;
use Translationmanager\Tests\TestCase;
use Translationmanager\TranslationData;

class TranslationDataTest extends TestCase {

	public function testForIncomingConstructor() {

		/** @var \WP_Post $post */
		$post               = \Mockery::mock( 'WP_Post' );
		$post->ID           = 123;
		$post->post_title   = 'Title';
		$post->post_content = '<b>Content</b>';
		$post->post_excerpt = 'Excerpt';

		Functions\when( 'get_current_blog_id' )->justReturn( 1 );
		Functions\expect( 'get_post' )->once()->with( 123 )->andReturn( $post );

		$incoming = TranslationData::for_incoming(
			[
				TranslationData::META_KEY => [
					TranslationData::SOURCE_POST_ID_KEY => 123,
					TranslationData::SOURCE_SITE_KEY    => 1,
					TranslationData::TARGET_SITE_KEY    => 2,
					TranslationData::TARGET_LANG_KEY    => 'de',
				],
			]
		);

		static::assertTrue( $incoming->is_incoming() );
		static::assertFalse( $incoming->is_outgoing() );
		static::assertSame( 1, $incoming->source_site_id() );
		static::assertSame( 123, $incoming->source_post_id() );
		static::assertSame( 2, $incoming->target_site_id() );
		static::assertSame( 'de', $incoming->target_language() );
		static::assertSame( $post, $incoming->source_post() );
	}

	public function testForOutgoingConstructor() {

		$this->markTestSkipped( 'NEED TO CLARIFY WHY THE get_current_blog_id() isn\'t mocked' );

		/** @var \WP_Post $post */
		$post               = \Mockery::mock( 'WP_Post' );
		$post->ID           = 123;
		$post->post_title   = 'Title';
		$post->post_content = '<b>Content</b>';
		$post->post_excerpt = 'Excerpt';

		$outgoing = TranslationData::for_outgoing( $post, 1, 2, 'de', [] );

		static::assertFalse( $outgoing->is_incoming() );
		static::assertTrue( $outgoing->is_outgoing() );
		static::assertSame( 1, $outgoing->source_site_id() );
		static::assertSame( 123, $outgoing->source_post_id() );
		static::assertSame( 2, $outgoing->target_site_id() );
		static::assertSame( 'de', $outgoing->target_language() );
		static::assertSame( $post, $outgoing->source_post() );
	}

	public function testHasGetSetValue() {

		$incoming = TranslationData::for_incoming( [] );

		$incoming->set_value( 'foo', 'foo' );
		$incoming->set_value( 'bar', 'bar!!', '_ns' );

		static::assertTrue( $incoming->has_value( 'foo' ) );
		static::assertFalse( $incoming->has_value( 'foo', '_ns' ) );
		static::assertTrue( $incoming->has_value( 'bar', '_ns' ) );
		static::assertTrue( $incoming->has_value( '_ns' ) );
		static::assertFalse( $incoming->has_value( 'bar' ) );

		static::assertSame( 'foo', $incoming->get_value( 'foo' ) );
		static::assertSame( 'bar!!', $incoming->get_value( 'bar', '_ns' ) );
		static::assertSame( [ 'bar' => 'bar!!' ], $incoming->get_value( '_ns' ) );
		static::assertNull( $incoming->get_value( 'foo', '_ns' ) );
		static::assertNull( $incoming->get_value( 'bar' ) );
	}

	public function testHasGetSetMeta() {

		$incoming = TranslationData::for_incoming( [] );

		$incoming->set_meta( 'foo', 'foo' );
		$incoming->set_meta( 'bar', 'bar!!', '_ns' );

		static::assertTrue( $incoming->has_meta( 'foo' ) );
		static::assertFalse( $incoming->has_value( 'foo' ) );
		static::assertFalse( $incoming->has_meta( 'foo', '_ns' ) );
		static::assertTrue( $incoming->has_meta( 'bar', '_ns' ) );
		static::assertFalse( $incoming->has_value( 'bar', '_ns' ) );
		static::assertTrue( $incoming->has_meta( '_ns' ) );
		static::assertFalse( $incoming->has_value( '_ns' ) );
		static::assertFalse( $incoming->has_meta( 'bar' ) );

		static::assertSame( 'foo', $incoming->get_meta( 'foo' ) );
		static::assertSame( 'bar!!', $incoming->get_meta( 'bar', '_ns' ) );
		static::assertSame( [ 'bar' => 'bar!!' ], $incoming->get_meta( '_ns' ) );
		static::assertNull( $incoming->get_meta( 'foo', '_ns' ) );
		static::assertNull( $incoming->get_meta( 'bar' ) );
	}

	public function testRemoveValue() {

		$incoming = TranslationData::for_incoming( [] );

		$incoming->set_value( 'foo', 'foo' );
		$incoming->set_value( 'bar', 'bar!!', '_ns' );
		$incoming->set_value( 'baz', 'baz!!', '_ns' );

		static::assertTrue( $incoming->has_value( 'foo' ) );
		static::assertTrue( $incoming->has_value( 'bar', '_ns' ) );
		static::assertTrue( $incoming->has_value( 'baz', '_ns' ) );

		$incoming->remove_value( 'foo' );
		$incoming->remove_value( 'bar', '_ns' );

		static::assertFalse( $incoming->has_value( 'foo' ) );
		static::assertFalse( $incoming->has_value( 'bar', '_ns' ) );
		static::assertTrue( $incoming->has_value( 'baz', '_ns' ) );

		$incoming->remove_value( '_ns' );

		static::assertFalse( $incoming->has_value( 'baz', '_ns' ) );
	}

	public function testRemoveMeta() {

		$incoming = TranslationData::for_incoming( [] );

		$incoming->set_meta( 'foo', 'foo' );
		$incoming->set_meta( 'bar', 'bar!!', '_ns' );
		$incoming->set_meta( 'baz', 'baz!!', '_ns' );

		static::assertTrue( $incoming->has_meta( 'foo' ) );
		static::assertTrue( $incoming->has_meta( 'bar', '_ns' ) );
		static::assertTrue( $incoming->has_meta( 'baz', '_ns' ) );

		$incoming->remove_meta( 'foo' );
		$incoming->remove_meta( 'bar', '_ns' );

		static::assertFalse( $incoming->has_meta( 'foo' ) );
		static::assertFalse( $incoming->has_meta( 'bar', '_ns' ) );
		static::assertTrue( $incoming->has_meta( 'baz', '_ns' ) );

		$incoming->remove_meta( '_ns' );

		static::assertFalse( $incoming->has_meta( 'baz', '_ns' ) );
	}

	public function testToArray() {

		$incoming = TranslationData::for_incoming(
			[
				'foo'                     => 'Foo',
				'bar'                     => 'Bar',
				TranslationData::META_KEY => [
					'source_post_id'  => 1,
					'source_site_id'  => 2,
					'target_site_id'  => 3,
					'target_language' => 'en',
					'b'               => 'B',
				],

			]
		);

		Functions\expect( '_doing_it_wrong' )->twice();

		$incoming->set_value( 'foo', 'Foo!' );
		$incoming->set_value( 'baz', 'Baz!!' );
		$incoming->set_value( 'hello', 'Hello', 'greetings' );
		$incoming->set_value( 'goodbye', 'Goodbye', 'greetings' );
		$incoming->remove_value( 'bar' );

		$incoming->set_meta( 'a', 'A!' );
		$incoming->set_meta( 'c', 'C!!' );
		$incoming->set_meta( 'yellow', 'Yellow', 'colors' );
		$incoming->set_meta( 'green', 'Green', 'colors' );
		$incoming->remove_meta( 'b' );
		$incoming->remove_meta( 'source_post_id' );   // _doing_it_wrong
		$incoming->set_meta( 'source_post_id', 456 ); // _doing_it_wrong

		$expected = [
			'foo'                     => 'Foo!',
			'baz'                     => 'Baz!!',
			'greetings'               => [
				'hello'   => 'Hello',
				'goodbye' => 'Goodbye',
			],
			TranslationData::META_KEY => [
				'source_post_id'  => 1,
				'source_site_id'  => 2,
				'target_site_id'  => 3,
				'target_language' => 'en',
				'a'               => 'A!',
				'c'               => 'C!!',
				'colors'          => [
					'yellow' => 'Yellow',
					'green'  => 'Green',
				],
			],

		];

		static::assertEquals( $expected, $incoming->to_array() );

	}
}