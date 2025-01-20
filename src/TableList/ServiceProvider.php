<?php

/**
 * ServiceProvider
 *
 * @author    Guido Scialfa <dev@guidoscialfa.com>
 * @package   Translation Manager
 * @copyright Copyright (c) 2018, Guido Scialfa
 *
 * Copyright (C) 2018 Guido Scialfa <dev@guidoscialfa.com>
 */

namespace Translationmanager\TableList;

use Pimple\Container;
use Translationmanager\Service\BootstrappableServiceProvider;

use function Translationmanager\Functions\get_supported_post_types;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\TableList
 */
class ServiceProvider implements BootstrappableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container['TableList.RestrictManagePosts'] = function (Container $container) {

            return new RestrictManagePosts($container['translationmanager.plugin']);
        };
    }

    /**
     * @inheritdoc
     */
    public function boot(Container $container)
    {
        // Restrict Content Manage Posts.
        add_action(
            'manage_posts_extra_tablenav',
            [$container['TableList.RestrictManagePosts'], 'restrict_manage_posts']
        );
        add_action(
            'admin_head',
            [$container['TableList.RestrictManagePosts'], 'enqueue_styles']
        );
        add_action(
            'admin_head',
            [$container['TableList.RestrictManagePosts'], 'enqueue_scripts']
        );
        add_action(
            'manage_posts_extra_tablenav',
            [$container['TableList.RestrictManagePosts'], 'restrict_manage_posts']
        );
        add_action('init', function () use ($container) {
            foreach (get_supported_post_types() as $postTypeName) {
                add_filter(
                    "bulk_actions-edit-{$postTypeName}",
                    [$container['TableList.RestrictManagePosts'], 'filter_bulk_action_list']
                );
            }
        }, 11);
    }
}
