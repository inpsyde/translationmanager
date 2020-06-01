<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Tests\Unit\Assert;

use InvalidArgumentException;
use Translationmanager\Utils\Assert;
use TranslationmanagerTests\TestCase;

/**
 * Class AssertTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class AssertTest extends TestCase
{
    /**
     * Test Semantic Version
     *
     * @dataProvider dataProviderForSemanticVersion
     */
    public function testSemanticVersion($version, $expectedException)
    {
        $expectedExceptionMessage = 'Exception Message';

        if ($expectedException) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        Assert::semVersion($version, $expectedExceptionMessage);
    }

    /**
     * @return array
     */
    public function dataProviderForSemanticVersion()
    {
        return [
            ['4.0.0', false],
            ['4.1.0', false],
            ['4.1.12', false],
            ['4.1.12-25', false],
            ['4.1.0-dev', false],
            ['4.0.0-dev', false],
            ['4.0.0+meta.23', false],
            ['4.2.0-dev.2+meta.23', false],
            ['4.1.12-25.dev', false],
            ['1.0.0-xyz.32+a.b', false],
            ['1.2.3-4.5.a.b', false],
            ['1.2.3.4.5-a-12+b+c+d', false],
            ['1.0.0-xyz.32', false],
            ['4-alpha-40306', false],
            ['4.8-alpha-40306', false],
            ['4.8.9-alpha-40306', false],
            ['5.3.6-13ubuntu3.2', false],
            ['1-1', false],
            ['1!x!y!z!.!3!2!+!a!.!b!', false],
            ['1me_meh?', false],
            ['meh', true],
            ['-meh', true],
            ['-1', true],
        ];
    }
}
