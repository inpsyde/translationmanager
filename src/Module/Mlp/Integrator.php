<?php

namespace Translationmanager\Module\Mlp;

use Inpsyde_Property_List_Interface;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection;
use Translationmanager\Module\Integrable;
use Translationmanager\Module\Processor\ProcessorBusFactory;

/**
 * Class Integrate
 *
 * @since   1.0.0
 * @package Translationmanager\Module\Mlp
 */
class Integrator implements Integrable
{
    /**
     * @inheritdoc
     */
    public function integrate()
    {
        $instance = new self;

        if (class_exists('Inpsyde\\MultilingualPress\\MultilingualPress')) {
            $instance->mlp3();
        }
        if (class_exists('Multilingual_Press')) {
            $instance->mlp2();
        }
    }

    /**
     * Mlp 2 Integration
     *
     * @return void
     * @since 1.0.0
     */
    private function mlp2()
    {
        add_action(
            'inpsyde_mlp_loaded',
            function (Inpsyde_Property_List_Interface $data) {
                $connectorBootstrap = new ConnectorBootstrap(
                    new ConnectorFactory(
                        new ProcessorBusFactory()
                    )
                );
                $connectorBootstrap->boot(
                    new Adapter(
                        2,
                        $data->get('site_relations'),
                        $data->get('content_relations')
                    )
                );
            }
        );
    }

    /**
     * Mlp 3 Integration
     *
     * @return void
     * @since 1.0.0
     */
    private function mlp3()
    {
        add_action(
            'multilingualpress.add_service_providers',
            function (ServiceProvidersCollection $providers) {
                $providers->add(new ServiceProvider());
            }
        );
    }
}
