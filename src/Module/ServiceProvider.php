<?php
/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Module
 */

namespace Translationmanager\Module;

use Pimple\Container;
use Translationmanager\Module\Mlp\ConnectorBootstrap;
use Translationmanager\Module\Mlp\ConnectorFactory;
use Translationmanager\Module\Mlp\DataProcessor;
use Translationmanager\Module\Processor\ProcessorBus;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use Translationmanager\Module\WooCommerce\Integrator;
use Translationmanager\Plugin;
use Translationmanager\Service\IntegrableServiceProvider;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Module
 */
class ServiceProvider implements IntegrableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container[ModulesProvider::class] = function () {
            return new ModulesProvider([
                'wp-seo' => YoastSeo\Integrator::class,
                'multilingualpress' => Mlp\Integrator::class,
                'multilingual-press' => Mlp\Integrator::class,
                'woocommerce' => WooCommerce\Integrator::class,
            ]);
        };
        $container[ModuleIntegrator::class] = function (Container $container) {
            return new ModuleIntegrator(
                $container[Plugin::class],
                $container[ModulesProvider::class],
                $container[ProcessorBusFactory::class]
            );
        };
        $container[ProcessorBusFactory::class] = function () {
            return new ProcessorBusFactory();
        };
    }

    /**
     * @inheritdoc
     */
    public function integrate(Container $container)
    {
        $container[ModuleIntegrator::class]->integrate();
    }
}
