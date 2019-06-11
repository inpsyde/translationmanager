<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\TableList;

use Brain\Monkey\Functions;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Translationmanager\TableList\ProjectItem;
use Translationmanager\TableList\TableList;
use \TranslationmanagerTests\TestCase;

/**
 * Class ProjectItemTest
 *
 * @package TranslationmanagerTests\TableList
 */
class ProjectItemTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Test Instance
     */
    public function testInstance()
    {
        Mockery::mock('overload:\WP_List_Table');

        $sut = new ProjectItem();

        $this->assertInstanceOf(ProjectItem::class, $sut);
    }

    /**
     * Test Project ID By Request Is Valid
     */
    public function testProjectIDByRequestIsValid()
    {
        Mockery::mock('overload:' . TableList::class, [
            'get_items_per_page' => 10,
        ]);

        $term = Mockery::mock('WP_Term');
        $term->term_id = 10;

        Functions\when('Translationmanager\\Functions\\filter_input')
            ->justReturn([
                'translationmanager_project_id' => 10,
            ]);

        Functions\expect('get_term')
            ->once()
            ->with(10, Mockery::type('string'))
            ->andReturn($term);

        Functions\expect('Translationmanager\\Functions\\get_project_items')
            ->once()
            ->with(10, Mockery::type('array'))
            ->andReturn([]);

        $sut = new ProjectItem();
        $sut->screen = (object)[
            'id' => 'project_item',
        ];

        $sut->items();
    }

    /**
     * Test Project by ID Request Throw an Exception if get Term returns a WP_Error
     */
    public function testProjectIDRequestThrowExceptionIfWpError()
    {
        Mockery::mock('overload:' . TableList::class);

        Functions\when('Translationmanager\\Functions\\filter_input')
            ->justReturn(['translationmanager_project_id' => 10]);

        Functions\expect('get_term')
            ->once()
            ->with(10, Mockery::type('string'))
            ->andReturn(Mockery::mock('WP_Error'));

        $sut = new ProjectItem();

        $sut->items();
    }

    /**
     * Test Items Return Empty if No Project Id Can be Retrieved
     */
    public function testItemsReturnEmptyIfNoProjectIDCanBeRetrievedByRequest()
    {
        Mockery::mock('overload:' . TableList::class);

        Functions\when('Translationmanager\\Functions\\filter_input')
            ->justReturn(false);

        $sut = new ProjectItem();

        $response = $sut->items();

        $this->assertSame([], $response);
    }
}
