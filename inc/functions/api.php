<?php

namespace Translationmanager\Functions;

use Exception;
use Translationmanager\Api;
use Translationmanager\Api\ApiException;
use Translationmanager\Domain\Project;
use Translationmanager\Plugin;
use Translationmanager\Setting\PluginSettings;
use Translationmanager\Translation;
use WP_Term;

/**
 * Retrieve API Instance
 *
 * Helper function to retrieve the instance of the translation manager api.
 * It's always return the same instance.
 *
 * @return \Translationmanager\Api The Instance
 * @since 1.0.0
 *
 * @api
 */
function translationmanager_api()
{
    static $api = null;

    $url = 'https://api.eurotext.de/api/v1';

    if (defined('TRANSLATION_MANAGER_API_URL') && TRANSLATION_MANAGER_API_URL) {
        $url = TRANSLATION_MANAGER_API_URL;
    }

    if ($api === null) {
        /**
         * Filter Api URL
         *
         * @param string The api url.
         *
         * @since 1.0.0
         */
        $url = apply_filters('translationmanager_api_url', $url);

        $api = new Api(
            get_option(PluginSettings::API_KEY),
            'b37270d25d5b3fccf137f7462774fe76',
            $url
        );
    }

    return $api;
}

/**
 * Update Project
 *
 * @param WP_Term $project The project term to use to retrieve the info to update the post.
 *
 * @return void
 * @throws Exception In case the project ID cannot be retrieved.
 *
 * @since 1.0.0
 *
 * @api
 */
function project_update(WP_Term $project)
{
    $project_id = get_term_meta($project->term_id, '_translationmanager_order_id', true);

    if (!$project_id) {
        throw new Exception(
            esc_html__('Invalid Project ID, impossible to update the project', 'translationmanager')
        );
    }

    $translation_data = translationmanager_api()->project()->get($project_id);

    foreach ($translation_data['items'] as $item_id => &$item) {
        $item = translationmanager_api()->project_item()->get($project_id, $item_id);

        if (!$item || !isset($item['data']) || !is_array($item['data'])) {
            continue;
        }

        foreach ($item['data'] as $incoming_translation) {
            $translation = Translation::for_incoming((array)$incoming_translation);

            /**
             * Fires for each item or translation received from the API.
             *
             * @param Translation $translation Translation data built from data received from API
             */
            do_action('translationmanager_incoming_data', $translation);

            /**
             * Filters the updater that executed have to return the updated post
             */
            $updater = apply_filters('translationmanager_post_updater', null, $translation);
            is_callable($updater) and $updater($translation);

            /**
             * Fires after the updater has updated the post.
             *
             * @param Translation $translation Translation data built from data received from API
             */
            do_action('translationmanager_updated_post', $translation);
        }
    }
}

/**
 * Retrieve project items statuses
 *
 * @param WP_Term $project_term The term instance to retrieve the project data.
 *
 * @return array All posts statues
 * @throws ApiException If something went wrong during retrieve the project data.
 *
 * @since 1.0.0
 *
 * @api
 */
function project_items_statuses(WP_Term $project_term)
{
    $statuses = [];

    $project_id = get_term_meta($project_term->term_id, '_translationmanager_order_id', true);
    if (!$project_id) {
        return $statuses;
    }

    $translation_data = translationmanager_api()->project()->get($project_id);
    if (!$translation_data) {
        return $statuses;
    }

    foreach ($translation_data['items'] as $item) {
        $post_title = isset($item[0]['post_title']) ? $item[0]['post_title'] : '';
        $slug = sanitize_title($post_title);
        $statuses[$slug] = $item['status'];
    }

    return $statuses;
}

/**
 * Get Global Project status
 *
 * @return string The translation status label
 * @throws ApiException If something went wrong during retrieve the project data.
 *
 * param \WP_Term $project_term The term instance to retrieve the project data.
 *
 * @api
 *
 * @since 1.0.0
 */
function project_global_status(WP_Term $project_term)
{
    $statuses = array_values(project_items_statuses($project_term));

    if (!$statuses) {
        return esc_html__('Unknown Status', 'translationmanager');
    }

    $unique_statuses = array_unique($statuses);

    $status = array_filter(
        $unique_statuses,
        function ($status) {

            return 'finished' === $status;
        }
    );

    return (count($status) === count($unique_statuses)
        ? esc_html__('Finished', 'translationmanager')
        : esc_html__('In Progress', 'translationmanager'));
}

/**
 * Project Order
 *
 * @param WP_Term $project_term The project term associated.
 *
 * @return mixed Whatever the update_term_meta returns
 * @throws ApiException In case the project cannot be created.
 *
 * @since 1.0.0
 *
 * @api
 */
function create_project_order(WP_Term $project_term)
{
    global $wp_version;

    $project = new Project(
        'WordPress',
        $wp_version,
        'translationmanager',
        (new Plugin())->version(),
        $project_term->name
    );

    $project_id = translationmanager_api()->project()->create($project);

    // Posts get collected by post type.
    $post_types = [];
    $languages = get_languages();
    $project_items = get_project_items($project_term->term_id);

    foreach ($project_items as $post) {
        if (!$post->_translationmanager_post_id || !isset($languages[$post->_translationmanager_target_id])) {
            // Invalid state, try next one.
            continue;
        }

        $source_post = get_post($post->_translationmanager_post_id);
        if (!$source_post) {
            continue;
        }

        $source_site_id = get_current_blog_id();
        $data = Translation::for_outgoing(
            $source_post,
            $source_site_id,
            $post->_translationmanager_target_id,
            $post->ID,
            $languages[$post->_translationmanager_target_id]->get_lang_code()
        );

        /**
         * Fires before translation data is transfered to the API.
         *
         * Data can be edited in place by listeners.
         *
         * @param Translation $data
         *
         * @since 1.0.0
         */
        do_action_ref_array('translationmanager_outgoing_data', [$data]);

        $post_types[$languages[$post->_translationmanager_target_id]->get_lang_code()][$source_post->post_type][] = $data->to_array();
    }

    foreach ($post_types as $post_type_target_language => $post_types_data) {
        foreach ($post_types_data as $post_type_name => $post_type_content) {
            translationmanager_api()
                ->project_item()
                ->create(
                    $project_id,
                    $post_type_name,
                    $post_type_target_language,
                    $post_type_content
                );
        }
    }

    translationmanager_api()
        ->project()
        ->update_status($project_id, 'new');

    // Set the order ID.
    if (!set_unique_term_meta($project_term, '_translationmanager_order_id', $project_id)) {
        return false;
    }

    // Set the default order status.
    return set_unique_term_meta(
        $project_term,
        '_translationmanager_order_status',
        esc_html__('In Transition', 'translationmanager')
    );
}
