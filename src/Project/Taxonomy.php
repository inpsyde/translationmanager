<?php

/**
 * Project
 *
 * @since   1.0.0
 * @package Translationmanager\Project
 */

namespace Translationmanager\Project;

use Brain\Nonces\WpNonce;
use Closure;
use Translationmanager\Functions;
use Translationmanager\Notice\TransientNoticeService;
use Translationmanager\View\Project\OrderInfo;
use WP_Term;

/**
 * Class Taxonomy
 *
 * @since   1.0.0
 * @package Translationmanager\Project
 */
class Taxonomy
{
    /**
     * @since 1.0.0
     */
    const COL_STATUS = 'translationmanager_order_status';

    /**
     * @since 1.0.0
     */
    const COL_ACTIONS = 'translationmanager_order_action';

    /**
     * Project Title and Description Form in edit page.
     *
     * @param string $value The views link. Untouched.
     *
     * @return string The untouched parameter
     * @todo  This is hooked in a filter, may create confusion about the value passed in.
     *        Is there a way to move into an action?
     *
     * @since 1.0.0
     */
    public function project_form($value)
    {
        $project = filter_input(
            INPUT_GET,
            'translationmanager_project_id',
            FILTER_SANITIZE_NUMBER_INT
        );

        $project = get_term($project, 'translationmanager_project');
        if (!$project instanceof WP_Term) {
            return $value;
        }

        $bind = (object)[
            'project' => $project,
            'nonce' => $this->nonce(),
        ];

        $closure = Closure::bind(
            function () {

                // @todo Make it a View.
                require Functions\get_template('/views/project/form-title-description.php');
            },
            $bind
        );

        $closure();

        return $value;
    }

    /**
     * Project Box in Edit Page
     *
     * @param string $value The views link. Untouched.
     *
     * @return string The untouched parameter
     * @todo  This is hooked in a filter, may create confusion about the value passed in.
     *        Is there a way to move into an action?
     *
     * @since 1.0.0
     */
    public function order_project_box_form($value)
    {
        $project = filter_input(
            INPUT_GET,
            'translationmanager_project_id',
            FILTER_SANITIZE_NUMBER_INT
        );

        $project = get_term($project, 'translationmanager_project');
        if (!$project instanceof WP_Term) {
            return $value;
        }

        (new OrderInfo($project->term_id))->render();

        return $value;
    }

    /**
     * Nonce
     *
     * @return \Brain\Nonces\WpNonce The nonce instance
     * @since 1.0.0
     */
    public function nonce()
    {
        return new WpNonce('update_project_info');
    }

    /**
     * Save Project Info based on request
     *
     * @return void
     * @since 1.0.0
     *
     * phpcs:disable WordPress.Security.NonceVerification
     */
    public function project_info_save()
    {
        // Check Action and auth.
        $action = sanitize_text_field(wp_unslash($_POST['action'] ?? ''));
        if ('translationmanager_project_info_save' !== $action) {
            return;
        }

        if (!$this->nonce()->validate() || !current_user_can('manage_options')) {
            wp_die('Cheating Uh?');
        }

        $project_id = (int)filter_input(
            INPUT_POST,
            'translationmanager_project_id',
            FILTER_SANITIZE_NUMBER_INT
        );
        $project = get_term($project_id, 'translationmanager_project');

        if ($project instanceof WP_Term) {
            $update = wp_update_term(
                $project->term_id,
                'translationmanager_project',
                [
                    'name' => sanitize_text_field(wp_unslash($_POST['tag-name'] ?? '')),
                    'description' => sanitize_text_field(wp_unslash($_POST['description'] ?? '')),
                ]
            );

            if (is_wp_error($update)) {
                TransientNoticeService::add_notice(
                    esc_html__(
                        'Something went wrong. Please try again.',
                        'translationmanager'
                    ),
                    'warning'
                );
            } else {
                TransientNoticeService::add_notice(
                    sprintf(
                        esc_html__(
                            'Project %s updated.',
                            'translationmanager'
                        ),
                        '<strong>' . get_term_field(
                            'name',
                            $project_id,
                            'translationmanager_project'
                        ) . '</strong>'
                    ),
                    'success'
                );
            }
        }

        if (!$project instanceof WP_Term) {
            TransientNoticeService::add_notice(
                esc_html__(
                    'Invalid project ID, the information could not be updated.',
                    'translationmanager'
                ),
                'warning'
            );
        }

        wp_safe_redirect(wp_get_referer());

        die;
    }

