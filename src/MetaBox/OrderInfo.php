<?php
/**
 * Order Info
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */

namespace Translationmanager\MetaBox;

use Translationmanager\Functions;
use Translationmanager\Utils\TimeZone;

/**
 * Class OrderInfo
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */
class OrderInfo {

	/**
	 * The metabox ID
	 *
	 * @since 1.0.0
	 */
	const ID = 'translationmanager_order_info';

	/**
	 * Context for the metabox
	 *
	 * @since 1.0.0
	 */
	const CONTEXT = 'side';

	/**
	 * Projects Term ID
	 *
	 * @var int The ID used to retrieve the projects associated to this term.
	 */
	private $projects_term_id;

	/**
	 * OrderInfo constructor
	 *
	 * @since 1.0.0
	 *
	 * @param int $projects_term_id The order ID that include project items.
	 */
	public function __construct( $projects_term_id ) {

		$this->projects_term_id = $projects_term_id;
	}

	/**
	 * Add Meta Box
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_meta_box() {

		add_meta_box(
			static::ID,
			esc_html__( 'Order information', 'translationmanager' ),
			[ $this, 'dispatch' ],
			'tm_order',
			self::CONTEXT
		);
	}

	/**
	 * Dispatch
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function dispatch() {

		$template = Functions\get_template( 'views/meta-box/order-info.php' );

		if ( ! $template || ! file_exists( $template ) ) {
			return;
		}

		require $template;
	}

	/**
	 * States can be (german):
	 *
	 * - In Vorbereitung ( In Preparation )
	 * - In Arbeit ( In Progress )
	 * - Geliefert ( Supplied )
	 *
	 * @since 1.0.0
	 *
	 * @return string The status for the current order.
	 */
	public function get_status_label() {

		$order_status = $this->get_order_status();

		if ( ! $order_status ) {
			return esc_html__( 'Ready to order', 'translationmanager' );
		}

		return apply_filters( 'translationmanager_order_status', $order_status, $this );
	}

	/**
	 * Get Order Status
	 *
	 * @since 1.0.0
	 *
	 * @return string The status of the project translation order
	 */
	public function get_order_status() {

		return get_term_meta( $this->projects_term_id, '_translationmanager_order_status', true );
	}

	/**
	 * Retrieve the latest request order status Date
	 *
	 * @since 1.0.0
	 *
	 * @return \DateTime|null Null if the value doesn't exists. DateTime instance otherwise.
	 */
	public function get_latest_update_request_date() {

		$timestamp = get_term_meta( $this->projects_term_id, '_translationmanager_order_status_last_update_request', true );

		if ( ! $timestamp ) {
			return null;
		}

		$date = new \DateTime( 'now', ( new TimeZone() )->value() );
		$date->setTimestamp( $timestamp );

		return $date;
	}

	/**
	 * Returns REST API ID or Plunet ID.
	 *
	 * Returns rest ID and as soon as given the plunet ID.
	 *
	 * @since 1.0.0
	 *
	 * TODO return correct number.
	 *
	 * @return string The meta value
	 */
	public function get_order_id() {

		return get_term_meta( $this->projects_term_id, '_translationmanager_order_id', true );
	}

	/**
	 * Get ordered date
	 *
	 * @since 1.0.0
	 *
	 * @return \DateTime
	 */
	public function get_ordered_at() {

		if ( ! get_post() ) {
			return null;
		}

		return new \DateTime( get_post()->post_date, ( new TimeZone() )->value() );
	}

	/**
	 * Get translated date
	 *
	 * @since 1.0.0
	 *
	 * @return \DateTime
	 */
	public function get_translated_at() {

		$timestamp = get_term_meta( $this->projects_term_id, '_translationmanager_order_translated_at', true );

		if ( ! $timestamp ) {
			return null;
		}

		$date = new \DateTime( 'now', ( new TimeZone() )->value() );
		$date->setTimestamp( $timestamp );

		return $date;
	}

	/**
	 * Has Projects
	 *
	 * @since 1.0.0
	 *
	 * @return int The number of projects within the current order
	 */
	public function has_projects() {

		$posts = Functions\get_project_items( $this->projects_term_id );

		return count( $posts );
	}

	/**
	 * Nonce Instance
	 *
	 * @since 1.0.0
	 *
	 * @return \Brain\Nonces\WpNonce The nonce instance
	 */
	public function nonce() {

		return new \Brain\Nonces\WpNonce( 'order_translation' );
	}
}
