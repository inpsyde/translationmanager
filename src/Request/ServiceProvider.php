<?php

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Request
 */

namespace Translationmanager\Request;

use Pimple\Container;
use Translationmanager\Service\BootstrappableServiceProvider;
use Translationmanager\Request\Api;
use Translationmanager\Auth;
use Brain\Nonces;
use Translationmanager\ProjectHandler;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Request
 */
class ServiceProvider implements BootstrappableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container['Request.AddTranslation'] = function () {

            return new Api\AddTranslation(
                new Auth\Validator(),
                new Nonces\WpNonce('add_translation'),
                new ProjectHandler()
            );
        };
        $container['Request.OrderProject'] = function () {

            return new Api\OrderProject(
                new Auth\Validator(),
                new Nonces\WpNonce('order_project')
            );
        };
        $container['Request.UpdateProjectOrderStatus'] = function () {

            return new Api\UpdateProjectOrderStatus(
                new Auth\Validator(),
                new Nonces\WpNonce('update_project')
            );
        };
        $container['Request.ImportProject'] = function () {

            return new Api\ImportProject(
                new Auth\Validator(),
                new Nonces\WpNonce('import_project')
            );
        };
    }

    /**
     * @inheritdoc
     */
    public function boot(Container $container)
    {
        // Add Translation.
        add_action('load-edit.php', [$container['Request.AddTranslation'], 'handle']);
        add_action('load-post.php', [$container['Request.AddTranslation'], 'handle']);

        // Import Translation.
        add_action(
            'admin_post_translationmanager_import_project',
            [$container['Request.ImportProject'], 'handle']
        );

        // Order Project.
        add_action(
            'admin_post_translationmanager_order_project',
            [
                $container['Request.OrderProject'],
                'handle',
            ]
        );

        // Update Project Order Status.
        add_action(
            'admin_post_translationmanager_update_project',
            [$container['Request.UpdateProjectOrderStatus'], 'handle']
        );
    }
}
