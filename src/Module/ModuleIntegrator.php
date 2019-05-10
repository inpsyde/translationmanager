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
use Translationmanager\Module\Processor\ProcessorBusFactory;
use Translationmanager\Plugin;

/**
 * Class Loader
 *
 * @package Translationmanager\Module
 */
class ModuleIntegrator
{
    /**
     * @var array
     */
    private $modulesProvider;

    /**
     * @var Plugin
     */
    private $plugin;

    /**
     * @var ProcessorBusFactory
     */
    private $processorBusFactory;

    /**
     * Loader constructor
     *
     * @param Plugin $plugin
     * @param ModulesProvider $modulesProvider
     * @param ProcessorBusFactory $processorBusFactory
     */
    public function __construct(
        Plugin $plugin,
        ModulesProvider $modulesProvider,
        ProcessorBusFactory $processorBusFactory
    ) {

        $this->modulesProvider = $modulesProvider;
        $this->plugin = $plugin;
        $this->processorBusFactory = $processorBusFactory;
    }

    /**
     * Register the integrations instances
     */
    public function integrate()
    {
        foreach ($this->modulesProvider as $pluginFilePath => $moduleClassName) {
            $mainPluginDir = dirname($this->plugin->dir());
            $moduleFilePath = "{$mainPluginDir}/{$pluginFilePath}";
            if (!$moduleFilePath || !class_exists($moduleClassName)) {
                continue;
            }

            try {
                $classReflection = new ReflectionClass($moduleClassName);
                $isIntegrable = $classReflection->implementsInterface(Integrable::class);
            } catch (ReflectionException $exc) {
                continue;
            }

            if (!$isIntegrable) {
                continue;
            }

            $moduleClassName::integrate($this->processorBusFactory, $moduleFilePath);
        }
    }
}
