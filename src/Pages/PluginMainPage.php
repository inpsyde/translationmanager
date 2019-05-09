<?php

/**
 * Plugin Main Page
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */

namespace Translationmanager\Pages;

use Translationmanager\Plugin;

/**
 * Class PluginMainPage
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */
class PluginMainPage implements Pageable
{
    /**
     * Page Slug
     *
     * @since 1.0.0
     *
     * @var string The page slug
     */
    const SLUG = 'translationmanager';

    /**
     * @var string
     */
    const MENU_POSITION = 100;

    /**
     * Plugin
     *
     * @since 1.0.0
     *
     * @var \Translationmanager\Plugin The instance of the plugin
     */
    private $plugin;

    /**
     * PluginMainPage constructor
     *
     * @param \Translationmanager\Plugin $plugin The instance of the plugin.
     *
     * @since 1.0.0
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @inheritdoc
     */
    public function add_page()
    {
        add_menu_page(
            esc_html__('Translations', 'translationmanager'),
            esc_html__('Translations', 'translationmanager'),
            'manage_options',
            self::SLUG,
            '__return_false',
            $this->plugin->url('/resources/img/tm-icon-bw.png'),
            self::MENU_POSITION
        );
    }

    /**
     * Fix incongruences because of custom hardcoded urls and menu items
     *
     * @return void
     */
    public function make_menu_items_coherent()
    {
        $this->apply_current_menu_classes();
        $this->correct_submenu_url();
    }

    /**
     * @inheritdoc
     */
    public function render_template()
    {
        // Nothing here for now.
    }

    /**
     * @return void
     */
    private function correct_submenu_url()
    {
        global $submenu;

        // User may not allowed, so the index may not exists.
        if (isset($submenu['translationmanager'])) {
            $submenu['translationmanager'][0][2] = $this->edit_project_items_url();
        }
    }

    /**
     * @return void
     */
    private function apply_current_menu_classes()
    {
        add_filter(
            'parent_file',
            function ($parent_file) {

                $screen = get_current_screen();

                if ('edit-translationmanager_project' === $screen->id) {
                    $parent_file = 'translationmanager';
                }

                return $parent_file;
            },
            PHP_INT_MAX
        );
        add_filter(
            'submenu_file',
            function ($submenu_file) {

                $screen = get_current_screen();

                if ('edit-translationmanager_project' === $screen->id) {
                    $submenu_file = $this->edit_project_items_url();
                }

                return $submenu_file;
            }
        );
    }

    /**
     * @return string The custom url for the menu items
     */
    private function edit_project_items_url()
    {
        return add_query_arg(
            [
                'taxonomy' => 'translationmanager_project',
                'post_type' => 'project_item',
            ],
            admin_url('/edit-tags.php')
        );
    }
}
