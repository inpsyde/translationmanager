<?php

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */

namespace Translationmanager\Pages;

use Pimple\Container;
use Translationmanager\Service\BootstrappableServiceProvider;
use Translationmanager\Setting\PluginSettings;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */
class ServiceProvider implements BootstrappableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container['Page.Project'] = function () {

            return new \Translationmanager\Pages\Project();
        };
        $container['Page.PluginMainPage'] = function (Container $container) {

            return new \Translationmanager\Pages\PluginMainPage($container['translationmanager.plugin']);
        };
        $container['Page.PageOptions'] = function (Container $container) {

            return new \Translationmanager\Pages\PageOptions($container['translationmanager.plugin']);
        };
    }

    /**
     * @inheritdoc
     */
    public function boot(Container $container)
    {
        // Page Project.
        add_action('admin_menu', [$container['Page.Project'], 'add_page']);
        add_action(
            'admin_title',
            [
                $container['Page.Project'],
                'reintroduce_page_title_in_header',
            ]
        );

        // Main Page.
        add_action('admin_menu', [$container['Page.PluginMainPage'], 'add_page']);
        add_action(
            'admin_menu',
            [$container['Page.PluginMainPage'], 'make_menu_items_coherent']
        );

        // Page Options.
        add_action('admin_menu', [$container['Page.PageOptions'], 'add_page']);
        add_action('admin_head', [$container['Page.PageOptions'], 'enqueue_style']);
        add_action('admin_head', [$container['Page.PageOptions'], 'enqueue_script']);
        add_action(
            'admin_init',
            [
                $container['Page.PageOptions'],
                'handle_support_request_form',
            ]
        );

        add_filter(
            'option_page_capability_' . PluginSettings::OPTION_GROUP,
            [$container['Page.PageOptions'], 'filter_capabilities']
        );
    }
}
