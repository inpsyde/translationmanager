<?php

namespace Translationmanager\Module\ACF;

use Translationmanager\Module\Integrable;
use Translationmanager\Module\Processor\ProcessorBus;
use Translationmanager\Translation;

/**
 * Class Integrator
 *
 * The Class will integrate ACF with TM,
 * so the Data from ACF fields will be sent to API and the translated Data will be received
 *
 * @package Translationmanager\Module\ACF
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
