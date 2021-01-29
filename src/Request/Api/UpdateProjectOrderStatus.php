<?php

namespace Translationmanager\Request\Api;

use Brain\Nonces\NonceInterface;
use DateTime;
use Exception;
use Translationmanager\Request\RequestHandleable;
use Translationmanager\Auth\Authable;
use Translationmanager\Notice\TransientNoticeService;
use Translationmanager\Utils\TimeZone;
use WP_Term;

use function Translationmanager\Functions\project_global_status;
use function Translationmanager\Functions\redirect_admin_page_network;
use function Translationmanager\Functions\set_unique_term_meta;

/**
 * Class UpdateProjectOrderStatus
 *
 * @since   1.0.0
 * @package Translationmanager\Request
 */
class UpdateProjectOrderStatus implements RequestHandleable
{
    /**
     * Auth
     *
     * @since 1.0.0
     *
     * @var Authable The instance to use to verify the request
     */
    private $auth;

    /**
     * Nonce
     *
     * @since 1.0.0
     *
     * @var NonceInterface The instance to use to verify the request
     */
    private $nonce;

    /**
     * User Capability
     *
     * @since 1.0.0
     *
     * @var string The capability needed by the user to be able to perform the request
     */
    private static $capability = 'manage_options';

    /**
     * UpdateProjectOrderStatus constructor
     *
     * @param Authable $auth The instance to use to verify the request.
     * @param NonceInterface $nonce The instance to use to verify the request.
     *
     * @since 1.0.0
     */
    public function __construct(Authable $auth, NonceInterface $nonce)
    {
        $this->auth = $auth;
        $this->nonce = $nonce;
    }

    /**
     * @inheritdoc
     */
    public function handle()
    {
        if (!$this->is_valid_request()) {
            return;
        }

        $data = $this->request_data();

        if (!$data) {
            TransientNoticeService::add_notice(
                esc_html__('The request is valid but no data was found.', 'translationmanager'),
                'error'
            );

            return;
        }

        try {
            // Retrieve the project info.
            $project = get_term(
                $data['translationmanager_project_id'],
                'translationmanager_project'
            );
            if (!$project instanceof WP_Term) {
                TransientNoticeService::add_notice(
                    esc_html__('Invalid project name.', 'translationmanager'),
                    'error'
                );

                return;
            }

            // Retrieve the generic status for the translation.
            $status = project_global_status($project);

            $this->update_project_status($project, $status);
            $this->update_project_status_request_date($project);

            if ('finished' === strtolower($status)) {
                // Update the translated at meta.
                $this->update_project_translated_at($project);
            }

            $notice = [
                'message' => esc_html__('Project updated.', 'translationmanager'),
                'severity' => 'success',
            ];
        } catch (Exception $e) {
            $notice = [
                'message' => $e->getMessage(),
                'severity' => 'error',
            ];
        }

        TransientNoticeService::add_notice($notice['message'], $notice['severity']);

        redirect_admin_page_network(
            'admin.php',
            [
                'page' => 'translationmanager-project',
                'translationmanager_project_id' => $project->term_id,
                'post_type' => 'project_item',
                'project_status' => $status,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function is_valid_request()
    {
        if (!isset($_POST['translationmanager_action_project_update'])) { // phpcs:ignore
            return false;
        }

        return $this->auth->can(wp_get_current_user(), self::$capability)
            && $this->auth->request_is_valid($this->nonce);
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
            ]
        );
    }

    /**
     * Update Order Status
     *
     * @param WP_Term $project The term object for which update the meta.
     * @param string $status The value to store as meta value.
     *
     * @return mixed Whatever the *_term_meta returns
     * @since 1.0.0
     */
    private function update_project_status(WP_Term $project, $status)
    {
        return set_unique_term_meta($project, '_translationmanager_order_status', $status);
    }

    /**
     * Update Order Status Last Update Date
     *
     * @param WP_Term $project The term object for which update the meta.
     *
     * @return mixed Whatever the *_term_meta returns
     * @throws Exception
     * @since 1.0.0
     */
    private function update_project_status_request_date(WP_Term $project)
    {
        return set_unique_term_meta(
            $project,
            '_translationmanager_order_status_last_update_request',
            (new DateTime('now', (new TimeZone())->value()))->getTimestamp()
        );
    }

    /**
     * Update Project Translated at meta
     *
     * @param WP_Term $project The term object for which update the meta.
     *
     * @return mixed Whatever the *_term_meta returns
     * @throws Exception
     * @since 1.0.0
     */
    private function update_project_translated_at(WP_Term $project)
    {
        return set_unique_term_meta(
            $project,
            '_translationmanager_order_translated_at',
            (new DateTime('now', (new TimeZone())->value()))->getTimestamp()
        );
    }
}
