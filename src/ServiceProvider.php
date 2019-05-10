<?php # -*- coding: utf-8 -*-

namespace Translationmanager;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $container)
    {
        $container[Plugin::class] = function () {
            return new Plugin();
        };
    }
}
