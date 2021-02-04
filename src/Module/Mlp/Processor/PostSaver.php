<?php

// -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Processor;

use stdClass;
use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Module\ModuleIntegrator;
use Translationmanager\Utils\NetworkState;
use Translationmanager\Module\Processor\IncomingProcessor;
use Translationmanager\Translation;
use WP_Post;

class PostSaver implements IncomingProcessor
{
    const SAVED_POST_KEY = 'saved_post';

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * TaxonomiesSync constructor
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @inheritDoc
     */
    public function processIncoming(Translation $translation)
    {
        $post_vars = get_object_vars(new WP_Post(new stdClass()));
        $post_data = [];

        foreach ($post_vars as $key => $value) {
            if ($translation->has_value($key)) {
                $post_data[$key] = $translation->get_value($key);
            }
        }

        $networkState = NetworkState::create();
        $networkState->switch_to($translation->target_site_id());

        $existing_id = array_key_exists('ID', $post_data) ? $post_data['ID'] : 0;

        // Save post with all the data.
        $target_post_id = wp_insert_post($post_data, true);

        do_action(
            'translationmanager_log',
            [
                'message' => 'Incoming post data from API processed.',
                'context' => [
                    'Post data ID' => $existing_id . ' (should equal "Source post ID")',
                    'Source post ID' => $translation->source_post_id() . ' (should equal "Post data ID")',
                    'Result' => is_wp_error($target_post_id)
                        ? $target_post_id->get_error_message()
                        : "Post ID {$target_post_id} saved correctly.",
                    'Target lang' => $translation->target_language(),
                    'Target site' => $translation->target_site_id(),
                    'Source site' => $translation->source_site_id(),
                ],
            ]
        );

        if (is_wp_error($target_post_id)) {
            $target_post_id = 0;
        }

        $target_post = $target_post_id ? get_post($target_post_id) : null;

        $networkState->restore();

        if (!$target_post) {
            return;
        }

        $sync_on_update = true;
        $isUpdateKey = $translation->get_meta(
            PostDataBuilder::IS_UPDATE_KEY,
            ModuleIntegrator::POST_DATA_NAMESPACE
        );

        if ($isUpdateKey) {
            $sync_on_update = apply_filters(
                'translationmanager_mlp_module_sync_post_relation_on_update',
                true,
                $translation
            );
        }

        // If it is a new post creation, link created post with source post.
        if ($sync_on_update) {
            $this->adapter->set_relation(
                $translation->source_site_id(),
                $translation->target_site_id(),
                $translation->source_post_id(),
                $target_post->ID,
                'post'
            );
        }

        $translation->set_meta(
            self::SAVED_POST_KEY,
            $target_post,
            ModuleIntegrator::POST_DATA_NAMESPACE
        );
    }
}
