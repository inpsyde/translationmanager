<?php

// -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Utils;

use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Utils\NetworkState;
use WP_Post;

/**
 * Class ImageCopier
 *
 * @since   1.0.0
 * @package Translationmanager\Module\Mlp\Utils
 */
class ImageCopier
{
    /**
     * @var \Translationmanager\Module\Mlp\Adapter
     */
    private $adapter;

    /**
     * ImageCopier constructor
     *
     * @param \Translationmanager\Module\Mlp\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param int $source_attachment_id
     * @param int $source_site_id
     * @param null $target_site_id
     *
     * @return int
     */
    public function copy_image(
        $source_attachment_id,
        $source_site_id = null,
        $target_site_id = null
    ) {

        if (!is_numeric($source_attachment_id) || !(int)$source_attachment_id) {
            return 0;
        }

        $source_site_id or $source_site_id = get_current_blog_id();
        $target_site_id or $target_site_id = get_current_blog_id();

        if ($source_site_id === $target_site_id) {
            return $source_attachment_id;
        }

        list($attachment, $source_file) = $this->source_image_data(
            $source_attachment_id,
            $source_site_id
        );

        if (!$attachment || !$source_file) {
            return 0;
        }

        $networkState = NetworkState::create();
        $networkState->switch_to($target_site_id);

        $linked_file = $linked = null;
        $linked_attachments = $this->adapter->relations($source_site_id, $source_attachment_id);
        if (!empty($linked_attachments[$target_site_id])) {
            $linked = $linked_attachments[$target_site_id];
            $linked_file = get_attached_file($linked_attachments[$target_site_id]);
        }

        if ($linked && basename($linked_file) === basename($source_file)) {
            $networkState->restore();

            return (int)$linked;
        }

        $filepath = $this->copy_source_file($source_file);
        $insert_id = $filepath ? $this->insert_attachment($attachment, $filepath) : 0;

        if ($insert_id) {
            $this->adapter->set_relation(
                $source_site_id,
                $target_site_id,
                $source_attachment_id,
                $insert_id
            );
        }

        $networkState->restore();

        return $insert_id;
    }

    /**
     * @param int $source_attachment_id
     * @param int $source_site_id
     *
     * @return array
     */
    private function source_image_data($source_attachment_id, $source_site_id)
    {
        $switched = $source_site_id !== get_current_blog_id();

        $switched and switch_to_blog($source_site_id);

        $attachment = get_post($source_attachment_id);
        if (!$attachment || $attachment->post_type !== 'attachment' || !wp_attachment_is_image($attachment)) {
            $switched and restore_current_blog();

            return [null, ''];
        }

        $source_file = get_attached_file($source_attachment_id, true);

        $switched and restore_current_blog();

        if (!file_exists($source_file)) {
            return [$attachment, ''];
        }

        return [$attachment, $source_file];
    }

    /**
     * @param string $source_file
     *
     * @return string
     */
    private function copy_source_file($source_file)
    {
        $uploads = wp_upload_dir();

        if (!empty($uploads['error']) || empty($uploads['path']) || empty($uploads['url'])) {
            return '';
        }

        $filepath = trailingslashit($uploads['path']) . basename($source_file);

        return copy($source_file, $filepath) ? $filepath : '';
    }

    /**
     * @param \WP_Post $attachment
     * @param string $filepath
     *
     * @return int
     */
    private function insert_attachment(WP_Post $attachment, $filepath)
    {
        $new_attachment = array_diff_key(
            $attachment->to_array(),
            [
                'ID' => '',
                'guid' => '',
                'ancestors' => '',
                'page_template' => '',
                'post_category' => '',
                'tags_input' => '',
                'post_modified_gmt' => '',
                'filter' => '',
            ]
        );

        $attachment_id = wp_insert_attachment($new_attachment, $filepath);

        if (!$attachment_id || is_wp_error($attachment_id)) {
            return 0;
        }

        require_once ABSPATH . 'wp-admin/includes/image.php';
        wp_update_attachment_metadata(
            $attachment_id,
            wp_generate_attachment_metadata($attachment_id, $filepath)
        );

        return $attachment_id;
    }
}
