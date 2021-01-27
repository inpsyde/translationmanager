<?php

/**
 * Project Handler
 *
 * @since   1.0.0
 * @package Translationmanager
 */

namespace Translationmanager;

use Exception;

/**
 * Class ProjectHandler
 *
 * @since   1.0.0
 * @package Translationmanager
 */
class ProjectHandler
{
    /**
     * Create Project
     *
     * @param string $title The title for the project.
     *
     * @return int The newly term ID
     * @throws \Exception In case the project cannot be created.
     *
     * @since 1.0.0
     */
    public function create_project($title)
    {
        // Check if project already exists.
        $ids = term_exists($title, 'translationmanager_project');

        if (!$ids) {
            // Create if it does not exists.
            $ids = wp_insert_term($title, 'translationmanager_project');
        }

        if (is_wp_error($ids)) {
            throw new Exception($ids->get_error_message());
        }

        return (int)$ids['term_id'];
    }

    /**
     * Add Translation
     *
     * @param int $project The project ID.
     * @param int $post_id The post associated to this project item.
     * @param int $lang_id The language id of the project item.
     *
     * @since 1.0.0
     */
    public function add_translation($project, $post_id, $lang_id)
    {
        $labels = get_post_type_labels(get_post_type_object(get_post_type($post_id)));

        $translation_id = wp_insert_post(
            [
                'post_type' => 'project_item',
                'post_title' => sprintf(
                    __('%1$s: "%2$s"', 'translationmanager'),
                    esc_html($labels->singular_name),
                    get_the_title($post_id)
                ),
                'meta_input' => [
                    '_translationmanager_target_id' => $lang_id,
                    '_translationmanager_post_id' => $post_id,
                ],
            ]
        );

        // Retrieve the slug of the term because we are dealing with non hierarchical terms.
        $project = get_term_field('slug', $project, 'translationmanager_project');

        wp_set_post_terms($translation_id, [$project], 'translationmanager_project');
    }

    /**
     * Create new Project by Date
     *
     * @return int The new project ID
     * @throws \Exception In case the project cannot be created.
     *
     * @since 1.0.0
     */
    public static function create_project_using_date()
    {
        return (new self())->create_project(
            sprintf(esc_html__('Project %s', 'translationmanager'), date('Y-m-d H:i:s'))
        );
    }
}
