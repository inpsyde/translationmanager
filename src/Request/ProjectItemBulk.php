<?php

/**
 * Class ProjectItemBulk
 *
 * @since   1.0.0
 * @package Translationmanager\Request
 */

namespace Translationmanager\Request;

use Translationmanager\Notice\StandardNotice;

/**
 * Class ProjectItemBulk
 *
 * @since   1.0.0
 * @package Translationmanager\Request
 */
class ProjectItemBulk implements RequestHandleable
{
    /**
     * User Capability
     *
     * @since 1.0.0
     *
     * @var string The capability needed by the user to be able to perform the request
     */
    private static $capability = 'manage_options';

    /**
     * @inheritdoc
     *
     * phpcs:disable WordPress.Security.NonceVerification
     */
    public function handle()
    {
        if (!$this->is_valid_request()) {
            return;
        }

        $action = sanitize_text_field(wp_unslash($_POST['action'] ?? ''));

        switch ($action) {
            case 'trash':
                $this->trash_posts();
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function is_valid_request()
    {
        if (!isset($_POST['action'])) { // phpcs:ignore
            return false;
        }

        return current_user_can(self::$capability) && check_admin_referer('bulk-posts');
    }

    /**
     * @inheritdoc
     */
    public function request_data()
    {
        return filter_input_array(
            INPUT_POST,
            [
                'translationmanager_project_id' => FILTER_SANITIZE_NUMBER_INT,
                'post_ID' => FILTER_SANITIZE_NUMBER_INT,
                'translationmanager_language' => [
                    'filter' => FILTER_UNSAFE_RAW,
                    'flags' => FILTER_FORCE_ARRAY,
                ],
            ]
        );
    }

    /**
     * Trash Requested Posts
     *
     * @return void
     * @since 1.0.0
     */
    private function trash_posts()
    {
        $posts = filter_input(INPUT_POST, 'post', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
        if (!$posts) {
            return;
        }

        $response = [];
        foreach ($posts as $post) {
            $response[] = (bool)wp_trash_post($post);
        }

        $success = array_filter($response);

        $notice = [
            'message' => esc_html__('Items correctly removed from the project.', 'translationmanager'),
            'severity' => 'success',
        ];

        if (count($success) !== count($response)) {
            $notice = [
                'message' => esc_html__(
                    'Some items cannot be removed correctly from the project. Try again or remove them manually.',
                    'translationmanager'
                ),
                'severity' => 'error',
            ];
        }

        (new StandardNotice($notice['message'], $notice['severity']))->show();
    }
}
