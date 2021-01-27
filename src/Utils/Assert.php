<?php

# -*- coding: utf-8 -*-

namespace Translationmanager\Utils;

use Webmozart\Assert\Assert as WebMozartAssert;

/**
 * Class Assert
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class Assert extends WebMozartAssert
{
    /**
     * Assert Given Value Contains Semantic Version Number
     *
     * @param $version
     * @param $message
     */
    public static function semVersion($version, $message = '')
    {
        $pattern = '~^(?P<numbers>(?:[0-9]+)+(?:[0-9\.]+)?)+(?P<anything>.*?)?$~';
        $matched = preg_match($pattern, $version, $matches);

        if (!$matched) {
            self::reportInvalidArgument($message ?: 'Invalid Semantic Version Value.');
        }
    }
}
