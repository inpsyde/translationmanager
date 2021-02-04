<?php

// -*- coding: utf-8 -*-

namespace Translationmanager\Utils;

/**
 * Storage for the (switched) state of the network.
 */
class NetworkState
{
    /**
     * @var int
     */
    private $site_id;

    /**
     * @var int[]
     */
    private $stack;

    /**
     * Returns a new instance for the global site ID and switched stack.
     *
     * @return static
     */
    public static function create()
    {
        $state = new static();

        $state->site_id = (int)get_current_blog_id();
        $state->stack = isset($GLOBALS['_wp_switched_stack']) ? (array)$GLOBALS['_wp_switched_stack'] : [];

        return $state;
    }

    /**
     * @param int $site
     *
     * @return bool
     */
    public function switch_to($site)
    {
        if (!is_numeric($site) || (int)$site === get_current_blog_id()) {
            return false;
        }

        return switch_to_blog($site);
    }

    /**
     * Restores the stored site state.
     *
     * @return int The current site ID.
     */
    public function restore()
    {
        $current = (int)get_current_blog_id();
        $stack = isset($GLOBALS['_wp_switched_stack'])
            ? (array)$GLOBALS['_wp_switched_stack']
            : [];

        if ($current === $this->site_id && $stack === $this->stack) {
            return $current;
        }

        switch_to_blog($this->site_id);

        $GLOBALS['_wp_switched_stack'] = $this->stack;
        $GLOBALS['switched'] = !empty($this->stack);

        return $current;
    }

    private function __construct()
    {
    }
}
