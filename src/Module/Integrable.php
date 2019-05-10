<?php
/**
 * Class Integrable
 *
 * @since   1.0.0
 * @package Translationmanager\Module
 */

namespace Translationmanager\Module;

use Translationmanager\Module\Processor\ProcessorBusFactory;

/**
 * Class Integrable
 *
 * @package Translationmanager\Module
 */
interface Integrable
{
    /**
     * Integrate Module
     *
     * @param ProcessorBusFactory $processorBusFactory
     * @param string $pluginPath
     * @return void
     */
    public static function integrate(ProcessorBusFactory $processorBusFactory, $pluginPath);
}
