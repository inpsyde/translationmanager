<?php

namespace Translationmanager\Pages;

use Translationmanager\Functions;

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

		Functions\redirect_admin_page_network( 'edit.php?', [
			'post_type' => 'project_item',
			'success'   => esc_html__( 'Item added to project.', 'translationmanager' ),
		] );
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
		$valid_languages = Functions\get_languages();

		foreach ( $languages as $language_id ) {
			if ( ! isset( $valid_languages[ $language_id ] ) ) {
				continue;
			}

			$id = wp_insert_post(
				array(
					'post_type'  => 'project_item',
					'post_title' => sprintf(
						esc_html__( '%s: "%s"', 'translationmanager' ),
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