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
use Translationmanager\Module\ACF\Acf;
use Translationmanager\Module\Mlp\Integrator as MultilingualPressIntegrator;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use Translationmanager\Module\WooCommerce\Integrator as WooCommerceIntegrator;
use Translationmanager\Module\YoastSeo\Integrator as WordPressSeoByYoastIntegrator;
use Translationmanager\Module\ACF\Integrator as ACFIntegrator;
use Translationmanager\Module\Elementor\Integrator as ElementorIntegrator;
use Translationmanager\Module\Elementor\Processor\IncomingMetaProcessor as ElementorIncomingMetaProcessor;
use Translationmanager\Module\Elementor\Processor\OutgoingMetaProcessor as ElementorOutgoingMetaProcessor;
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
            return new AcfIntegrator($container['tm/acf/processor_bus']);
        };

        $container[ElementorIntegrator::class] = function (Container $container) {
            return new ElementorIntegrator($container['tm/elementor/processor_bus']);
        };

        $container['tm/acf/processor_bus'] = function (Container $container) {
            $outgoingMetaProcessor = $container['tm/acf/outgoing_meta_processor'];
            $incomingMetaProcessor = $container['tm/acf/incoming_meta_processor'];
            $processorBusFactory = $container[ProcessorBusFactory::class];
            $processorBus = $processorBusFactory->create();
            $processorBus
                ->pushProcessor($outgoingMetaProcessor)
                ->pushProcessor($incomingMetaProcessor);

            return $processorBus;
        };

        $container['tm/acf/outgoing_meta_processor'] = function (Container $container) {
            return new OutgoingMetaProcessor($container['tm/acf/acf']);
        };

        $container['tm/acf/acf'] = function () {
            return new Acf();
        };

        $container['tm/acf/incoming_meta_processor'] = function () {
            return new IncomingMetaProcessor();
        };

        $container['tm/elementor/processor_bus'] = function (Container $container) {
            $outgoingMetaProcessor = new ElementorOutgoingMetaProcessor();
            $incomingMetaProcessor = new ElementorIncomingMetaProcessor();
            $processorBusFactory = $container[ProcessorBusFactory::class];
            $processorBus = $processorBusFactory->create();
            $processorBus
                ->pushProcessor($outgoingMetaProcessor)
                ->pushProcessor($incomingMetaProcessor);

            return $processorBus;
        };

        $container[ModulesProvider::class] = function (Container $container) {
            return new ModulesProvider([
                'wp-seo' => $container[WordPressSeoByYoastIntegrator::class],
                'multilingualpress' => $container[MultilingualPressIntegrator::class],
                'multilingual-press' => $container[MultilingualPressIntegrator::class],
                'woocommerce' => $container[WooCommerceIntegrator::class],
                'acf' => $container[ACFIntegrator::class],
                'elementor' => $container[ElementorIntegrator::class],
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
