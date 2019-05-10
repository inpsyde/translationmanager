<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Module\WooCommerce;

use Translationmanager\Module\Integrable;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use Translationmanager\Module\WooCommerce\Processor\OutgoingMetaProcessor;
use Translationmanager\Translation;
use WP_Post;

/**
 * Class Integrator
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class Integrator implements Integrable
{
    const _NAMESPACE = 'woocommerce';

    /**
     * @inheritDoc
     */
    public static function integrate(ProcessorBusFactory $processorBusFactory, $pluginPath)
    {
        $processorBus = $processorBusFactory->create();
        $processorBus
            ->pushProcessor(new OutgoingMetaProcessor());

        add_action(
            'translationmanager_outgoing_data',
            function (Translation $translation) use ($processorBus) {
                $processorBus->process($translation);
            }
        );
        add_action(
            'translationmanager_updated_post',
            function (WP_Post $post, Translation $translation) use ($processorBus) {
                $processorBus->process($translation);
            }
        );
    }

    private function __construct()
    {
    }
}
