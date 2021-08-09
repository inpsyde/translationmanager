<?php

# -*- coding: utf-8 -*-

namespace Translationmanager\Module\ACF\Processor;

use Translationmanager\Exception\UnexpectedEntityException;
use Translationmanager\Module\Processor\IncomingProcessor;
use Translationmanager\Module\TranslationEntityAwareTrait;
use Translationmanager\Module\ACF\Integrator;
use Translationmanager\Utils\NetworkState;
use Translationmanager\Translation;
use WP_Error;
use WP_Term;

/**
 * Class IncomingMetaProcessor
 *
 * Will receive the ACF data and will import
 *
 * @package Translationmanager\Module\ACF\Processor
 */
class IncomingMetaProcessor implements IncomingProcessor
{
    use TranslationEntityAwareTrait;

    /**
     * @inheritDoc
     */
    public function processIncoming(Translation $translation)
    {
        if (!$translation->is_valid()) {
            return;
        }

        $project = $this->getProject();

        if (!$project instanceof WP_Term) {
            return;
        }

        $notTranslatedFieldsToImport = get_term_meta(
            $project->term_id,
            Integrator::NOT_TRANSLATABE_ACF_FIELDS,
            true
        ) ?? [];

        $networkState = NetworkState::create();
        $targetSiteId = $translation->target_site_id();

        $networkState->switch_to($targetSiteId);

        try {
            $post = $this->post($translation);
        } catch (UnexpectedEntityException $exc) {
            $networkState->restore();
            return;
        }

        $translatedFieldsToImport = [];
        if ($translation->has_value(Integrator::ACF_FIELDS, Integrator::_NAMESPACE)) {
            $translatedFieldsToImport = $translation->get_value(
                Integrator::ACF_FIELDS,
                Integrator::_NAMESPACE
            );
        }

        $fieldsToImport = array_merge($translatedFieldsToImport, $notTranslatedFieldsToImport);
        if (!empty($fieldsToImport)) {
            foreach ($fieldsToImport as $fieldKey => $fieldValue) {
                update_post_meta($post->ID, $fieldKey, $fieldValue);
            }
        }

        $networkState->restore();
    }

    /**
     * Get the project info
     *
     * @return array|WP_Error|WP_Term|null
     */
    protected function getProject()
    {
        $projectId = (int)filter_input(
            INPUT_POST,
            'translationmanager_project_id',
            FILTER_SANITIZE_NUMBER_INT
        );

        return get_term($projectId, 'translationmanager_project');
    }
}
