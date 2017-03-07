<?php

namespace Tm4mlp\Post_Type;

class Project_Item {
	const STATUS_TRASH = 'trash';

	const POST_TYPE = 'tm4mlp_cart';

	const COLUMN_PROJECT = 'tm4mlp_project';

	const COLUMN_LANGUAGE = 'tm4mlp_language';

	public static function register_post_status() {
	}

	public static function modify_columns( $columns ) {
		if ( ! static::is_subject() ) {
			return $columns;
		}

		unset( $columns['date'] );

		$columns = static::_column_project( $columns );
		$columns = static::_column_language( $columns );

		add_action(
			'manage_' . static::POST_TYPE . '_posts_custom_column',
			array( __CLASS__, 'print_column' ),
			10,
			2
		);

		return $columns;
	}

	public static function is_subject() {
		return get_current_screen()
		       && static::POST_TYPE == get_current_screen()->post_type;
	}

	public static function _column_project( $columns ) {
		$request = wp_parse_args(
			$_GET,
			array(
				TM4MLP_TAX_PROJECT => null,
			)
		); // Input var ok.

		if ( static::STATUS_TRASH == $request['post_status']
		) {
			// This is trash so we show no project column.
			return $columns;
		}

		if ( $request[ TM4MLP_TAX_PROJECT ] ) {
			// Term/Project filter is active so this col is not needed.
			return $columns;
		}

		$columns[ self::COLUMN_PROJECT ] = __( 'Project', 'tm4mlp' );

		return $columns;
	}

	protected static function _column_language( $columns ) {
		$columns[ self::COLUMN_LANGUAGE ] = __( 'Target language', 'tm4mlp' );

		return $columns;
	}

	public static function print_column( $column_name, $post_id ) {
		switch ( $column_name ) {
			case static::COLUMN_PROJECT:
				$terms = get_the_terms( $post_id, TM4MLP_TAX_PROJECT );

				if ( ! $terms ) {
					break;
				}

				foreach ( $terms as $term ) {
					/** @var \WP_Term $term */
					printf(
						'<a href="%s">%s</a>',
						'edit.php?' .
						http_build_query(
							array(
								TM4MLP_TAX_PROJECT => $term->slug,
								'post_type'        => TM4MLP_CART
							)
						),
						$term->name
					);
				}
				break;
			case static::COLUMN_LANGUAGE:
				$lang_id = get_post_meta( $post_id, '_tm4mlp_target_id', true );

				if ( ! $lang_id ) {
					// TODO error handling.
					return;
				}

				$languages = tm4mlp_get_languages();

				if ( ! isset( $languages[ $lang_id ] ) ) {
					// TODO error handling.
					return;
				}

				echo $languages[ $lang_id ]->get_label();

				break;
		}
	}
}