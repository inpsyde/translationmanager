<?php

/**
 * Requirements
 *
 * @since   1.0.0
 * @package Translationmanager
 */

namespace Translationmanager;

/**
 * Class Requirements
 *
 * @since   1.0.0
 * @package Translationmanager
 */
class Requirements
{
    /**
     * PHP Min Version
     *
     * @since 1.0.0
     *
     * @var string The minimum required php version
     */
    const PHP_MIN_VERSION = '5.6';

    /**
     * PHP Current Version
     *
     * @since 1.0.0
     *
     * @var string The current php version
     */
    const PHP_CURR_VERSION = PHP_VERSION;

    /**
     * Check PHP Version
     *
     * @return bool True if the current version is less than the minimum required version
     * @since 1.0.0
     */
    public function is_php_version_ok()
    {
        $php_version = false;
        if (preg_match('!^([0-9]+\.([0-9]+\.)?[0-9]+)!', self::PHP_MIN_VERSION, $m)) {
            $php_version = $m[1];
        }

        if (!$php_version) {
            return false;
        }

        return version_compare($php_version, self::PHP_CURR_VERSION, '<=');
    }
}
