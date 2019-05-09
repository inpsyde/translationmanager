<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Tests\Unit;

use Brain\Nonces\NonceInterface;
use Mockery;
use Brain\Monkey\Functions;
use Translationmanager\Auth\Authable;
use Translationmanager\Notice\TransientNoticeService;
use Translationmanager\ProjectHandler;
use Translationmanager\ProjectUpdater;
use Translationmanager\Request\Api\AddTranslation;
use Translationmanager\Tests\TestCase;

/**
 * Class AddTranslationTest
 *
 * @package Translationmanager\Tests\unit\src
 */
class AddTranslationTest extends TestCase
{

    /**
     * Test Handle Create a new Project
     */
    public function testHandleCreateANewProject()
    {

        $_POST['translationmanager_action_project_add_translation'] = true;

        $authMock = Mockery::mock(Authable::class);
        $nonceMock = Mockery::mock(NonceInterface::class);
        $projectUpdaterMock = Mockery::mock('overload:' . ProjectUpdater::class);
        $projectHandlerMock = Mockery::mock('alias:' . ProjectHandler::class);
        $noticeMock = Mockery::mock('alias:' . TransientNoticeService::class);

        $authMock->shouldReceive('can')
            ->andReturn(true);

        $authMock->shouldReceive('request_is_valid')
            ->andReturn(true);

        $projectUpdaterMock
            ->shouldReceive('init')
            ->once();

        $projectHandlerMock
            ->shouldReceive('create_project_using_date')
            ->once()
            ->andReturn(1);

        $projectHandlerMock
            ->shouldReceive('add_translation')
            ->once()
            ->with(1, 2, '1');

        $noticeMock
            ->shouldReceive('add_notice')
            ->once()
            ->with('New Translation added successfully.', 'success');

        Functions\expect('Translationmanager\\Functions\\filter_input')
            ->andReturnUsing(function () {

                return [
                    'post_ID' => 2,
                    'translationmanager_language' => ['1'],
                ];
            });

        Functions\expect('Translationmanager\\Functions\\redirect_admin_page_network')
            ->once()
            ->with(Mockery::type('string'), [
                'page' => 'translationmanager-project',
                'translationmanager_project_id' => 1,
                'post_type' => 'project_item',
                'updated' => -1,
            ]);

        Functions\when('apply_filters')
            ->returnArg(2);

        Functions\when('wp_get_current_user')
            ->justReturn(Mockery::mock('\\WP_User'));

        Functions\when('update_user_meta')
            ->justReturn(true);

        Functions\when('get_current_user_id')
            ->justReturn(1);

        $translation = new AddTranslation($authMock, $nonceMock, $projectHandlerMock);

        $translation->handle();

        $this->assertTrue(true);
    }

    /**
     * Test Handle Insert Into an Existing Project
     */
    public function testHandleInsertIntoAnExistingProject()
    {

        $_POST['translationmanager_action_project_add_translation'] = true;

        $authMock = Mockery::mock(Authable::class);
        $nonceMock = Mockery::mock(NonceInterface::class);
        $projectUpdaterMock = Mockery::mock('overload:' . ProjectUpdater::class);
        $projectHandlerMock = Mockery::mock(ProjectHandler::class);
        $noticeMock = Mockery::mock('alias:' . TransientNoticeService::class);

        $authMock
            ->shouldReceive('can')
            ->andReturn(true);

        $authMock
            ->shouldReceive('request_is_valid')
            ->andReturn(true);

        $projectUpdaterMock
            ->shouldReceive('init')
            ->once();

        $projectHandlerMock
            ->shouldReceive('add_translation')
            ->once()
            ->with(1, 2, '1');

        $noticeMock
            ->shouldReceive('add_notice')
            ->once()
            ->with('New Translation added successfully.', 'success');

        Functions\expect('Translationmanager\\Functions\\filter_input')
            ->andReturnUsing(function () {

                return [
                    'translationmanager_project_id' => 1,
                    'post_ID' => 2,
                    'translationmanager_language' => ['1'],
                ];
            });

        Functions\expect('Translationmanager\\Functions\\redirect_admin_page_network')
            ->once()
            ->with(Mockery::type('string'), [
                'page' => 'translationmanager-project',
                'translationmanager_project_id' => 1,
                'post_type' => 'project_item',
                'updated' => -1,
            ]);

        Functions\when('apply_filters')
            ->returnArg(2);

        Functions\when('wp_get_current_user')
            ->justReturn(Mockery::mock('\\WP_User'));

        Functions\when('update_user_meta')
            ->justReturn(true);

        Functions\when('get_current_user_id')
            ->justReturn(1);

        $translation = new AddTranslation($authMock, $nonceMock, $projectHandlerMock);

        $translation->handle();

        $this->assertTrue(true);
    }

    /**
     * Test Empty Data Doesn't get Handled
     */
    public function testThatEmptyDataDoesntDoAnything()
    {

        $_POST['translationmanager_action_project_add_translation'] = true;

        $authMock = Mockery::mock(Authable::class);
        $nonceMock = Mockery::mock(NonceInterface::class);
        $projectHandlerMock = Mockery::mock(ProjectHandler::class);

        $authMock
            ->shouldReceive('can')
            ->andReturn(true);

        $authMock
            ->shouldReceive('request_is_valid')
            ->andReturn(true);

        Functions\expect('Translationmanager\\Functions\\filter_input')
            ->andReturnUsing('__return_empty_array');

        Functions\when('wp_get_current_user')
            ->justReturn(Mockery::mock('\\WP_User'));

        Functions\when('update_user_meta')
            ->justReturn(true);

        Functions\when('get_current_user_id')
            ->justReturn(1);

        $translation = new AddTranslation($authMock, $nonceMock, $projectHandlerMock);

        $response = $translation->handle();

        $this->assertSame(null, $response);
    }

    /**
     * @inheritDoc
     */
    public function setUp()
    {

        parent::setUp();

        Functions\when('esc_html__')
            ->returnArg(1);
    }
}
