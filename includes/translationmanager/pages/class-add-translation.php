<?php

namespace Translationmanager\Pages;

class Add_Translation {
	/**
	 * @see ::handle_post()
	 */
	public function dispatch() {

		$type = sanitize_key( filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING ) );
		$id   = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );

		if ( ! current_user_can( 'edit_others_pages' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'translationmanager' ) );
		}

		if ( ! $type ) {
			wp_die( esc_html__( 'Something went wrong - missing type.', 'translationmanager' ) );
		}

		$method = 'handle_' . $type;

		if ( ! method_exists( $this, $method ) ) {
			wp_die( esc_html__( 'Invalid type', 'translationmanager' ) );
		}

		if ( ! $id ) {
			wp_die( esc_html__( 'Something went wrong - missing ID.', 'translationmanager' ) );
		}

		$this->$method( $id );

		// Redirect to cart.
		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' . http_build_query(
					array(
						'post_type' => 'tmanager_cart',
						'success'   => esc_html__( 'Item added to cart.', 'translationmanager' ),
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

		$languages       = array( 0, 1 );
		$valid_languages = translationmanager_get_languages();

		foreach ( $languages as $language_id ) {
			if ( ! isset( $valid_languages[ $language_id ] ) ) {
				continue;
			}

			$id = wp_insert_post(
				array(
					'post_type'  => 'tmanager_cart',
					'post_title' => sprintf(
						__( '%s: "%s"', 'translationmanager' ),
						$post_type_labels->singular_name,
						$post->post_title
					),
					'meta_input' => array(
						'_translationmanager_related_' . $post->post_type => $id,
						'_target_language'                                => $valid_languages[ $language_id ]['lang_code'],
					),
				)
			);
		}
	}
}