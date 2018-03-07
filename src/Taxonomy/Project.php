<?php
/**
 * Project
 *
 * @since   1.0.0
 * @package Translationmanager\Taxonomy
 */

namespace Translationmanager\Taxonomy;

use Translationmanager\Functions;

use Translationmanager\Notice\TransientNoticeService;

/**
 * Class Project
 *
 * @since   1.0.0
 * @package Translationmanager\Taxonomy
 */
class Project {

	/**
	 * @since 1.0.0
	 */
	const COL_STATUS = 'translationmanager_order_status';

	/**
	 * @since 1.0.0
	 */
	const COL_ACTIONS = 'translationmanager_order_action';

	/**
	 * Set Hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'init', [ $this, 'register_taxonomy' ] );
		add_action( 'manage_translationmanager_project_custom_column', [ $this, 'print_column' ], 10, 3 );
		add_action( 'admin_post_translationmanager_project_info_save', [ $this, 'project_info_save' ] );

		add_filter( 'manage_edit-translationmanager_project_columns', [ $this, 'modify_columns' ] );
		add_filter( 'translationmanager_project_row_actions', [ $this, 'modify_row_actions' ], 10, 2 );
		add_filter( 'views_project_item', [ $this, 'order_project_box_form' ] );
		add_filter( 'views_project_item', [ $this, 'project_form' ] );
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
	 * Nonce
	 *
	 * @since 1.0.0
	 *
	 * @return \Brain\Nonces\WpNonce The nonce instance
	 */
	public function nonce() {

		return new \Brain\Nonces\WpNonce( 'update_project_info' );
	}

	/**
	 * Save Project Info based on request
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function project_info_save() {

		// Check Action and auth.
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
		if ( 'translationmanager_project_info_save' !== $action ) {
			return;
		}

		if ( ! $this->nonce()->validate() || ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Cheating Uh?' );
		}

		$project_id = sanitize_title( filter_input( INPUT_POST, '_translationmanager_project_id', FILTER_SANITIZE_STRING ) );
		$project    = get_term_by( 'slug', $project_id, 'translationmanager_project' );

		if ( ! $project instanceof \WP_Term ) {
			TransientNoticeService::add_notice( esc_html__(
				'Invalid project ID, impossible to update the info.', 'translationmanager'
			), 'warning' );
		}

		$update = wp_update_term( $project->term_id, 'translationmanager_project', [
			'name'        => sanitize_text_field( filter_input( INPUT_POST, 'tag-name', FILTER_SANITIZE_STRING ) ),
			'description' => filter_input( INPUT_POST, 'description', FILTER_SANITIZE_STRING ),
		] );

		if ( is_wp_error( $update ) ) {
			TransientNoticeService::add_notice( esc_html__(
				'Something went wrong. Please go back and try again.', 'translationmanager'
			), 'warning' );
		}

		TransientNoticeService::add_notice( sprintf( esc_html__(
			'Project %s updated.', 'translationmanager'
		), '<strong>' . $project_id . '</strong>' ), 'success' );

		wp_safe_redirect( wp_get_referer() );

		die;
	}

	/**
	 * Register Taxonomy
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_taxonomy() {

		register_taxonomy(
			'translationmanager_project',
			'project_item',
			[
				'label'  => esc_html__( 'Projects', 'translationmanager' ),
				'labels' => [
					'add_new_item' => esc_html__( 'Create new project', 'translationmanager' ),
				],
				'public' => true,
			]
		);
	}

	/**
	 * Register Status for Post
	 *
	 * @since 1.0.0
	 */
	public static function register_post_status() {
	}

	/**
	 * Edit Row Actions
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $columns The columns contain the values for the row.
	 * @param \WP_Term $term    The term instance related to the columns.
	 *
	 * @return array The columns content
	 */
	public static function modify_row_actions( $columns, $term ) {

		$new_columns = [
			'delete' => $columns['delete'],
			'view'   => sprintf(
				'<a href="%s">%s</a>',
				self::get_project_link( $term->term_id ),
				esc_html__( 'View', 'translationmanager' )
			),
		];

		return $new_columns;
	}

	/**
	 * Project Link
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id The term id from which retrieve the project name.
	 *
	 * @return string
	 */
	public static function get_project_link( $term_id ) {

		return get_admin_url(
			null,
			add_query_arg( [
				'page'                       => 'translationmanager-project',
				'translationmanager_project' => get_term_field( 'slug', $term_id ),
				'post_type'                  => 'project_item',
			], 'admin.php' )
		);
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public static function modify_columns( $columns ) {

		unset( $columns['cb'] );
		unset( $columns['slug'] );
		unset( $columns['posts'] );

		// Add status ad second place.
		$columns = array_slice( $columns, 0, 1 )
		           + [ static::COL_STATUS => esc_html__( 'Status', 'translationmanager' ) ]
		           + array_slice( $columns, 1 );

		$columns[ static::COL_ACTIONS ] = '';

		return $columns;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $value
	 * @param $column_name
	 * @param $term_id
	 *
	 * @return string
	 */
	public static function print_column( $value, $column_name, $term_id ) {

		switch ( $column_name ) {
			case static::COL_STATUS:
				if ( ! get_term_meta( $term_id, '_translationmanager_order_id', true ) ) {
					return esc_html__( 'New', 'translationmanager' );
				}

				return sprintf(
					esc_html__( 'Ordered at %s', 'translationmanager' ),
					date( 'Y-m-d' )
				);
				break;
			case static::COL_ACTIONS:
				return sprintf(
					'<a href="%s" class="button">%s</a>',
					self::get_project_link( $term_id ),
					esc_html__( 'Show project', 'translationmanager' )
				);
		}

		return $value;
	}
}
