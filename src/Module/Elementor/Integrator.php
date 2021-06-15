<?php

namespace Translationmanager\Module\Elementor;

use Translationmanager\Module\Integrable;
use Translationmanager\Module\Processor\ProcessorBus;
use Translationmanager\Translation;

/**
 * Class Integrator
 *
 * The Class will integrate Elementor with TM,
 * so the Data from Elementor fields will be sent to API and the translated Data will be received
 */
class Integrator implements Integrable
{
    const _NAMESPACE = 'Elementor';

    const ELEMENTOR_FIELDS = 'elementor_fields';
    const NOT_TRANSLATABE_DATA = 'not_translatable_elementor_fields';

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
        if (!did_action('elementor/loaded')) {
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
