<?php

/**
 * Page Projects
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */

namespace Translationmanager\Pages;

use Translationmanager\TableList\ProjectItem;

/**
 * Class Projects
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */
class Project implements Pageable
{
    /**
     * Page Slug
     *
     * @since 1.0.0
     *
     * @var string The page slug
     */
    const SLUG = 'translationmanager-project';

    /**
     * The Page Title
     *
     * @since 1.0.0
     *
     * @var string The page title
     */
    private $page_title;

    /**
     * Project constructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->page_title = __('Project', 'translationmanager');
    }

    /**
     * Reintroduce Page Title in Header
     *
     * Since we are adding an hidden page the `<title>` tag will miss the page title.
     *
     * @param string $admin_title The page title in the admin.
     *
     * @return string The page title
     * @since 1.0.0
     */
    public function reintroduce_page_title_in_header($admin_title)
    {
        if ($this->is_project_item_cpt()) {
            $admin_title = $this->page_title . ' ' . $admin_title;
        }

        return $admin_title;
    }

    /**
     * @inheritdoc
     */
    public function add_page()
    {
        add_submenu_page(
            null,
            esc_html__('Project', 'translationmanager'),
            esc_html__('Project', 'translationmanager'),
            'manage_options',
            self::SLUG,
            [$this, 'render_template']
        );

        add_submenu_page(
            'translationmanager',
            esc_html__('Projects', 'translationmanager'),
            esc_html__('Projects', 'translationmanager'),
            'manage_options',
            'translationmanager-project',
            '__return_false'
        );
    }

    /**
     * @inheritdoc
     */
    public function render_template()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__(
                'You do not have sufficient permissions to access this page.',
                'translationmanager'
            ));
        }

        $this->requires();

        $bind = (object)[
            'page_title' => $this->page_title,
            'wp_list_table' => new ProjectItem(),
        ];

        require_once \Translationmanager\Functions\get_template('/views/project/page-layout.php');

        unset($bind);
    }

    /**
     * Requires Additional Stuffs
     *
     * @return void
     * @since 1.0.0
     */
    private function requires()
    {
        require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
    }

    /**
     * Check if the current screen is the post type
     *
     * @return bool True if the current screen is for the post type, false otherwise
     * @since 1.0.0
     */
    private function is_project_item_cpt()
    {
        return get_current_screen() && 'project_item' === get_current_screen()->post_type;
    }
}
