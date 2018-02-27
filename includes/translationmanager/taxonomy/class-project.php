<?php

namespace Translationmanager\Taxonomy;

class Project {
	const TAXONOMY = 'translationmanager_project';

	const COL_STATUS = 'tmanager_order_status';
	const COL_ACTIONS = 'tmanager_order_action';

	public static function register_post_status() {
	}

	/**
	 * @param string[] $columns
	 * @param \WP_Term $term
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
	 * @param $term_id
	 *
	 * @return string
	 */
	public static function get_project_link( $term_id ) {

		return get_admin_url(
			null,
			'edit.php?' .
			http_build_query(
				array(
					'translationmanager_project' => get_term_field( 'slug', $term_id ),
					'post_type'                  => 'tmanager_cart',
				)
			)
		);
	}

	public static function modify_columns( $columns ) {

		unset( $columns['cb'] );
		unset( $columns['slug'] );
		unset( $columns['posts'] );

		// Add status ad second place.
		$columns = array_slice( $columns, 0, 1 )
		           + array( static::COL_STATUS => esc_html__( 'Status', 'translationmanager' ) )
		           + array_slice( $columns, 1 );

		$columns[ static::COL_ACTIONS ] = '';

		add_action(
			'manage_' . static::TAXONOMY . '_custom_column',
			array( __CLASS__, 'print_column' ),
			10,
			3
		);

		return $columns;
	}

	public static function print_column( $value, $column_name, $term_id ) {

		switch ( $column_name ) {
			case static::COL_STATUS:
				if ( ! get_term_meta( $term_id, '_tmanager_order_id', true ) ) {
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