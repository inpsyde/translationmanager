<?php
/**
 * Project
 *
 * @since   1.0.0
 * @package Translationmanager\Taxonomy
 */

namespace Translationmanager\Taxonomy;

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
	const TAXONOMY = 'translationmanager_project';

	/**
	 * @since 1.0.0
	 */
	const COL_STATUS = 'translationmanager_order_status';

	/**
	 * @since 1.0.0
	 */
	const COL_ACTIONS = 'translationmanager_order_action';

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

		$columns['view'] = sprintf(
			'<a href="%s">%s</a>',
			self::get_project_link( $term->term_id ),
			esc_html__( 'View', 'translationmanager' )
		);

		return $columns;
	}

	/**
	 * Project Link
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id
	 *
	 * @return string
	 */
	public static function get_project_link( $term_id ) {

		return get_admin_url(
			null,
			'edit.php?' .
			http_build_query( [
				'translationmanager_project' => get_term_field( 'slug', $term_id ),
				'post_type'                  => 'project_item',
			] )
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

		add_action(
			'manage_' . static::TAXONOMY . '_custom_column',
			[ __CLASS__, 'print_column' ],
			10,
			3
		);

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