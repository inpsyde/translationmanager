<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Unit;

use Brain\Monkey\Functions;
use TranslationmanagerTests\TestCase;
use Translationmanager\Translation;

/**
 * Class TranslationTest
 *
 * @package TranslationmanagerTests\Unit
 */
class TranslationTest extends TestCase
{
    /**
     * Test Incoming Constructor
     */
    public function testForIncomingConstructor()
    {

        /** @var \WP_Post $post */
        $post = \Mockery::mock('WP_Post');
        $post->ID = 123;
        $post->post_title = 'Title';
        $post->post_content = '<b>Content</b>';
        $post->post_excerpt = 'Excerpt';

        Functions\when('get_current_blog_id')->justReturn(1);
        Functions\expect('get_post')->once()->with(123)->andReturn($post);

        $incoming = Translation::for_incoming(
            [
                Translation::META_KEY => [
                    Translation::SOURCE_POST_ID_KEY => 123,
                    Translation::SOURCE_SITE_KEY => 1,
                    Translation::TARGET_SITE_KEY => 2,
                    Translation::TARGET_LANG_KEY => 'de',
                ],
            ]
        );

        static::assertTrue($incoming->is_incoming());
        static::assertFalse($incoming->is_outgoing());
        static::assertSame(1, $incoming->source_site_id());
        static::assertSame(123, $incoming->source_post_id());
        static::assertSame(2, $incoming->target_site_id());
        static::assertSame('de', $incoming->target_language());
        static::assertSame($post, $incoming->source_post());
    }

    /**
     * Test Outgoing Constructor
     */
    public function testForOutgoingConstructor()
    {

        /** @var \WP_Post $post */
        $post = \Mockery::mock('WP_Post');
        $post->ID = 123;
        $post->post_title = 'Title';
        $post->post_content = '<b>Content</b>';
        $post->post_excerpt = 'Excerpt';

        Functions\when('get_current_blog_id')
            ->justReturn(1);

        Functions\when('switch_to_blog')
            ->justReturn(false);

        Functions\when('get_post')
            ->justReturn($post);

        $outgoing = Translation::for_outgoing($post, 1, 2, 3, 'de', []);

        static::assertFalse($outgoing->is_incoming());
        static::assertTrue($outgoing->is_outgoing());
        static::assertSame(1, $outgoing->source_site_id());
        static::assertSame(123, $outgoing->source_post_id());
        static::assertSame(2, $outgoing->target_site_id());
        static::assertSame('de', $outgoing->target_language());
        static::assertSame($post, $outgoing->source_post());
    }

    /**
     * Test Has, Get and Set Value
     */
    public function testHasGetSetValue()
    {

        $incoming = Translation::for_incoming([]);

        $incoming->set_value('foo', 'foo');
        $incoming->set_value('bar', 'bar!!', '_ns');

        static::assertTrue($incoming->has_value('foo'));
        static::assertFalse($incoming->has_value('foo', '_ns'));
        static::assertTrue($incoming->has_value('bar', '_ns'));
        static::assertTrue($incoming->has_value('_ns'));
        static::assertFalse($incoming->has_value('bar'));

        static::assertSame('foo', $incoming->get_value('foo'));
        static::assertSame('bar!!', $incoming->get_value('bar', '_ns'));
        static::assertSame(['bar' => 'bar!!'], $incoming->get_value('_ns'));
        static::assertNull($incoming->get_value('foo', '_ns'));
        static::assertNull($incoming->get_value('bar'));
    }

    /**
     * Test Has, Get, Set Meta
     */
    public function testHasGetSetMeta()
    {

        $incoming = Translation::for_incoming([]);

        $incoming->set_meta('foo', 'foo');
        $incoming->set_meta('bar', 'bar!!', '_ns');

        static::assertTrue($incoming->has_meta('foo'));
        static::assertFalse($incoming->has_value('foo'));
        static::assertFalse($incoming->has_meta('foo', '_ns'));
        static::assertTrue($incoming->has_meta('bar', '_ns'));
        static::assertFalse($incoming->has_value('bar', '_ns'));
        static::assertTrue($incoming->has_meta('_ns'));
        static::assertFalse($incoming->has_value('_ns'));
        static::assertFalse($incoming->has_meta('bar'));

        static::assertSame('foo', $incoming->get_meta('foo'));
        static::assertSame('bar!!', $incoming->get_meta('bar', '_ns'));
        static::assertSame(['bar' => 'bar!!'], $incoming->get_meta('_ns'));
        static::assertNull($incoming->get_meta('foo', '_ns'));
        static::assertNull($incoming->get_meta('bar'));
    }

