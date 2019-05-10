<?php # -*- coding: utf-8 -*-

namespace Translationmanager;

use ArrayAccess;
use JsonSerializable;
use WP_Post;

/**
 * Class Translatable
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
interface Translatable extends ArrayAccess, JsonSerializable
{
    const META_KEY = '__meta';
    const VALUES_KEY = '__values';
    const SOURCE_POST_ID_KEY = 'source_post_id';
    const SOURCE_POST_KEY = 'source_post_obj';
    const SOURCE_SITE_KEY = 'source_site_id';
    const TARGET_POST_KEY = 'target_post_id';
    const TARGET_SITE_KEY = 'target_site_id';
    const TARGET_LANG_KEY = 'target_language';
    const INCOMING = 'incoming';
    const OUTGOING = 'outgoing';

    /**
     * @return bool
     */
    public function is_incoming();

    /**
     * @return bool
     */
    public function is_outgoing();

    /**
     * @return bool
     */
    public function is_valid();

    /**
     * @return int
     */
    public function source_post_id();

    /**
     * @return int
     */
    public function source_site_id();

    /**
     * @return WP_Post|null
     */
    public function source_post();

    /**
     * @return int
     */
    public function target_site_id();

    /**
     * @return int
     */
    public function target_language();

    /**
     * @param string $key
     * @param string $namespace
     *
     * @return bool
     */
    public function has_value($key, $namespace = '');

    /**
     * @param string $key
     * @param string $namespace
     *
     * @return mixed
     */
    public function get_value($key, $namespace = '');

    /**
     * @param string $key
     * @param mixed $value
     * @param string $namespace
     */
    public function set_value($key, $value, $namespace = '');

    /**
     * @param string $key
     * @param string $namespace
     */
    public function remove_value($key, $namespace = '');

    /**
     * @param string $key
     * @param string $namespace
     *
     * @return bool
     */
    public function has_meta($key, $namespace = '');

    /**
     * @param string $key
     * @param string $namespace
     *
     * @return mixed
     */
    public function get_meta($key, $namespace = '');

    /**
     * @param string $key
     * @param mixed $value
     * @param string $namespace
     */
    public function set_meta($key, $value, $namespace = '');

    /**
     * @param string $key
     * @param string $namespace
     */
    public function remove_meta($key, $namespace = '');

    /**
     * @return array
     */
    public function to_array();
}
