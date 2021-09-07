<?php

namespace Translationmanager\Module\YoastSeo;

use Translationmanager\Exception\UnexpectedEntityException;
use Translationmanager\Module\TranslationEntityAwareTrait;
use Translationmanager\Utils\NetworkState;
use Translationmanager\Translation;
use WPSEO_Meta;

class WordPressSeo
{
    use TranslationEntityAwareTrait;

    const _NAMESPACE = 'wordpress_seo';

    /**
     * Store WordPress SEO meta fields related to source post into translation data, using meta for fields that should
     * not be translated.
     *
     * @param Translation $translation
     */
    public function prepare_outgoing(Translation $translation)
    {
        if (!$translation->is_valid()) {
            return;
        }

        $projectItemId = $translation->get_meta('project_item_id');
        $source_post_id = $translation->source_post_id();

        if (!$source_post_id) {
            return;
        }

        $to_translate = [
            'title',
            'metadesc',
            'focuskw',
            'bctitle',
        ];

        $to_not_translate = [
            'meta-robots-noindex',
            'meta-robots-nofollow',
            'meta-robots-adv',
        ];

        foreach ($to_translate as $key) {
            $field = get_post_meta($source_post_id, WPSEO_Meta::$meta_prefix . $key, true);
            $translation->set_value($key, $field, self::_NAMESPACE);
        }

        $projectMeta = [];
        foreach ($to_not_translate as $key) {
            $projectMeta[$key] = get_post_meta($source_post_id, WPSEO_Meta::$meta_prefix . $key, true);
        }

        update_post_meta($projectItemId, self::_NAMESPACE, $projectMeta);
    }

    /**
     * After a translation post has been updated, updates its meta merging translated data and meta data that were set
     * on API request.
     *
     * @wp-hook translationmanager_updated_post
     *
     * @param Translation $translation
     */
    public function update_translation(Translation $translation)
    {
        if (!$translation->is_valid()) {
            return;
        }

        $projectItemId = $translation->get_meta('project_item_id');

        $not_translated = get_post_meta($projectItemId, self::_NAMESPACE, true);

        $networkState = NetworkState::create();

        $networkState->switch_to($translation->target_site_id());

        try {
            $post = $this->post($translation);
        } catch (UnexpectedEntityException $exc) {
            $networkState->restore();
            return;
        }

        $translated = $translation->get_value(self::_NAMESPACE);
        $all_meta = array_filter(array_merge($not_translated, $translated));

        foreach ($all_meta as $key => $value) {
            $exists = get_post_meta($post->ID, WPSEO_Meta::$meta_prefix . $key);
            // Existent non-translated data are not updated
            if (!$exists && isset($translated[$key])) {
                update_post_meta($post->ID, WPSEO_Meta::$meta_prefix . $key, $value);
            }
        }

        $networkState->restore();
    }
}
