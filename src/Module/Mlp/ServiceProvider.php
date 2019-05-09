<?php
/**
 * Service Provider
 *
 * @since   1.0.0
 * @package Translationmanager\Module\Mlp
 */

namespace Translationmanager\Module\Mlp;

use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;

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
                $container[PluginProperties::class]['filePath'],
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
        $adapter = $container[Adapter::class];
        add_action(
            'multilingualpress.bootstrapped',
            function () use ($adapter) {

                Integrator::action($adapter);
            }
        );
    }
}
