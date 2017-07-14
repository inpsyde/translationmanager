<?php

namespace Tmwp\Admin;

/**
 * @package Tmwp\Admin
 */
class Cart_Updater {

	/**
	 * @var string
	 */
	private $append_to_title = '';

	/**
	 * Add action and filters to make the thing work
	 */
	public function setup() {

		add_filter( TMWP_FILTER_PROJECT_ADD_TRANSLATION, array( $this, 'force_ancestors_in_cart' ), 10, 4 );
	}

	/**
	 * Run after all the cart items referring a post have been created and assigned to a project, extracts the
	 * ancestor ids of the post being translated and add those ancestors to cart as well, ith they are not there
	 * already.
	 *
	 * @wp-hook tmwp_action_project_add_translation
	 *
	 * @param int     $project
	 * @param int     $post_id
	 * @param array() $languages
	 *
	 * @return mixed
	 */
	public function force_ancestors_in_cart( $project, $post_id, $languages ) {

		$post = get_post( $post_id );

		if ( ! $post || ! apply_filters( 'tmwp_force_add_parent_translations', false, $post ) ) {
			return $project;
		}

		$ancestors = wp_parse_id_list( get_post_ancestors( $post ) );

		if ( ! $ancestors ) {
			return $project;
		}

		$cart_items_for_post_ancestors = get_posts(
			array(
				'fields'     => 'ids',
				'post_type'  => TMWP_CART,
				'nopaging'   => true,
				'tax_query'  => array(
					array(
						'taxonomy' => TMWP_TAX_PROJECT,
						'terms'    => array( $project ),
						'field'    => 'term_id'
					)
				),
				'meta_query' => array(
					array(
						'key'     => '_tmwp_post_id',
						'value'   => $ancestors,
						'compare' => 'IN',
						'type'    => 'NUMERIC'
					),
					array(
						'key'     => '_tmwp_target_id',
						'value'   => $languages,
						'compare' => 'IN'
					)
				)
			)
		);

		$already_in_cart = array();
		foreach ( $cart_items_for_post_ancestors as $cart_item_id ) {

			$lang = get_post_meta( $cart_item_id, '_tmwp_target_id', true );
			if ( ! $lang || ! in_array( $lang, $languages, true ) ) {
				continue;
			}

			empty( $already_in_cart[ $lang ] ) and $already_in_cart[ $lang ] = array();
			$added_ancestor_id = (int) get_post_meta( $cart_item_id, '_tmwp_post_id', true );
			$added_ancestor_id and $already_in_cart[ $lang ][ $added_ancestor_id ] = true;
		}

		$original_title        = get_the_title( $post );
		$ancestor_hint         = esc_html__( 'ancestor of: "%s"', 'translationmanager' );
		$this->append_to_title = '(' . sprintf( $ancestor_hint, $original_title ) . ')';

		$handler = new Handler\Project_Handler();

		add_filter( 'wp_insert_post_data', array( $this, 'update_cart_item_title' ), 10 );

		foreach ( $languages as $lang_id ) {
			foreach ( $ancestors as $ancestor_id ) {
				if ( empty( $already_in_cart[ $lang_id ][ $ancestor_id ] ) ) {
					$handler->add_translation( $project, $ancestor_id, $lang_id );
				}
			}
		}

		$this->append_to_title = '';
		remove_filter( 'wp_insert_post_data', array( $this, 'update_cart_item_title' ), 10 );

		return $project;
	}

	/**
	 * Filter the cart item post data being added, appending to title an hint that post was added automatically because
	 * ancestor of another post.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function update_cart_item_title( array $data ) {

		if ( $this->append_to_title && ! empty( $data[ 'post_type' ] ) && $data[ 'post_type' ] === TMWP_CART ) {
			empty( $data[ 'post_title' ] ) and $data[ 'post_title' ] = '';
			$data[ 'post_title' ] and $data[ 'post_title' ] .= ' ';
			$data[ 'post_title' ] = $this->append_to_title;
		}

		return $data;
	}

}