<?php

namespace Tm4mlp\Pages;

class Add_Translation {
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
	}

	protected function handle_post( $id ) {
		$post = get_post( $id );

		if ( is_wp_error( $post ) ) {
			throw new \InvalidArgumentException( 'Post with ID ' . (int) $id . ' not found' );
		}

		$data           = $post->to_array();
		$data['__meta'] = array();

		/**
		 * Sanitizes the translation source data.
		 *
		 * Within this hook the data can be reduced
		 * or enriched by other plugins / modules.
		 *
		 * @see   tm4mlp_sanitize_post()
		 *
		 * @since 1.0.0
		 *
		 * @param array    $data The current sanitized data which will be send in for translation.
		 * @param \WP_Post $post The target post which needs to be translated.
		 */
		$data = apply_filters( 'tm4mlp_sanitize_post', $data, $post );

		$order_id = tm4mlp_api_order( $data );

		$id = wp_insert_post(
			array(
				'post_type'  => TM4MLP_CART,
				'post_title' => sprintf(
					__( 'Translation of "%s"', 'tm4mlp' ),
					$post->post_title
				),
				'meta_input' => array(
					'_tm4mlp_related_' . $post->post_type => $id,
					'_tm4mlp_order_id'                    => $order_id,
				)
			)
		);
	}
}