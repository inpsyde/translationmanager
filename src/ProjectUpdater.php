<?php

/**
 * Project Updater
 *
 * @package Translationmanager\Admin
 */

namespace Translationmanager;

/**
 * @since   1.0.0
 * @package Translationmanager\Admin
 */
class ProjectUpdater
{
    /**
     * Append to Title
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $append_to_title = '';

    /**
     * Set Hooks
     *
     * @since 1.0.0
     *
     * Add action and filters to make the thing work
     */
    public function init()
    {
        add_action(
            'translationmanager_action_project_add_translation',
            [$this, 'force_ancestors_in_project'],
            10,
            4
        );
    }

    /**
     * Run after all the projects items referring a post have been created and assigned to a project, extracts the
     * ancestor ids of the post being translated and add those ancestors to cart as well, ith they are not there
     * already.
     *
     * @wp-hook translationmanager_action_project_add_translation
     *
     * @param int $project The project ID.
     * @param int $post_id The post ID.
     * @param \Translationmanager\Domain\Language[] $languages A list of languages.
     *
     * @return int The project ID
     * @since   1.0.0
     */
    public function force_ancestors_in_project($project, $post_id, $languages)
    {
        $post = get_post($post_id);

        if (!$post || !apply_filters('translationmanager_force_add_parent_translations', false, $post)) {
            return $project;
        }

        $ancestors = wp_parse_id_list(get_post_ancestors($post));

        if (!$ancestors) {
            return $project;
        }

        $project_items = get_posts(
            [
                'fields' => 'ids',
                'post_type' => 'project_item',
                'nopaging' => true,
                'tax_query' => [
                    [
                        'taxonomy' => 'translationmanager_project',
                        'terms' => [$project],
                        'field' => 'term_id',
                    ],
                ],
            ]
        );

        $already_in_project = [];
        foreach ($project_items as $project_item_id) {
            $lang = get_post_meta($project_item_id, '_translationmanager_target_id', true);
            if (!$lang || !in_array($lang, $languages, true)) {
                continue;
            }

            $added_ancestor_id = (int)get_post_meta(
                $project_item_id,
                '_translationmanager_post_id',
                true
            );
            if ($added_ancestor_id && in_array($added_ancestor_id, $ancestors, true)) {
                empty($already_in_project[$lang]) and $already_in_project[$lang] = [];
                $already_in_project[$lang][$added_ancestor_id] = true;
            }
        }

        $original_title = get_the_title($post);
        $ancestor_hint = esc_html__('ancestor of: "%s"', 'translationmanager');
        $this->append_to_title = '(' . sprintf($ancestor_hint, $original_title) . ')';

        $handler = new \Translationmanager\ProjectHandler();

        add_filter('wp_insert_post_data', [$this, 'update_project_item_title'], 10);

        foreach ($languages as $lang_id) {
            foreach ($ancestors as $ancestor_id) {
                if (empty($already_in_project[$lang_id][$ancestor_id])) {
                    $handler->add_translation($project, $ancestor_id, $lang_id);
                }
            }
        }

        $this->append_to_title = '';
        remove_filter('wp_insert_post_data', [$this, 'update_project_item_title'], 10);

        return $project;
    }

    /**
     * Filter the cart item post data being added, appending to title an hint that post was added automatically because
     * ancestor of another post.
     *
     * @param array $data Data to update.
     *
     * @return array data updated
     * @since 1.0.0
     */
    public function update_project_item_title(array $data)
    {
        if ($this->append_to_title & !empty($data['post_type']) & $data['post_type'] === 'project_item') {
            empty($data['post_title']) and $data['post_title'] = '';
            $data['post_title'] and $data['post_title'] .= ' ';

            $data['post_title'] .= $this->append_to_title;
        }

        return $data;
    }
}
