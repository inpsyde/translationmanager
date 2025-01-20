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

namespace Translationmanager\Xliff;

use Pimple\Container;
use Translationmanager\Service\BootstrappableServiceProvider;
use Translationmanager\Module\ACF\Acf;
use Translationmanager\Auth\Validator;

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
        $container['tm/Xliff/SettingsView'] = function () {
            return new SettingsView();
        };

        $container['tm/ACF/Acf'] = function () {
            return new Acf();
        };

        $container['tm/Xliff/XliffElementHelper'] = function () {
            return new XliffElementHelper();
        };

        $container['tm/Xliff/Xliff'] = function (Container $container) {
            return new Xliff(
                $container['tm/ACF/Acf'],
                $container['tm/Xliff/XliffElementHelper'],
                $container['translationmanager.plugin']
            );
        };

        $container['tm/Xliff/Export'] = function (Container $container) {
            return new Export($container['tm/Xliff/Xliff']);
        };

        $container['tm/Xliff/Import'] = function (Container $container) {
            return new Import($container['tm/Xliff/Xliff'], new Validator());
        };
    }

    /**
     * @inheritdoc
     */
    public function boot(Container $container)
    {
        add_action('after_filter_options', [$container['tm/Xliff/SettingsView'], 'renderExportButton']);
        add_action('after_order_info', [$container['tm/Xliff/SettingsView'], 'renderImportOptions']);

        add_action(
            'admin_head',
            function () use ($container) {
                $plugin = $container['translationmanager.plugin'];
                $projectId = filter_input(
                    INPUT_GET,
                    'translationmanager_project_id',
                    FILTER_SANITIZE_NUMBER_INT
                );

                wp_register_script(
                    'translationmanager-export-XLIFF',
                    $plugin->url('/resources/js/exportXLIFF.js'),
                    [],
                    filemtime($plugin->dir('/resources/js/exportXLIFF.js')),
                    true
                );
                wp_localize_script(
                    'translationmanager-export-XLIFF',
                    'projectInfo',
                    ['projectId' => $projectId]
                );
                wp_enqueue_script('translationmanager-export-XLIFF');

                wp_register_script(
                    'translationmanager-import-XLIFF',
                    $plugin->url('/resources/js/importXLIFF.js'),
                    [],
                    filemtime($plugin->dir('/resources/js/importXLIFF.js')),
                    true
                );
                wp_localize_script(
                    'translationmanager-import-XLIFF',
                    'projectInfo',
                    ['projectId' => $projectId]
                );
                wp_enqueue_script('translationmanager-import-XLIFF');
            }
        );

        add_action(
            'wp_ajax_' . Export::ACTION,
            [$container['tm/Xliff/Export'], 'handle']
        );

        add_action(
            'wp_ajax_' . Import::ACTION,
            [$container['tm/Xliff/Import'], 'handle']
        );
    }
}
