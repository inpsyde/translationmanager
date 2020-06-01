<?php # -*- coding: utf-8 -*-

namespace TranslationmanagerTests\Unit\Module\Mlp;

use Brain\Monkey\Filters;
use Brain\Monkey\Actions;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Module\Mlp\Connector;
use Translationmanager\Module\Mlp\ConnectorBootstrap as Testee;
use Translationmanager\Module\Mlp\ConnectorFactory;
use TranslationmanagerTests\TestCase;

/**
 * Class ConnectorBootstrapTest
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class ConnectorBootstrapTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Test Instance
     */
    public function testInstance()
    {
        $connectorFactory = Mockery::mock(ConnectorFactory::class);
        $testee = new Testee($connectorFactory);

        self::assertInstanceOf(Testee::class, $testee);
    }

    /**
     * Test Bootstrap
     */
    public function testBootstrap()
    {
        {
            $adapter = Mockery::mock(Adapter::class);
            $connector = Mockery::mock(Connector::class);
            $connectorFactory = Mockery::mock(ConnectorFactory::class);
            $testee = new Testee($connectorFactory);
        }

        {
            $connectorFactory
                ->shouldReceive('create')
                ->once()
                ->with($adapter)
                ->andReturn($connector);

            /**
             * Main reason of the test
             *
             * We want to ensure all of the proper Filters and Actions are added
             */
            Filters\expectAdded('translationmanager_current_language')
                ->once()
                ->with([$connector, 'current_language']);

            Filters\expectAdded('translationmanager_languages')
                ->once()
                ->with([$connector, 'related_sites'], Mockery::type('int'), Mockery::type('int'));

            Filters\expectAdded('translation_manager_languages_by_site_id')
                ->once()
                ->with([$connector, 'related_sites'], Mockery::type('int'), Mockery::type('int'));

            Actions\expectAdded('translationmanager_outgoing_data')
                ->once()
                ->with([$connector, 'prepare_outgoing']);

            Filters\expectAdded('translationmanager_post_updater')
                ->once()
                ->with([$connector, 'prepare_updater']);
        }

        {
            $testee->boot($adapter);
        }
    }
}
