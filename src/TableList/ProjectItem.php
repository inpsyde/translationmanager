<?php
/**
 * Project Item Table List
 *
 * @since   1.0.0
 * @package Translationmanager\TableList
 */

namespace Translationmanager\TableList;

use function Translationmanager\Functions\get_project_items;

/**
 * Class ProjectItem
 *
 * @since   1.0.0
 * @package Translationmanager\TableList
 */
class ProjectItem extends \WP_List_Table {

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
	public function get_columns() {

		$posts_columns          = [];
		$posts_columns['cb']    = '<input type="checkbox" />';
		$posts_columns['title'] = esc_html__( 'Title', 'translationmanager' );

		$posts_columns = apply_filters( "manage_{$this->screen->id}_posts_columns", $posts_columns );

		return $posts_columns;
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
	public function column_default( $item, $column_name ) {

		do_action( "manage_{$this->screen->id}_custom_column", $column_name, $item->ID );
	}

	/**
	 * Column Title
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $item The post from which retrieve the title.
	 *
	 * @return string The post title
	 */
	public function column_title( $item ) {

		return '<strong>' . esc_html( $item->post_title ) . '</strong>';
	}

	/**
	 * @inheritdoc
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {

		if ( $primary !== $column_name ) {
			return '';
		}

		return $this->row_actions( apply_filters( 'project_item_row_actions', [], $item ) );
	}

	/**
	 * @inheritdoc
	 */
	protected function get_sortable_columns() {

		return [
			'title' => 'title',
		];
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

			$this->items = get_project_items( $term->term_id );
		}

		return $this->items;
	}
}
