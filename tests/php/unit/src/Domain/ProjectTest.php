<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Tests\Unit\Domain;

use Translationmanager\Domain\Project;
use Translationmanager\Tests\TestCase;

/**
 * Class ProjectTest
 *
 * @package Translationmanager\Tests\Unit\Domain
 */
class ProjectTest extends TestCase
{

    /**
     * Test Instance Creation
     */
    public function testInstance()
    {

        $testee = new Project(
            'WordPress',
            '1.0.0',
            'translationmanager',
            '1.0.0',
            'Project Term Name'
        );

        $this->assertInstanceOf('TranslationManager\\Domain\\Project', $testee);
    }

    /**
     * Test To Header Data
     */
    public function testToHeader()
    {

        $testee = new Project(
            'WordPress',
            '1.0.0',
            'translationmanager',
            '1.0.0',
            'Project Term Name'
        );

        $expected = [
            'X-System' => 'WordPress',
            'X-System-Version' => '1.0.0',
            'X-Plugin' => 'translationmanager',
            'X-Plugin-Version' => '1.0.0',
            'X-Name' => 'Project Term Name',
            'X-Type' => 'quote',
            'X-Callback' => null,
        ];

        $this->assertSame($expected, $testee->to_header_array());
    }
}
