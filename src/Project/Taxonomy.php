<?php
/**
 * Project
 *
 * @since   1.0.0
 * @package Translationmanager\Project
 */

namespace Translationmanager\Project;

use Translationmanager\Functions;

use Translationmanager\Notice\TransientNoticeService;

/**
 * Class Taxonomy
 *
 * @since   1.0.0
 * @package Translationmanager\Project
 */
class Taxonomy {

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
		add_action( 'translationmanager_project_item_table_views', [ $this, 'project_form' ] );
		add_action( 'translationmanager_project_item_table_views', [ $this, 'order_project_box_form' ] );

		add_filter( 'manage_edit-translationmanager_project_columns', [ $this, 'modify_columns' ] );
		add_filter( 'translationmanager_project_row_actions', [ $this, 'modify_row_actions' ], 10, 2 );
		add_filter( 'get_edit_term_link', [ $this, 'edit_term_link' ], 10, 3 );
	}

	/**
	 * Project Title and Description Form in edit page.
	 *
	 * @todo  This is hooked in a filter, may create confusion about the value passed in.
	 *       Is there a way to move into an action?
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The views link. Untouched.
	 *
	 * @return string The untouched parameter
	 */
	public function project_form( $value ) {

		$project = filter_input( INPUT_GET, 'translationmanager_project_id', FILTER_SANITIZE_NUMBER_INT );

		$project = get_term( $project, 'translationmanager_project' );
		if ( ! $project instanceof \WP_Term ) {
			return $value;
		}

		$bind = (object) [
			'project' => $project,
			'nonce'   => $this->nonce(),
		];

		\Closure::bind( function () {

			// @todo Make it a View.
			require Functions\get_template( '/views/project/form-title-description.php' );
		}, $bind )();

		return $value;
	}

	/**
	 * Project Box in Edit Page
	 *
	 * @todo  This is hooked in a filter, may create confusion about the value passed in.
	 *       Is there a way to move into an action?
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The views link. Untouched.
	 *
	 * @return string The untouched parameter
	 */
	public function order_project_box_form( $value ) {

		$project = filter_input( INPUT_GET, 'translationmanager_project_id', FILTER_SANITIZE_NUMBER_INT );

		$project = get_term( $project, 'translationmanager_project' );
		if ( ! $project instanceof \WP_Term ) {
			return $value;
		}

		( new \Translationmanager\View\Project\OrderInfo( $project->term_id ) )->render();

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

		$project_id = (int) filter_input( INPUT_POST, 'translationmanager_project_id', FILTER_SANITIZE_NUMBER_INT );
		$project    = get_term( $project_id, 'translationmanager_project' );

		if ( $project instanceof \WP_Term ) {
			$update = wp_update_term( $project->term_id, 'translationmanager_project', [
				'name'        => sanitize_text_field( filter_input( INPUT_POST, 'tag-name', FILTER_SANITIZE_STRING ) ),
				'description' => filter_input( INPUT_POST, 'description', FILTER_SANITIZE_STRING ),
			] );

			if ( is_wp_error( $update ) ) {
				TransientNoticeService::add_notice( esc_html__(
					'Something went wrong. Please go back and try again.', 'translationmanager'
				), 'warning' );
			} else {
				TransientNoticeService::add_notice( sprintf( esc_html__(
					'Project %s updated.', 'translationmanager'
				), '<strong>' . get_term_field( 'name', $project_id, 'translationmanager_project' ) . '</strong>' ), 'success' );
			}
		}

		if ( ! $project instanceof \WP_Term ) {
			TransientNoticeService::add_notice( esc_html__(
				'Invalid project ID, impossible to update the info.', 'translationmanager'
			), 'warning' );
		}

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
				'label'        => esc_html__( 'Projects', 'translationmanager' ),
				'labels'       => [
					'add_new_item' => esc_html__( 'Create new project', 'translationmanager' ),
				],
				'public'       => true,
				'capabilities' => [
					'manage_terms' => 'manage_options',
					'edit_terms'   => 'manage_options',
					'delete_terms' => 'manage_options',
					'assign_terms' => 'manage_options',
				],
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
	 * @param int $project The project from which retrieve the term indetifier.
	 *
	 * @return string
	 */
	public static function get_project_link( $project ) {

		return get_admin_url(
			null,
			add_query_arg( [
				'page'                          => 'translationmanager-project',
				'translationmanager_project_id' => $project,
				'post_type'                     => 'project_item',
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

	/**
	 * Edit Term Link for Project Taxonomy
	 *
	 * @since 1.0.0
	 *
	 * @param string $location The location link.
	 * @param int    $term_id  The term id.
	 * @param string $taxonomy The taxonomy name associated to the term.
	 *
	 * @return string The filtered location
	 */
	public function edit_term_link( $location, $term_id, $taxonomy ) {

		if ( 'translationmanager_project' === $taxonomy ) {
			$location = self::get_project_link( $term_id );
		}

		return $location;
	}
}