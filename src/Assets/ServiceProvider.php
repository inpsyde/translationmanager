<?php

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Assets
 */

namespace Translationmanager\Assets;

use Pimple\Container;
use Translationmanager\Service\BootstrappableServiceProvider;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Assets
 */
class ServiceProvider implements BootstrappableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container['Assets.Translationmanager'] = function (Container $container) {

            return new Translationmanager($container['translationmanager.plugin']);
        };
    }

    /**
     * @inheritdoc
     */
    public function boot(Container $container)
    {
        add_action('admin_head', [$container['Assets.Translationmanager'], 'register_style']);
    }
}
