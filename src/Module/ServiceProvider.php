<?php
/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Module
 */

namespace Translationmanager\Module;

use CachingIterator;
use Pimple\Container;
use Translationmanager\Module\Mlp\DataProcessor;
use Translationmanager\Module\Mlp\Integrator as MultilingualPressIntegrator;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use Translationmanager\Module\WooCommerce\Integrator as WooCommerceIntegrator;
use Translationmanager\Module\YoastSeo\Integrator as WordPressSeoByYoastIntegrator;
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
        $container[MultilingualPressIntegrator::class] = function () {
            return new MultilingualPressIntegrator();
        };
        $container[WordPressSeoByYoastIntegrator::class] = function () {
            return new WordPressSeoByYoastIntegrator();
        };
        $container[WooCommerceIntegrator::class] = function (Container $container) {
            return new WooCommerceIntegrator(
                $container[ProcessorBusFactory::class]
            );
        };

        $container[ModulesProvider::class] = function (Container $container) {
            return new ModulesProvider([
                'wp-seo' => $container[WordPressSeoByYoastIntegrator::class],
                'multilingualpress' => $container[MultilingualPressIntegrator::class],
                'multilingual-press' => $container[MultilingualPressIntegrator::class],
                'woocommerce' => $container[WooCommerceIntegrator::class],
            ]);
        };
        $container['Modules'] = function (Container $container) {
            return new CachingIterator(
                $container[ModulesProvider::class]->getIterator(),
                CachingIterator::FULL_CACHE
            );
        };
        $container[ModuleIntegrator::class] = function (Container $container) {
            return new ModuleIntegrator(
                $container['Modules']
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
