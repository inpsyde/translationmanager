<?php

# -*- coding: utf-8 -*-

namespace Translationmanager\Module;

use ArrayIterator;
use IteratorAggregate;
use Translationmanager\Utils\Assert;
use Traversable;

/**
 * Class ModuleProvider
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class ModulesProvider implements IteratorAggregate
{
    const FILTER_AVAILABLE_MODULES = 'translationmanager.available_modules';

    /**
     * @var array
     */
    private $modules;

    /**
     * ModulesProvider constructor
     * @param array $modules
     */
    public function __construct(array $modules)
    {
        Assert::allIsInstanceOf($modules, Integrable::class);

        $this->modules = $modules;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        $availableModules = [];

        /**
         * Filter Modules
         *
         * @param array $modules All of the available modules
         */
        $modules = (array)apply_filters(self::FILTER_AVAILABLE_MODULES, $this->modules);

        $plugins = $this->plugins();
        $allowedModules = $this->allowedModules($plugins);

        foreach ($allowedModules as $name) {
            $pluginPath = isset($plugins[$name]) ? $plugins[$name] : '';
            $moduleInstance = isset($modules[$name]) ? $modules[$name] : '';

            if ($pluginPath && $moduleInstance) {
                $availableModules[$pluginPath] = $moduleInstance;
            }
        }

        return new ArrayIterator($availableModules);
    }

    /**
     * From index to assoc installed plugins list
     *
     * @return array Associative array where key is the plugin base name and the value it's the file path
     * @internal
     */
    protected function plugins()
    {
        $list = [];

        $activePlugins = get_option('active_plugins', []);

        if (function_exists('wp_get_active_network_plugins')) {
            $activePlugins = array_merge($activePlugins, wp_get_active_network_plugins());
        }

        // Consistence because `wp_get_active_network_plugins` returns different list than get_option.
        foreach ($activePlugins as $plugin) {
            $basename = basename($plugin, '.php');
            $directory = basename(dirname($plugin));
            $list[$basename] = "{$directory}/{$basename}.php";
        }

        return $list;
    }

    /**
     * Has Modules
     *
     * @param array $plugins
     * @return array The existing modules installed or empty array if no modules are available
     * @internal
     */
    protected function allowedModules(array $plugins)
    {
        return array_intersect(
            array_keys($plugins),
            array_keys($this->modules)
        );
    }
}
