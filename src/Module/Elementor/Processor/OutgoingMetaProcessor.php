<?php

# -*- coding: utf-8 -*-

namespace Translationmanager\Module\Elementor\Processor;

use Translationmanager\Module\Elementor\Integrator;
use Translationmanager\Module\Processor\OutgoingProcessor;
use Translationmanager\Translation;

/**
 * Class OutgoingMetaProcessor
 *
 * Will generate the outgoing ACF Data
 *
 * @package Translationmanager\Module\ACF\Processor
 */
class OutgoingMetaProcessor implements OutgoingProcessor
{
    const KEYS_TO_SYNC = ['_elementor_data', '_elementor_controls_usage', '_elementor_css', '_elementor_edit_mode'];
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

    const _NAMESPACE = 'Elementor';

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

        if (!$projectItemId || !$sourcePostId) {
            return;
        }

        $untranslatableData = $translatableElements = [];
        foreach (self::KEYS_TO_SYNC as $meta) {
            $untranslatableData[$meta] = get_post_meta($sourcePostId, $meta, true);

            if ($meta === '_elementor_data') {
                $elementorData = get_post_meta($sourcePostId, $meta, true);
                $elementorData = json_decode($elementorData);
                if (empty($elementorData)) {
                    continue;
                }
                $translatableElements = $this->findTranslatableValues($elementorData);
            }
        }
        if (empty($translatableElements) || empty($untranslatableData)) {
            return;
        }

        $translation->set_value(Integrator::ELEMENTOR_FIELDS, $translatableElements, self::_NAMESPACE);
        update_post_meta($projectItemId, Integrator::NOT_TRANSLATABE_DATA, wp_slash($untranslatableData));
    }

    protected function findTranslatableValues(array $elementorData): array
    {
        $translatableElements = [];
        foreach ($elementorData as $data) {
            if (!empty($data->elements)) {
                $translatableElements = array_merge(
                    $translatableElements,
                    $this->findTranslatableValues($data->elements)
                );
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
                    $translatableElementsKey = 'id-' . $data->id;
                    $translatableElements[$translatableElementsKey][$key] = $setting;
                }
            }
        }

        return $translatableElements;
    }
}
