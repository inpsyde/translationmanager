<?php

namespace Tm4mlp\Taxonomy;

class Project {
	const TAXONOMY = TM4MLP_TAX_PROJECT;

	const COL_ORDER_STATUS = 'tm4mlp_order_status';

	public static function register_post_status() {
	}

	public static function modify_columns( $columns ) {

		unset( $columns['cb'] );
		unset( $columns['slug'] );

		$columns['posts'] = '';

		// Add status ad second place.
		$columns = array_slice( $columns, 0, 1 )
		           + array( static::COL_ORDER_STATUS => __( 'Status', 'tm4mlp' ) )
		           + array_slice( $columns, 1 );

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
			case static::COL_ORDER_STATUS:
				if ( ! get_term_meta( $term_id, '_tm4mlp_order_id', true ) ) {
					return __( 'New', 'tm4mlp' );
				}

				return sprintf(
					__( 'Ordered at %s', 'tm4mlp' ),
					date( 'Y-m-d' )
				);
				break;

		}

		return $value;
	}
}