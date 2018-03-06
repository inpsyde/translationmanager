<?php

namespace Translationmanager\PostType;

use Translationmanager\Functions;
use Translationmanager\Plugin;

class ProjectItem {

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
	 * Set Hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'admin_head', [ $this, 'remove_month_dropdown_results' ] );
		add_action( 'manage_project_item_posts_columns', [ $this, 'filter_columns' ], 10, 2 );
		add_action( 'manage_edit-project_item_sortable_columns', [ $this, 'filter_sortable_columns' ], 10, 2 );
		add_action( 'manage_project_item_posts_custom_column', [ $this, 'print_column' ], 10, 2 );
		add_action( 'pre_get_posts', [ $this, 'filter_order_by' ] );

		add_filter( 'bulk_actions-edit-project_item', [ $this, 'filter_bulk_actions_labels' ] );
		add_filter( 'post_row_actions', [ $this, 'filter_row_actions' ], 10, 2 );
		add_filter( 'display_post_states', [ $this, 'remove_states_from_table_list' ], 10, 2 );
		add_filter( 'views_edit-project_item', [ $this, 'order_project_box_form' ] );
		add_filter( 'views_edit-project_item', [ $this, 'project_form' ] );
		add_filter( 'bulk_post_updated_messages', [ $this, 'filter_bulk_updated_messages' ], 10, 2 );
	}

	/**
	 * Register The post type
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_post_type() {

		register_post_type( 'project_item', [
			'label'         => esc_html__( 'Project', 'translationmanager' ),
			'labels'        => [
				'name'      => esc_html__( 'Project', 'translationmanager' ),
				'menu_name' => esc_html__( 'Translation', 'translationmanager' ),
			],
			'show_in_menu'  => false,
			'description'   => esc_html__( 'What you are about to order.', 'translationmanager' ),
			'public'        => true,
			'capabilities'  => [
				// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
				'create_posts' => 'do_not_allow',
			],
			'map_meta_cap'  => true,
			'menu_position' => 100,
			'supports'      => [ 'title' ],
			'menu_icon'     => ( $this->plugin )->url( '/resources/img/tm-icon-bw.png' ),
		] );
	}

	/**
	 * Filter Columns
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns The columns to filter.
	 *
	 * @return array The filtered columns
	 */
	public function filter_columns( $columns ) {

		if ( ! $this->is_project_item_cpt() ) {
			return $columns;
		}

		unset( $columns['date'] );

		$columns = $this->column_project( $columns );
		$columns = $this->column_languages( $columns );

		$columns['translationmanager_added_by'] = esc_html__( 'Added By', 'translationmanager' );
		$columns['translationmanager_added_at'] = esc_html__( 'Added At', 'translationmanager' );

		return $columns;
	}

	/**
	 * Filter Sortable Columns
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns The sortable columns.
	 *
	 * @return array The filtered sortable columns
	 */
	public function filter_sortable_columns( $columns ) {

		return array_merge( $columns, [
			'translationmanager_added_by'               => 'translationmanager_added_by',
			'translationmanager_added_at'               => 'translationmanager_added_at',
			'translationmanager_target_language_column' => 'translationmanager_target_language_column',
		] );
	}

	/**
	 * Print Table List Columns
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name The column name key.
	 * @param int    $post_id     The post id.
	 */
	public function print_column( $column_name, $post_id ) {

		switch ( $column_name ) {
			case 'translationmanager_project':
				$terms = get_the_terms( $post_id, 'translationmanager_project' );

				if ( ! $terms ) {
					break;
				}

				foreach ( $terms as $term ) {
					printf(
						'<a href="%s">%s</a>',
						esc_url( add_query_arg( [
							'translationmanager_project' => $term->slug,
							'post_type'                  => 'project_item',
						], 'edit.php' ) ),
						esc_html( $term->name )
					);
				}
				break;

			case 'translationmanager_source_language_column':
				$languages = Functions\current_language();

				if ( $languages ) {
					printf(
						'<a href="%1$s">%2$s</a>',
						esc_url( get_blog_details( get_current_blog_id() )->siteurl ),
						esc_html( $languages->get_label() )
					);
					break;
				}

				// In case of failure.
				echo esc_html__( 'Unknown', 'translationmanager' );
				break;

			case 'translationmanager_target_language_column':
				$lang_id   = get_post_meta( $post_id, '_translationmanager_target_id', true );
				$languages = Functions\get_languages();

				if ( $lang_id && isset( $languages[ $lang_id ] ) ) {
					printf(
						'<a href="%1$s">%2$s</a>',
						esc_url( get_blog_details( intval( $lang_id ) )->siteurl ),
						esc_html( $languages[ $lang_id ]->get_label() )
					);
					break;
				}

				// In case of failure.
				echo esc_html__( 'Unknown', 'translationmanager' );
				break;

			case 'translationmanager_added_by':
				$user = new \WP_User( get_post( $post_id )->post_author );
				echo esc_html( esc_html( ucfirst( Functions\username( $user ) ) ) );
				break;

			case 'translationmanager_added_at':
				echo esc_html( get_the_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $post_id ) );
				break;
		}
	}

	/**
	 * Filter filter order by
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Query $query The current query to filter.
	 *
	 * @return void
	 */
	public function filter_order_by( \WP_Query $query ) {

		if ( $this->is_project_item_cpt() ) {
			$orderby = $query->get( 'orderby' );

			switch ( $orderby ) {
				case 'translationmanager_added_by':
					$query->set( 'orderby', 'author' );
					break;

				case 'translationmanager_added_at':
					$query->set( 'orderby', 'date' );
					break;

				case 'translationmanager_target_language_column':
					$query->set( 'meta_key', '_translationmanager_target_id' );
					$query->set( 'orderby', 'meta_value' );
					break;
			}
		}

		// Remove after done.
		remove_action( 'pre_get_posts', [ $this, 'filter_order_by' ] );
	}

	/**
	 * Remove the Month Dropdown
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function remove_month_dropdown_results() {

		if ( $this->is_project_item_cpt() ) {
			add_filter( 'months_dropdown_results', '__return_empty_array' );
		}
	}

	/**
	 * Change the Bulk Actions labels for projects
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions The bulk actions list.
	 *
	 * @return array The filtered actions
	 */
	public function filter_bulk_actions_labels( array $actions ) {

		unset( $actions['edit'] );

		if ( isset( $actions['trash'] ) ) {
			$actions['trash'] = esc_html__( 'Remove from project', 'translationmanager' );
		}

		return $actions;
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

		if ( $this->is_project_item_cpt() && isset( $actions['trash'] ) ) {
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
	 * Project Box in Edit Page
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The views link. Untouched.
	 *
	 * @return string The untouched parameter
	 */
	public function order_project_box_form( $value ) {

		$slug = sanitize_title( filter_input( INPUT_GET, 'translationmanager_project', FILTER_SANITIZE_STRING ) );

		if ( $slug ) {
			$term = get_term_by( 'slug', $slug, 'translationmanager_project' );
			// This is used inside the view.
			( new \Translationmanager\MetaBox\OrderInfo( $term->term_id ) )->render_template();
		}

		return $value;
	}

	/**
	 * Project Title and Description Form in edit page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The views link. Untouched.
	 *
	 * @return string The untouched parameter
	 */
	public function project_form( $value ) {

		$slug = sanitize_title( filter_input( INPUT_GET, 'translationmanager_project', FILTER_SANITIZE_STRING ) );

		if ( $slug ) {
			$term = get_term_by( 'slug', $slug, 'translationmanager_project' );

			require Functions\get_template( '/views/project/form-title-description.php' );
		}

		return $value;
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

		//	$bulk_messages['project_item'] = array(
		//		'updated'   => _n( '%s translation updated.', '%s translations updated.', $bulk_counts['updated'] ),
		//		'locked'    => _n( '%s translation not updated, somebody is editing it.', '%s translations not updated, somebody is editing them.', $bulk_counts['locked'] ),
		//		'deleted'   => _n( '%s translation permanently deleted.', '%s translations permanently deleted.', $bulk_counts['deleted'] ),
		//		'trashed'   => _n( '%s translation removed from the project.', '%s translations removed from the project.', $bulk_counts['trashed'] ),
		//		'untrashed' => _n( '%s translation restored at the project.', '%s translations restored at the project.', $bulk_counts['untrashed'] ),
		//	);

		$updated = filter_input( INPUT_GET, 'updated', FILTER_SANITIZE_NUMBER_INT );
		if ( - 1 === $updated ) {
			$bulk_messages['project_item']['updated'] = esc_html__( 'Project has been created.', 'translationmanager' );
		}

		return $bulk_messages;
	}

	/**
	 * Filter Project Column
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns The columns items to filter.
	 *
	 * @return array The filtered columns
	 */
	private function column_project( $columns ) {

		$request = $_GET; // phpcs:ignore
		foreach ( $request as $key => $val ) {
			$request[ $key ] = sanitize_text_field( filter_input( INPUT_GET, $key, FILTER_SANITIZE_STRING ) );
		}

		$request = wp_parse_args( $request, [
			'translationmanager_project' => null,
		] );

		if ( isset( $request['post_status'] ) && static::STATUS_TRASH === $request['post_status'] ) {
			// This is trash so we show no project column.
			return $columns;
		}

		if ( $request['translationmanager_project'] ) {
			// Term/Project filter is active so this col is not needed.
			return $columns;
		}

		$columns['translationmanager_project'] = esc_html__( 'Project', 'translationmanager' );

		return $columns;
	}

	/**
	 * Filter Column Language
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns The columns items to filter.
	 *
	 * @return array The filtered columns
	 */
	private function column_languages( $columns ) {

		$columns['translationmanager_source_language_column'] = esc_html__( 'Source language', 'translationmanager' );
		$columns['translationmanager_target_language_column'] = esc_html__( 'Target language', 'translationmanager' );

		return $columns;
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
