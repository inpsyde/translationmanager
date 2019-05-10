<?php

namespace Translationmanager\Module\Mlp;

use Inpsyde_Property_List_Interface;
use Translationmanager\Utils\Assert;
use Translationmanager\Functions;
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
     * @var string
     */
    private $mlpVersion;

    /**
     * Integrate constructor
     *
     * @param string $pluginVersion
     * @since 1.0.0
     */
    public function __construct($pluginVersion)
    {
        Assert::semVersion($pluginVersion);

        $this->mlpVersion = $pluginVersion;
    }

    /**
     * @inheritdoc
     */
    public function integrate()
    {
        Functions\version_compare('3.0.0', $this->mlpVersion, '<=')
            ? $this->mlp3()
            : $this->mlp2();
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