    /**
     * Test Remove Value
     */
    public function testRemoveValue()
    {

        $incoming = Translation::for_incoming([]);

        $incoming->set_value('foo', 'foo');
        $incoming->set_value('bar', 'bar!!', '_ns');
        $incoming->set_value('baz', 'baz!!', '_ns');

        static::assertTrue($incoming->has_value('foo'));
        static::assertTrue($incoming->has_value('bar', '_ns'));
        static::assertTrue($incoming->has_value('baz', '_ns'));

        $incoming->remove_value('foo');
        $incoming->remove_value('bar', '_ns');

        static::assertFalse($incoming->has_value('foo'));
        static::assertFalse($incoming->has_value('bar', '_ns'));
        static::assertTrue($incoming->has_value('baz', '_ns'));

        $incoming->remove_value('_ns');

        static::assertFalse($incoming->has_value('baz', '_ns'));
    }

    /**
     * Test Remove Meta
     */
    public function testRemoveMeta()
    {

        $incoming = Translation::for_incoming([]);

        $incoming->set_meta('foo', 'foo');
        $incoming->set_meta('bar', 'bar!!', '_ns');
        $incoming->set_meta('baz', 'baz!!', '_ns');

        static::assertTrue($incoming->has_meta('foo'));
        static::assertTrue($incoming->has_meta('bar', '_ns'));
        static::assertTrue($incoming->has_meta('baz', '_ns'));

        $incoming->remove_meta('foo');
        $incoming->remove_meta('bar', '_ns');

        static::assertFalse($incoming->has_meta('foo'));
        static::assertFalse($incoming->has_meta('bar', '_ns'));
        static::assertTrue($incoming->has_meta('baz', '_ns'));

        $incoming->remove_meta('_ns');

        static::assertFalse($incoming->has_meta('baz', '_ns'));
    }

    /**
     * Test to Array
     */
    public function testToArray()
    {

        $incoming = Translation::for_incoming(
            [
                'foo' => 'Foo',
                'bar' => 'Bar',
                Translation::META_KEY => [
                    'source_post_id' => 1,
                    'source_site_id' => 2,
                    'target_site_id' => 3,
                    'target_language' => 'en',
                    'b' => 'B',
                ],

            ]
        );

        Functions\expect('_doing_it_wrong')->twice();

        $incoming->set_value('foo', 'Foo!');
        $incoming->set_value('baz', 'Baz!!');
        $incoming->set_value('hello', 'Hello', 'greetings');
        $incoming->set_value('goodbye', 'Goodbye', 'greetings');
        $incoming->remove_value('bar');

        $incoming->set_meta('a', 'A!');
        $incoming->set_meta('c', 'C!!');
        $incoming->set_meta('yellow', 'Yellow', 'colors');
        $incoming->set_meta('green', 'Green', 'colors');
        $incoming->remove_meta('b');
        $incoming->remove_meta('source_post_id');   // _doing_it_wrong
        $incoming->set_meta('source_post_id', 456); // _doing_it_wrong

        $expected = [
            'foo' => 'Foo!',
            'baz' => 'Baz!!',
            'greetings' => [
                'hello' => 'Hello',
                'goodbye' => 'Goodbye',
            ],
            Translation::META_KEY => [
                'source_post_id' => 1,
                'source_site_id' => 2,
                'target_site_id' => 3,
                'target_language' => 'en',
                'a' => 'A!',
                'c' => 'C!!',
                'colors' => [
                    'yellow' => 'Yellow',
                    'green' => 'Green',
                ],
            ],

        ];

        static::assertEquals($expected, $incoming->to_array());
    }
}
