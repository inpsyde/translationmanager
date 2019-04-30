<?php

namespace Translationmanager\ProjectItem;

use Translationmanager\Plugin;

class PostType {

	/**
	 * Trash Status
	 *
	 * @since 1.0.0
	 *
	 * @var string The trash status slug
	 */
	const STATUS_TRASH = 'trash';

	/**
	 * Plugin Instance
	 *
	 * @since 1.0.0
	 *
	 * @var \Translationmanager\Plugin The plugin instance
	 */
	private $plugin;

	/**
	 * ProjectItem constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Register The post type
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_post_type() {

		register_post_type(
			'project_item',
			[
				'label'         => esc_html__( 'Project', 'translationmanager' ),
				'labels'        => [
					'name'      => esc_html__( 'Project', 'translationmanager' ),
					'menu_name' => esc_html__( 'Translation', 'translationmanager' ),
				],
				'show_in_menu'  => false,
				'description'   => esc_html__( 'What you are about to order.', 'translationmanager' ),
				'public'        => false,
				'capabilities'  => [
					// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
					'create_posts'       => 'do_not_allow',
					'edit_post'          => 'manage_options',
					'read_post'          => 'manage_options',
					'delete_post'        => 'manage_options',
					'edit_posts'         => 'manage_options',
					'edit_others_posts'  => 'manage_options',
					'publish_posts'      => 'manage_options',
					'read_private_posts' => 'manage_options',
				],
				'map_meta_cap'  => false,
				'menu_position' => 100,
				'supports'      => [ 'title' ],
				'menu_icon'     => $this->plugin->url( '/resources/img/tm-icon-bw.png' ),
			]
		);
	}

	/**
	 * Filter Row Actions for Project post type
	 *
	 * @since 1.0.0
	 *
	 * @param array    $actions The actions list.
	 * @param \WP_Post $post    The current post.
	 *
	 * @return array The filtered list
	 */
	public function filter_row_actions( array $actions, \WP_Post $post ) {

		if ( $this->is_project_item_cpt() ) {
			$actions = [
				'trash' => sprintf(
					'<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
					get_delete_post_link( $post->ID ),
					/* translators: %s: post title */
					esc_attr( sprintf( __( 'Move &#8220;%s&#8221; to the Trash' ), $post->post_title ) ),
					esc_html__( 'Remove from project', 'translationmanager' )
				),
			];
		}

		return $actions;
	}

	/**
	 * Remove states from Project Post Type
	 *
	 * @since 1.0.0
	 *
	 * @param array    $post_states The post states.
	 * @param \WP_Post $post        The post object.
	 *
	 * @return array Empty array if the post type is the project one.
	 */
	public function remove_states_from_table_list( array $post_states, \WP_Post $post ) {

		if ( 'project_item' !== $post->post_type ) {
			return $post_states;
		}

		return [];
	}

	/**
	 * Filter Bulk Messages
	 *
	 * @since 1.0.0
	 *
	 * @param array $bulk_messages The messages list for the post type.
	 * @param array $bulk_counts   The quantity of the posts based on states.
	 *
	 * @return array The filtered bulk messages
	 */
	public function filter_bulk_updated_messages( array $bulk_messages, array $bulk_counts ) {

		$locked_msg = 1 === $bulk_counts['locked']
			? esc_html__( '1 page not updated, somebody is editing it.', 'translationmanager' )
			: _n( '%s page not updated, somebody is editing it.', '%s pages not updated, somebody is editing them.', $bulk_counts['locked'] );

		$bulk_messages['project_item'] = [
			'updated'   => esc_html__( 'Project has been updated.', 'translationmanager' ),
			'locked'    => $locked_msg,
			'deleted'   => _n(
				'%s translation permanently deleted.',
				'%s translations permanently deleted.',
				$bulk_counts['deleted'],
				'translationmanager'
			),
			'trashed'   => _n(
				'%s translation moved to the Trash.',
				'%s translations moved to the Trash.',
				$bulk_counts['trashed'],
				'translationmanager'
			),
			'untrashed' => _n(
				'%s translation restored from the Trash.',
				'%s translations restored from the Trash.',
				$bulk_counts['untrashed'],
				'translationmanager'
			),
		];

		// $bulk_messages['project_item'] = array(
		// 'updated'   => _n( '%s translation updated.', '%s translations updated.', $bulk_counts['updated'] ),
		// 'locked'    => _n( '%s translation not updated, somebody is editing it.', '%s translations not updated, somebody is editing them.', $bulk_counts['locked'] ),
		// 'deleted'   => _n( '%s translation permanently deleted.', '%s translations permanently deleted.', $bulk_counts['deleted'] ),
		// 'trashed'   => _n( '%s translation removed from the project.', '%s translations removed from the project.', $bulk_counts['trashed'] ),
		// 'untrashed' => _n( '%s translation restored at the project.', '%s translations restored at the project.', $bulk_counts['untrashed'] ),
		// );

		$updated = filter_input( INPUT_GET, 'updated', FILTER_SANITIZE_NUMBER_INT );
		if ( - 1 === $updated ) {
			$bulk_messages['project_item']['updated'] = esc_html__( 'Project has been created.', 'translationmanager' );
		}

		return $bulk_messages;
	}

	/**
	 * Check if the current screen is the post type
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the current screen is for the post type, false otherwise
	 */
	private function is_project_item_cpt() {

		return get_current_screen() && 'project_item' === get_current_screen()->post_type;
	}
}
