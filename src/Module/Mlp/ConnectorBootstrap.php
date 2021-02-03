<?php

# -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp;

/**
 * Class ConnectorBootstrap
 *
 * The class it's used for Mlp version 2 and 3, basically it servers as bootstrap of the MLP
 * module by adding the proper filter and actions to Translation Manager.
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class ConnectorBootstrap
{
    /**
     * @var ConnectorFactory
     */
    private $connectorFactory;

    /**
     * connectorBootstrap constructor
     * @param ConnectorFactory $connectorFactory
     */
    public function __construct(ConnectorFactory $connectorFactory)
    {
        $this->connectorFactory = $connectorFactory;
    }

    /**
     * Action
     *
     * Actually the implementation for the Module.
     *
     * @param Adapter $adapter
     * @since 1.0.0
     */
    public function boot(Adapter $adapter)
    {
        $connector = $this->connectorFactory->create($adapter);

        // TM interface hooks to let it know about the environment.
        add_filter('translationmanager_current_language', [$connector, 'current_language']);
        add_filter('translationmanager_languages', [$connector, 'related_sites'], 10, 2);
        add_filter(
            'translation_manager_languages_by_site_id',
            [$connector, 'related_sites'],
            10,
            2
        );

        // Setup the translation workflow.
        add_action('translationmanager_outgoing_data', [$connector, 'prepare_outgoing']);
        add_filter('translationmanager_post_updater', [$connector, 'prepare_updater']);
    }
}
