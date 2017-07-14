<?php

namespace Tmwp\Meta_Box;

class Order_Info {

	const ID = 'tmwp_order_info';

	const CONTEXT = 'side';
	private $project_id;

	public function __construct( $project_id = null ) {
		$this->project_id = $project_id;
	}

	public function add_meta_box() {
		add_meta_box(
			static::ID,
			__( 'Order information', 'tmwp' ),
			array( $this, 'dispatch' ),
			TMWP_ORDER,
			self::CONTEXT
		);
	}

	public function dispatch() {
		/** @var string $template */
		$template = tmwp_get_template( 'admin/meta-box/order-info.php' );

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
	 * @todo Correct status.
	 *
	 * @return mixed
	 */
	public function get_status() {
		if ( ! $this->get_order_id() ) {
			return __( 'Ready to order', 'tmwp' );
		}

		return apply_filters( 'tmwp_order_status', 'In preparation', get_the_ID() );
	}

	/**
	 * Returns REST API ID or Plunet ID.
	 *
	 * Returns rest ID
	 * and as soon as given the plunet ID.
	 *
	 * TODO return correct number.
	 *
	 * @return string
	 */
	public function get_order_id() {
		return get_term_meta( $this->get_project_id(), '_tmwp_order_id', true );
	}

	/**
	 * @return mixed
	 */
	public function get_project_id() {
		return $this->project_id;
	}

	/**
	 * @return \DateTime
	 */
	public function get_ordered_at() {
		if (!get_post()) {
			return null;
		}

		return new \DateTime( get_post()->post_date );
	}

	/**
	 * @return \DateTime
	 */
	public function get_translated_at() {
		return null;
	}
}