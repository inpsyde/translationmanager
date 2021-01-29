<?php

/**
 * Service Provider
 *
 * @since   1.0.0
 * @package Translationmanager\Module\Mlp
 */

namespace Translationmanager\Module\Mlp;

use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Translationmanager\Module\Processor\ProcessorBusFactory;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Module\Mlp
 */
class ServiceProvider implements BootstrappableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container[Adapter::class] = function (Container $container) {
            return new Adapter(
                3,
                $container['Inpsyde\\MultilingualPress\\Framework\\Api\\SiteRelations'],
                $container['Inpsyde\\MultilingualPress\\Framework\\Api\\ContentRelations']
            );
        };
    }

    /**
     * @inheritdoc
     */
    public function bootstrap(Container $container)
    {
        $connectorBootstrap = new ConnectorBootstrap(
            new ConnectorFactory(
                new ProcessorBusFactory()
            )
        );
        $adapter = $container[Adapter::class];

        add_action('multilingualpress.bootstrapped', function () use ($connectorBootstrap, $adapter) {
            $connectorBootstrap->boot($adapter);
        });
    }
}
