<?php

# -*- coding: utf-8 -*-

namespace Translationmanager\Module\Elementor\Processor;

use Translationmanager\Exception\UnexpectedEntityException;
use Translationmanager\Module\Processor\IncomingProcessor;
use Translationmanager\Module\TranslationEntityAwareTrait;
use Translationmanager\Module\Elementor\Integrator;
use Translationmanager\Utils\NetworkState;
use Translationmanager\Translation;

/**
 * Class IncomingMetaProcessor
 *
 * Will receive the ACF data and will import
 *
 * @package Translationmanager\Module\Elementor\Processor
 */
class IncomingMetaProcessor implements IncomingProcessor
{
    use TranslationEntityAwareTrait;

    const TRANSLATABLE_SETTINGS = [
        'title',
        'section_title',
        'editor',
        'text',
        'title_text',
        'description_text',
        'inner_text',
        'testimonial_content',
        'testimonial_name',
        'testimonial_job',
        'tabs',
        'alert_title',
        'alert_description',
        'html',
        'link_text',
        'slides',
    ];
    const TRANSLATABLE_WIDGETS = [
        'heading',
        'text-editor',
        'button',
        'icon-box',
        'star-rating',
        'icon-list',
        'counter',
        'progress',
        'testimonial',
        'tabs',
        'accordion',
        'toggle',
        'alert',
        'html',
        'read-more',
        'text-path',
        'slides',
    ];

    /**
     * @inheritDoc
     */
    public function processIncoming(Translation $translation)
    {
        if (!$translation->is_valid()) {
            return null;
        }

        $projectItemId = $translation->get_meta('project_item_id');

        $notTranslatedFieldsToImport = get_post_meta($projectItemId, Integrator::NOT_TRANSLATABE_DATA, true);

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
        if ($translation->has_value(Integrator::ELEMENTOR_FIELDS, Integrator::_NAMESPACE)) {
            $translatedFieldsToImport = $translation->get_value(
                Integrator::ELEMENTOR_FIELDS,
                Integrator::_NAMESPACE
            );
        }

        if (empty($notTranslatedFieldsToImport) || empty($translatedFieldsToImport)) {
            return;
        }

        foreach ($notTranslatedFieldsToImport as $metaKey => $metaValue) {
            if ($metaKey === '_elementor_data') {
                $metaValue = $this->replaceTranslations(json_decode($metaValue), $translatedFieldsToImport);
                $metaValue = str_replace('\\', '\\\\', json_encode($metaValue));
            }

            update_post_meta($post->ID, $metaKey, $metaValue);
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }

        $networkState->restore();
    }

    /**
     * Will replace the translation data within _elementor_data meta
     *
     * @param array $sourceData the data from _elementor_data meta
     * @param array $translationData the incoming data which should replace
     * @return array replaced data
     */
    protected function replaceTranslations(array $sourceData, array $translationData): array
    {

        foreach ($sourceData as $data) {
            if (!empty($data->elements)) {
                $this->replaceTranslations($data->elements, $translationData);
            }
            if (
                isset($data->elType) &&
                $data->elType === 'widget' &&
                isset($data->widgetType) &&
                in_array($data->widgetType, self::TRANSLATABLE_WIDGETS) &&
                !empty($data->settings)
            ) {
                foreach ((array)$data->settings as $key => $setting) {
                    if (
                        !in_array($key, self::TRANSLATABLE_SETTINGS)
                        && !is_array($setting)
                        && !in_array(str_replace('_', '-', $key), self::TRANSLATABLE_WIDGETS)
                    ) {
                        continue;
                    }
                    $id = 'id-' . $data->id;
                    if (key_exists($id, $translationData)) {
                        $data->settings = array_merge((array)$data->settings, $translationData[$id]);
                    }
                }
            }
        }

        return $sourceData;
    }
}
