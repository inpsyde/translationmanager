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
    const POST_DATA_NAMESPACE = 'POST';

    /**
     * @var array
     */
    private $modulesProvider;

    /**
     * Loader constructor
     *
     * @param ModulesProvider $modulesProvider
     */
    public function __construct(ModulesProvider $modulesProvider)
    {
        $this->modulesProvider = $modulesProvider;
    }

    /**
     * Register the integrations instances
     */
    public function integrate()
    {
        /** @var Integrable $moduleInstance */
        foreach ($this->modulesProvider as $pluginFilePath => $moduleInstance) {
            if (!$moduleInstance instanceof Integrable) {
                continue;
            }

            $moduleInstance->integrate();
        }
    }
}
