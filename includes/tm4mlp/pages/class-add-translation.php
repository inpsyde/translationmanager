<?php

namespace Tm4mlp\Pages;

class Add_Translation {
	/**
	 * @see ::handle_post()
	 */
	public function dispatch() {
		if ( ! current_user_can( TM4MLP_CAP_TRANSLATION_REQUEST ) ) {
			wp_die( __( 'You are not allowed to do this' ) );
		}

		if ( ! isset( $_GET['type'] ) ) { // Input var okay
			wp_die( __( 'Something went wrong - missing type.' ) );
		}

		$type   = sanitize_key( $_GET['type'] ); // Input var okay
		$method = 'handle_' . $type;

		if ( ! method_exists( $this, $method ) ) {
			wp_die( __( 'Invalid type' ) );
		}

		if ( ! isset( $_GET['id'] ) || ! intval( $_GET['id'] ) ) { // Input var okay
			wp_die( __( 'Something went wrong - missing ID.' ) );
		}

		$this->$method( intval( $_GET['id'] ) ); // Input var okay; WPCS: sanitization okay

		// Redirect to cart.
		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' . http_build_query(
					array(
						'post_type' => TM4MLP_CART,
						'success'   => __( 'Item added to cart.', 'tm4mlp' )
					)
				)
			)
		);
	}

	protected function handle_post( $id ) {
		$post = get_post( $id );

		if ( is_wp_error( $post ) ) {
			throw new \InvalidArgumentException( 'Post with ID ' . (int) $id . ' not found' );
		}

		$data           = $post->to_array();
		$data['__meta'] = array();

		$post_type_labels = get_post_type_labels( get_post_type_object( $post->post_type ) );

		wp_insert_post(
			array(
				'post_type'  => TM4MLP_CART,
				'post_title' => sprintf(
					__( '%s: "%s"', 'tm4mlp' ),
					$post_type_labels->singular_name,
					$post->post_title
				),
				'meta_input' => array(
					'_tm4mlp_related_' . $post->post_type => $id,
				)
			)
		);
	}
}