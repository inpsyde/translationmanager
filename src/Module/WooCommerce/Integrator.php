<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Module\WooCommerce;

use Translationmanager\Module\Integrable;
use Translationmanager\Module\Processor\ProcessorBusFactory;
use Translationmanager\Module\WooCommerce\Processor\IncomingMetaProcessor;
use Translationmanager\Module\WooCommerce\Processor\OutgoingMetaProcessor;
use Translationmanager\Translation;

/**
 * Class Integrator
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class Integrator implements Integrable
{
    const DATA_NAMESPACE = 'woocommerce';

    const PRODUCT_META_PURCHASE_NOTE = 'purchase_note';

    /**
     * @inheritDoc
     */
    public static function integrate(ProcessorBusFactory $processorBusFactory, $pluginPath)
    {
        // Temporary disabled until WooCommerce fields will be supported
        return;

        if (!function_exists('WC')) {
            return;
        }

        $processorBus = $processorBusFactory->create();
        $processorBus
            ->pushProcessor(new OutgoingMetaProcessor())
            ->pushProcessor(new IncomingMetaProcessor());

        add_action(
            'translationmanager_outgoing_data',
            function (Translation $translation) use ($processorBus) {
                $processorBus->process($translation);
            }
        );
        add_action(
            'translationmanager_updated_post',
            function (Translation $translation) use ($processorBus) {
                $processorBus->process($translation);
            }
        );
    }

    private function __construct()
    {
    }
}
