<?php

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */

namespace Translationmanager\MetaBox;

use Pimple\Container;
use Translationmanager\Service\BootstrappableServiceProvider;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */
class ServiceProvider implements BootstrappableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container['Metabox.Translation'] = function () {

            return new \Translationmanager\MetaBox\Translation();
        };
    }

    /**
     * @inheritdoc
     */
    public function boot(Container $container)
    {
        add_action('add_meta_boxes', [$container['Metabox.Translation'], 'add_meta_box']);
    }
}
