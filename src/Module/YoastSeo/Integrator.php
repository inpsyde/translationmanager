<?php

/**
 * Class Integrate
 *
 * @since   1.0.0
 * @package Translationmanager\Module\YoastSeo
 */

namespace Translationmanager\Module\YoastSeo;

use Translationmanager\Module\Integrable;

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
    public function integrate()
    {
        if (!class_exists('WPSEO_Meta')) {
            return;
        }

        $wordpressSeo = new WordPressSeo();

        add_action('translationmanager_outgoing_data', [$wordpressSeo, 'prepare_outgoing']);
        add_action('translationmanager_updated_post', [$wordpressSeo, 'update_translation']);
    }
}
