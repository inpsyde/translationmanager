<?php

namespace Tm4mlp\Pages;

class Add_Translation {
	public function dispatch() {
		if ( ! current_user_can( TM4MLP_CAP_TRANSLATION_REQUEST ) ) {
			wp_die( __( 'You are not allowed to do this' ) );
		}

		$type   = trim( strval( wp_unslash( $_GET['type'] ) ) ); // Input var okay
		$method = 'handle_' . $type;

		if ( ! method_exists( $this, $method ) ) {
			wp_die( __( 'Can not handle ' . $type ) );
		}

		if ( ! isset( $_GET['id'] ) || ! intval( $_GET['id'] ) ) {
			wp_die( __( 'Something went wrong - missing ID.' ) );
		}

		$this->$method( intval( $_GET['id'] ) ); // Input var okay
	}

	protected function handle_post( $id ) {
		$post = get_post( $id );

		if ( is_wp_error( $post ) ) {
			throw new \InvalidArgumentException( 'Post with ID ' . (int) $id . ' not found' );
		}

		$data = apply_filters( 'tm4mlp_sanitize_post', $post->to_array(), $post );

		// TODO Send data to Etrapi

		wp_insert_post(
			array(
				'post_type'            => TM4MLP_TRANSLATION_STATUS_POST_TYPE,
				'post_title'           => 'stub',
				'_tm4mlp_related_type' => 'post',
				'_tm4mlp_related_id'   => $id,
			)
		);
	}
}