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
use Translationmanager\Module\ACF\Processor\IncomingMetaProcessor;
use Translationmanager\Module\ACF\Processor\OutgoingMetaProcessor;
use Translationmanager\Module\Mlp\DataProcessor;
use Translationmanager\Module\Mlp\Integrator as MultilingualPressIntegrator;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use Translationmanager\Module\WooCommerce\Integrator as WooCommerceIntegrator;
use Translationmanager\Module\YoastSeo\Integrator as WordPressSeoByYoastIntegrator;
use Translationmanager\Module\ACF\Integrator as ACFIntegrator;
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

        $container[AcfIntegrator::class] = function (Container $container) {
            return new AcfIntegrator($container['tm/ACF/processor_bus']);
        };

        $container['tm/ACF/processor_bus'] = function (Container $container) {
            $outgoingMetaProcessor = $container['tm/ACF/outgoing_meta_processor'];
            $incomingMetaProcessor = $container['tm/ACF/incoming_meta_processor'];
            $processorBusFactory = $container[ProcessorBusFactory::class];
            $processorBus = $processorBusFactory->create();
            $processorBus
                ->pushProcessor($outgoingMetaProcessor)
                ->pushProcessor($incomingMetaProcessor);

            return $processorBus;
        };

        $container['tm/ACF/outgoing_meta_processor'] = function () {
            return new OutgoingMetaProcessor();
        };

        $container['tm/ACF/incoming_meta_processor'] = function () {
            return new IncomingMetaProcessor();
        };

        $container[ModulesProvider::class] = function (Container $container) {
            return new ModulesProvider([
                'wp-seo' => $container[WordPressSeoByYoastIntegrator::class],
                'multilingualpress' => $container[MultilingualPressIntegrator::class],
                'multilingual-press' => $container[MultilingualPressIntegrator::class],
                'woocommerce' => $container[WooCommerceIntegrator::class],
                'acf' => $container[ACFIntegrator::class],
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
