<?php

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Project
 */

namespace Translationmanager\Project;

use Pimple\Container;
use Translationmanager\Service\BootstrappableServiceProvider;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Project
 */
class ServiceProvider implements BootstrappableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container['Project.Taxonomy'] = function () {

            return new Taxonomy();
        };
    }

    /**
     * @inheritdoc
     */
    public function boot(Container $container)
    {
        // Taxonomy.
        add_action(
            'init',
            [$container['Project.Taxonomy'], 'register_taxonomy']
        );
        add_action(
            'manage_translationmanager_project_custom_column',
            [$container['Project.Taxonomy'], 'print_column'],
            10,
            3
        );
        add_action(
            'admin_post_translationmanager_project_info_save',
            [$container['Project.Taxonomy'], 'project_info_save']
        );
        add_action(
            'translationmanager_project_item_table_views',
            [$container['Project.Taxonomy'], 'project_form']
        );
        add_action(
            'translationmanager_project_item_table_views',
            [$container['Project.Taxonomy'], 'order_project_box_form']
        );

        add_filter(
            'manage_edit-translationmanager_project_columns',
            [$container['Project.Taxonomy'], 'modify_columns']
        );
        add_filter(
            'translationmanager_project_row_actions',
            [$container['Project.Taxonomy'], 'modify_row_actions'],
            10,
            2
        );
        add_filter(
            'get_edit_term_link',
            [$container['Project.Taxonomy'], 'edit_term_link'],
            10,
            3
        );
    }
}
