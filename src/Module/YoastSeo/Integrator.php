<?php

/**
 * Class Integrate
 *
 * @since   1.0.0
 * @package Translationmanager\Module\YoastSeo
 */

namespace Translationmanager\Module\YoastSeo;

use Translationmanager\Module\Integrable;
use Translationmanager\Module\Processor\ProcessorBusFactory;

/**
 * Class Integrate
 *
 * @since   1.0.0
 * @package Translationmanager\Module\YoastSeo
 */
class Integrator implements Integrable
{
    /**
     * @inheritdoc
     */
    public static function integrate(ProcessorBusFactory $processorBusFactory, $pluginPath)
    {
        if (!class_exists('WPSEO_Meta')) {
            return;
        }

        $wordpressSeo = new WordPressSeo();

        add_action('translationmanager_outgoing_data', [$wordpressSeo, 'prepare_outgoing']);
        add_action('translationmanager_updated_post', [$wordpressSeo, 'update_translation']);
    }

    private function __construct()
    {
    }
}