    /**
     * Register Taxonomy
     *
     * @return void
     * @since 1.0.0
     */
    public function register_taxonomy()
    {
        register_taxonomy(
            'translationmanager_project',
            'project_item',
            [
                'label' => esc_html__('Projects', 'translationmanager'),
                'labels' => [
                    'add_new_item' => esc_html__('Create new project', 'translationmanager'),
                ],
                'public' => true,
                'capabilities' => [
                    'manage_terms' => 'manage_options',
                    'edit_terms' => 'manage_options',
                    'delete_terms' => 'manage_options',
                    'assign_terms' => 'manage_options',
                ],
            ]
        );
    }

    /**
     * Register Status for Post
     *
     * @since 1.0.0
     */
    public static function register_post_status()
    {
    }

    /**
     * Edit Row Actions
     *
     * @param string[] $columns The columns contain the values for the row.
     * @param \WP_Term $term The term instance related to the columns.
     *
     * @return array The columns content
     * @since 1.0.0
     */
    public static function modify_row_actions($columns, $term)
    {
        $new_columns = [
            'delete' => $columns['delete'],
            'view' => sprintf(
                '<a href="%s">%s</a>',
                self::get_project_link($term->term_id),
                esc_html__('View', 'translationmanager')
            ),
        ];

        return $new_columns;
    }

    /**
     * Project Link
     *
     * @param int $project The project from which retrieve the term indetifier.
     *
     * @return string
     * @since 1.0.0
     */
    public static function get_project_link($project)
    {
        return get_admin_url(
            null,
            add_query_arg(
                [
                    'page' => 'translationmanager-project',
                    'translationmanager_project_id' => $project,
                    'post_type' => 'project_item',
                ],
                'admin.php'
            )
        );
    }

    /**
     * @param $columns
     *
     * @return array
     * @since 1.0.0
     */
    public static function modify_columns($columns)
    {
        unset($columns['cb']);
        unset($columns['slug']);
        unset($columns['posts']);

        // Add status ad second place.
        $columns = array_slice($columns, 0, 1)
            + [static::COL_STATUS => esc_html__('Status', 'translationmanager')]
            + array_slice($columns, 1);

        $columns[static::COL_ACTIONS] = '';

        return $columns;
    }

    /**
     * @param $value
     * @param $column_name
     * @param $term_id
     *
     * @return string
     * @since 1.0.0
     */
    public static function print_column($value, $column_name, $term_id)
    {
        switch ($column_name) {
            case static::COL_STATUS:
                if (!get_term_meta($term_id, '_translationmanager_order_id', true)) {
                    return esc_html__('New', 'translationmanager');
                }

                $orderInfo = new OrderInfo($term_id);

                return sprintf(
                    esc_html($orderInfo->get_status_label())
                );
                break;
            case static::COL_ACTIONS:
                return sprintf(
                    '<a href="%s" class="button">%s</a>',
                    self::get_project_link($term_id),
                    esc_html__('Show project', 'translationmanager')
                );
        }

        return $value;
    }

    /**
     * Edit Term Link for Project Taxonomy
     *
     * @param string $location The location link.
     * @param int $term_id The term id.
     * @param string $taxonomy The taxonomy name associated to the term.
     *
     * @return string The filtered location
     * @since 1.0.0
     */
    public function edit_term_link($location, $term_id, $taxonomy)
    {
        if ('translationmanager_project' === $taxonomy) {
            $location = self::get_project_link($term_id);
        }

        return $location;
    }
}
