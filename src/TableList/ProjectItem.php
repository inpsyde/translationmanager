<?php
/**
 * Project Item Table List
 *
 * @since   1.0.0
 * @package Translationmanager\TableList
 */

namespace Translationmanager\TableList;

use Translationmanager\Functions;

/**
 * Class ProjectItem
 *
 * @since   1.0.0
 * @package Translationmanager\TableList
 */
final class ProjectItem extends TableList {

	/**
	 * Post Type
	 *
	 * @since 1.0.0
	 *
	 * @var \WP_Post_Type The post type to handle
	 */
	private $post_type;

	/**
	 * ProjectItem constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post_Type $post_type The post type to handle.
	 */
	public function __construct( \WP_Post_Type $post_type ) {

		$this->post_type = $post_type;

		parent::__construct( [
			'plural'   => esc_html__( 'Project', 'translationmanager' ),
			'singular' => esc_html__( 'Project', 'translationmanager' ),
			'ajax'     => false,
			'screen'   => $this->post_type->name,
		] );
	}

	/**
	 * @inheritdoc
	 */
	public function has_items() {

		return count( $this->items() );
	}

	/**
	 * @inheritdoc
	 */
	public function column_cb( $post ) {

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}
		?>

		<label class="screen-reader-text" for="cb-select-<?php echo intval( $post->ID ) ?>">
			<?php printf( esc_html__( 'Select %s', 'translationmanager' ), $post->post_title ); ?>
		</label>

		<input id="cb-select-<?php echo intval( $post->ID ) ?>"
		       type="checkbox"
		       name="post[]"
		       value="<?php echo intval( $post->ID ) ?>"/>

		<?php
	}

	/**
	 * @inheritdoc
	 */
	public function get_columns() {

		$columns = parent::get_columns();

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
	 * @return array The filtered sortable columns
	 */
	public function get_sortable_columns() {

		return [
			'translationmanager_added_by'               => 'translationmanager_added_by',
			'translationmanager_added_at'               => 'translationmanager_added_at',
			'translationmanager_target_language_column' => 'translationmanager_target_language_column',
		];
	}

	/**
	 * @inheritdoc
	 */
	protected function get_bulk_actions() {

		if ( current_user_can( 'manage_options' ) ) {
			$actions['trash'] = esc_html__( 'Remove from project', 'translationmanager' );
		}

		return $actions;
	}

	/**
	 * Fill the Items list with posts instances
	 *
	 * @since 1.0.0
	 *
	 * @return array A list of \WP_Post elements
	 */
	private function items() {

		if ( ! $this->items ) {
			$term = filter_input( INPUT_GET, 'translationmanager_project', FILTER_SANITIZE_STRING );

			if ( ! $term ) {
				return [];
			}

			$term = get_term_by( 'slug', $term, 'translationmanager_project' );

			if ( ! $term || is_wp_error( $term ) ) {
				return [];
			}

			$this->items = Functions\get_project_items( $term->term_id );
		}

		return $this->items;
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

		if ( isset( $request['post_status'] ) && 'trash' === $request['post_status'] ) {
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
	 * Project Column
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $item The post instance.
	 */
	protected function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'translationmanager_project':
				$terms = get_the_terms( $item->ID, 'translationmanager_project' );

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
					echo esc_html( $languages->get_label() );
					break;
				}

				// In case of failure.
				echo esc_html__( 'Unknown', 'translationmanager' );
				break;

			case 'translationmanager_target_language_column':
				$lang_id   = get_post_meta( $item->ID, '_translationmanager_target_id', true );
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
				$user = new \WP_User( get_post( $item->ID )->post_author );
				echo esc_html( esc_html( ucfirst( Functions\username( $user ) ) ) );
				break;

			case 'translationmanager_added_at':
				echo esc_html( get_the_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $item->ID ) );
				break;
		}
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
}
