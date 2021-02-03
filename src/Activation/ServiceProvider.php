<?php

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Installation
 */

namespace Translationmanager\Activation;

use Pimple\Container;
use Translationmanager\Service\IntegrableServiceProvider;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Installation
 */
class ServiceProvider implements IntegrableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container['Activation.Activator'] = function (Container $container) {

            return new Activator($container['translationmanager.plugin']);
        };
    }

    /**
     * @inheritdoc
     */
    public function integrate(Container $container)
    {
        $container['Activation.Activator']->store_version();
    }
}
