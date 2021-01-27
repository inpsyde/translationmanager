<?php

/**
 * Order Info
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */

namespace Translationmanager\View\Project;

use Brain\Nonces\WpNonce;
use DateTime;
use Translationmanager\Functions;
use Translationmanager\Utils\TimeZone;
use Translationmanager\View\Viewable;

/**
 * Class OrderInfo
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */
class OrderInfo implements Viewable
{
    /**
     * Projects Term ID
     *
     * @var int The ID used to retrieve the projects associated to this term.
     */
    private $projects_term_id;

    /**
     * OrderInfo constructor
     *
     * @param int $projects_term_id The order ID that include project items.
     *
     * @since 1.0.0
     */
    public function __construct($projects_term_id)
    {
        $this->projects_term_id = $projects_term_id;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $template = Functions\get_template('views/project/order-info.php');

        if (!$template || !file_exists($template)) {
            return;
        }

        require $template;
    }

    /**
     * Nonce
     *
     * @return \Brain\Nonces\WpNonce The instance of the nonce
     * @since 1.0.0
     */
    private function nonce()
    {
        $action = str_replace('translationmanager_', '', $this->action());

        return new WpNonce($action);
    }

    /**
     * States can be (german):
     *
     * - In Vorbereitung ( In Preparation )
     * - In Arbeit ( In Progress )
     * - Geliefert ( Supplied )
     *
     * @return string The status for the current order.
     * @since 1.0.0
     */
    public function get_status_label()
    {
        $order_status = $this->get_order_status();

        if (!$order_status) {
            return esc_html__('Ready to order', 'translationmanager');
        }

        return apply_filters('translationmanager_order_status', $order_status, $this);
    }

    /**
     * Get Order Status
     *
     * @return string The status of the project translation order
     * @since 1.0.0
     */
    private function get_order_status()
    {
        return get_term_meta($this->projects_term_id, '_translationmanager_order_status', true);
    }

    /**
     * Retrieve the latest request order status Date
     *
     * @return \DateTime|null Null if the value doesn't exists. DateTime instance otherwise.
     * @since 1.0.0
     * @throws \Exception
     */
    private function get_latest_update_request_date()
    {
        $timestamp = get_term_meta(
            $this->projects_term_id,
            '_translationmanager_order_status_last_update_request',
            true
        );

        if (!$timestamp) {
            return null;
        }

        $date = new DateTime('now', (new TimeZone())->value());
        $date->setTimestamp($timestamp);

        return $date;
    }

    /**
     * Returns REST API ID or Plunet ID.
     *
     * Returns rest ID and as soon as given the plunet ID.
     *
     * @return string The meta value
     * @since 1.0.0
     *
     * TODO return correct number.
     */
    private function get_order_id()
    {
        return get_term_meta($this->projects_term_id, '_translationmanager_order_id', true);
    }

    /**
     * Get ordered date
     *
     * @return \DateTime
     * @throws \Exception
     * @since 1.0.0
     */
    private function get_ordered_at()
    {
        $posts = Functions\get_project_items($this->projects_term_id);

        return new DateTime($posts[0]->post_date, (new TimeZone())->value());
    }

    /**
     * Get translated date
     *
     * @return \DateTime
     * @throws \Exception
     * @since 1.0.0
     */
    private function get_translated_at()
    {
        $timestamp = get_term_meta(
            $this->projects_term_id,
            '_translationmanager_order_translated_at',
            true
        );

        if (!$timestamp) {
            return null;
        }

        $date = new DateTime('now', (new TimeZone())->value());
        $date->setTimestamp($timestamp);

        return $date;
    }

    /**
     * Has Projects
     *
     * @return int The number of projects within the current order
     * @since 1.0.0
     */
    private function has_projects()
    {
        $posts = Functions\get_project_items($this->projects_term_id);

        return count($posts);
    }

    /**
     * Action
     *
     * @return string The action to perform.
     * @since 1.0.0
     */
    private function action()
    {
        if ($this->get_translated_at() instanceof DateTime) {
            return 'translationmanager_import_project';
        }

        if ($this->get_order_id()) {
            return 'translationmanager_update_project';
        }

        return 'translationmanager_order_project';
    }
}
