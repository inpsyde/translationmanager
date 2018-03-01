<?php

namespace Translationmanager\Meta_Box;

use Translationmanager\Functions;

class Order_Info {

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
	 * Project ID
	 *
	 * @var null
	 */
	private $project_id;

	/**
	 * Order_Info constructor
	 *
	 * @since 1.0.0
	 *
	 * @param null $project_id
	 */
	public function __construct( $project_id = null ) {

		$this->project_id = $project_id;
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
			array( $this, 'dispatch' ),
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
	 * - In Vorbereitung
	 * - In Arbeit
	 * - Geliefert
	 *
	 * @since 1.0.0
	 *
	 * @todo  Correct status.
	 * @fixme Why pass the post ID loop in a term context?
	 *
	 * @return string The status for the current order.
	 */
	public function get_status() {

		if ( ! $this->get_order_id() ) {
			return esc_html__( 'Ready to order', 'translationmanager' );
		}

		return apply_filters( 'translationmanager_order_status', 'In preparation', get_the_ID() );
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

		return get_term_meta( $this->get_project_id(), '_translationmanager_order_id', true );
	}

	/**
	 * Project ID
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_project_id() {

		return $this->project_id;
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

		return new \DateTime( get_post()->post_date );
	}

	/**
	 * Get translated date
	 *
	 * @todo  To Implement get_translated_at.
	 *
	 * @since 1.0.0
	 *
	 * @return \DateTime
	 */
	public function get_translated_at() {

		return null;
	}

	/**
	 * Has Projects
	 *
	 * @since 1.0.0
	 *
	 * @return int The number of projects within the current order
	 */
	public function has_projects() {

		$posts = Functions\get_project_items( $this->project_id );

		return count( $posts );
	}
}
