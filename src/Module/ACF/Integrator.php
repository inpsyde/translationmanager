<?php

/**
 * Class Integrate
 *
 * @since   1.0.0
 * @package Translationmanager\Module\ACF
 */

namespace Translationmanager\Module\ACF;

use Translationmanager\Module\Integrable;
use Translationmanager\Module\Processor\ProcessorBus;
use Translationmanager\Translation;

/**
 * Class Integrator
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class Integrator implements Integrable
{
    const _NAMESPACE = 'ACF';

    const ACF_FIELDS = 'acf_fields';
    const NOT_TRANSLATABE_ACF_FIELDS = 'not_translatable_acf_fields';

    /**
     * @var ProcessorBus
     */
    private $processorBus;

    /**
     * Integrator constructor
     * @param ProcessorBus $processorBus
     */
    public function __construct(ProcessorBus $processorBus)
    {
        $this->processorBus = $processorBus;
    }

    /**
     * @inheritDoc
     */
    public function integrate()
    {
        if (!class_exists('ACF')) {
            return;
        }

        $processorBus = $this->processorBus;

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
}
