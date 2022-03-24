<?php

/**
 * System Status
 *
 * @since   1.0.0
 * @package Translationmanager
 */

namespace Translationmanager\SystemStatus;

/**
 * Class Controller
 *
 * @since   1.0.0
 * @package Translationmanager
 */
class Controller
{
    /**
     * Information Class Names
     *
     * @since 1.0.0
     *
     * @var array The list of the information we want to show to the user.
     */
    private static $informations = [
        '\\Translationmanager\\SystemStatus\\Data\\Php',
        '\\Translationmanager\\SystemStatus\\Translationmanager',
        '\\Translationmanager\\SystemStatus\\Data\\WordPress',
        '\\Translationmanager\\SystemStatus\\Data\\Database',
        '\\Translationmanager\\SystemStatus\\Data\\Plugins',
    ];

    /**
     * Create the System Status instance with information
     *
     * @return Builder
     * @since 1.0.0
     */
    public function system_status()
    {
        return new Builder(self::$informations, 'table');
    }

    /**
     * Render System Status
     *
     * @return void
     * @since 1.0.0
     */
    public function render()
    {
        $this->system_status()
            ->build()
            ->render();
    }
}
