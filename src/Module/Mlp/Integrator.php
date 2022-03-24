<?php

namespace Translationmanager\Module\Mlp;

use Inpsyde_Property_List_Interface;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection;
use Translationmanager\Module\Integrable;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use Translationmanager\Utils\Assert;

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
        if ($this->classExists('Inpsyde\\MultilingualPress\\MultilingualPress')) {
            $this->mlp3();
            return;
        }
        if ($this->classExists('Multilingual_Press')) {
            $this->mlp2();
        }
    }

    /**
     * Check if the Given Class Exists or not
     *
     * @param $class
     * @return bool
     */
    protected function classExists($class)
    {
        Assert::stringNotEmpty($class);

        return class_exists($class);
    }

    /**
     * Mlp 2 Integration
     *
     * @return void
     * @since 1.0.0
     */
    protected function mlp2()
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
    protected function mlp3()
    {
        add_action(
            'multilingualpress.add_service_providers',
            function (ServiceProvidersCollection $providers) {
                /** @psalm-suppress InvalidArgument */
                $providers->add(new ServiceProvider());
            }
        );
    }
}
