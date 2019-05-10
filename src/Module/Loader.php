<?php
/**
 * Class Loader
 *
 * @since   1.0.0
 * @package Translationmanager\Module
 */

namespace Translationmanager\Module;

use ReflectionClass;
use ReflectionException;
use Translationmanager\Module\Mlp;
use Translationmanager\Module\YoastSeo;

/**
 * Class Loader
 *
 * @since   1.0.0
 * @package Translationmanager\Module
 */
class Loader
{
    /**
     * Installed Plugins List
     *
     * @since 1.0.0
     *
     * @var array The list of the installed and active plugins
     */
    private $installedPlugins;

    /**
     * List of Integrations
     *
     * @var array List of integrations
     */
    private $integrations = [];

    /**
     * Modules
     *
     * @since 1.0.0
     *
     * @var array List of modules we want to integrate if the relative plugins are active
     */
    private static $modules = [
        'wp-seo' => YoastSeo\Integrator::class,
    ];

    /**
     * MultilingualPress Modules
     *
     * @var array
     */
    private static $multilingualPressModules = [
        'multilingualpress' => Mlp\Integrator::class,
        'multilingual-press' => Mlp\Integrator::class,
    ];

    /**
     * Loader constructor
     *
     * @param array $installedPlugins The list of the installed and active plugins.
     * @since 1.0.0
     */
    public function __construct(array $installedPlugins)
    {
        $this->installedPlugins = $installedPlugins;
    }

    /**
     * Register the integrations instances
     *
     * @return $this For concatenation
     * @since 1.0.0
     */
    public function register_integrations()
    {
        $this->installed_plugins_as_assoc_list();

        $available_modules = $this->available_modules();
        $modules = array_merge(self::$modules, self::$multilingualPressModules);

        // Are there modules installed?
        if (!$available_modules) {
            return $this;
        }

        foreach ($available_modules as $module) {
            if (!class_exists($modules[$module])) {
                continue;
            }

            try {
                $class = $modules[$module];
                $classReflection = new ReflectionClass($class);
                $isIntegrable = $classReflection->implementsInterface(Integrable::class);
            } catch (ReflectionException $exc) {
                continue;
            }

            if (!$isIntegrable) {
                continue;
            }

            $pluginData = get_file_data(
                $this->installedPlugins[$module],
                [
                    'version' => 'Version',
                ]
            );

            // Only for MultilingualPress until the end of support for MLP2
            if (in_array($module, self::$multilingualPressModules, true)) {
                $this->integrations[] = new $modules[$module]($pluginData['version']);
                continue;
            }

            $this->integrations[] = new $modules[$module]();
        }

        return $this;
    }

    /**
     * Integrate every module
     *
     * @return $this For concatenation
     * @since 1.0.0
     */
    public function integrate()
    {
        foreach ($this->integrations as $integration) {
            $integration->integrate();
        }

        return $this;
    }

    /**
     * From index to assoc installed plugins list
     *
     * @return void
     * @since 1.0.0
     */
    private function installed_plugins_as_assoc_list()
    {
        $list = [];

        foreach ($this->installedPlugins as $plugin) {
            $basename = basename($plugin, '.php');

            $list[$basename] = $plugin;
        }

        $this->installedPlugins = $list;
    }

    /**
     * Has Modules
     *
     * @return array The existing modules installed or empty array if no modules are available
     * @since 1.0.0
     */
    private function available_modules()
    {
        return array_intersect(
            array_keys($this->installedPlugins),
            array_keys(self::$modules),
            array_keys(self::$multilingualPressModules)
        );

        // TODO Add a filter in order to allow third party devs to inject their modules
    }
}
