<?php

# -*- coding: utf-8 -*-

namespace Translationmanager\Exception;

/**
 * Class InvalidPostException
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class UnexpectedEntityException extends EntityException
{
    /**
     * Create a new Exception for Unexpected Post Value Retrieved
     *
     * @param string $postType
     * @param string $message
     * @return UnexpectedEntityException
     */
    public static function forPostValue($postType, $message)
    {
        $message = $message ?: "Unexpected post value retrieved for post type {$postType}";

        return new self($message);
    }

    /**
     * Create a new Exception for Unexpected Term Value Retrieved
     *
     * @param mixed $value
     * @param string $message
     * @return UnexpectedEntityException
     */
    public static function forTermValue($value, $message)
    {
        $valueType = is_object($value) ? get_class($value) : gettype($value);
        $message = $message ?: "Unexpected term value retrieved. Should be WP_Term but got {$valueType}";

        return new self($message);
    }
}
