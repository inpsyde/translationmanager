<?php

# -*- coding: utf-8 -*-

namespace Translationmanager\Module\ACF\Processor;

use Translationmanager\Module\ACF\Integrator;
use Translationmanager\Module\Processor\OutgoingProcessor;
use Translationmanager\Translation;
use Translationmanager\Module\ACF\Acf;

/**
 * Class OutgoingMetaProcessor
 *
 * Will generate the outgoing ACF Data
 *
 * @package Translationmanager\Module\ACF\Processor
 */
class OutgoingMetaProcessor implements OutgoingProcessor
{
    const _NAMESPACE = 'ACF';

    /**
     * The Acf class
     *
     * @var Acf
     */
    private $acf;

    public function __construct(Acf $acf)
    {
        $this->acf = $acf;
    }

    /**
     * @inheritDoc
     */
    public function processOutgoing(Translation $translation)
    {
        if (!$translation->is_valid()) {
            return;
        }

        $projectItemId = $translation->get_meta('project_item_id');
        $sourcePostId = $translation->source_post_id();

        if (!$sourcePostId) {
            return;
        }

        $fields = get_field_objects($sourcePostId);
        if (!$fields) {
            return;
        }

        $acfFields = $this->acf->acfFieldKeys($fields, [], $sourcePostId);

        !empty($acfFields['to-not-translate']) ? $toNotTranslate = $acfFields['to-not-translate'] : [];
        unset($acfFields['to-not-translate']);
        if (!empty($acfFields)) {
            $translation->set_value(Integrator::ACF_FIELDS, $acfFields, self::_NAMESPACE);
        }
        if (!empty($toNotTranslate)) {
            update_post_meta($projectItemId, Integrator::NOT_TRANSLATABE_ACF_FIELDS, $toNotTranslate);
        }
    }
}
