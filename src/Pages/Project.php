<?php
/**
 * Page Projects
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */

namespace Translationmanager\Pages;

use Translationmanager\TableList\ProjectItem;

/**
 * Class Projects
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */
class Project implements Page {

	/**
	 * Page Slug
	 *
	 * @since 1.0.0
	 *
	 * @var string The page slug
	 */
	const SLUG = 'translationmanager-project';

	/**
	 * The Page Title
	 *
	 * @since 1.0.0
	 *
	 * @var string The page title
	 */
	private $page_title;

	/**
	 * Project constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->page_title = __( 'Project', 'translationmanager' );
	}

	/**
	 * Set Hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'admin_menu', [ $this, 'add_page' ] );
		add_action( 'admin_title', [ $this, 'reintroduce_page_title_in_header' ] );
	}

	/**
	 * Reintroduce Page Title in Header
	 *
	 * Since we are adding an hidden page the `<title>` tag will miss the page title.
	 *
	 * @since 1.0.0
	 *
	 * @param string $admin_title The page title in the admin.
	 *
	 * @return string The page title
	 */
	public function reintroduce_page_title_in_header( $admin_title ) {

		if ( $this->is_project_item_cpt() ) {
			$admin_title = $this->page_title . ' ' . $admin_title;
		}

		return $admin_title;
	}

	/**
	 * @inheritdoc
	 */
	public function add_page() {

		global $submenu;

		add_submenu_page(
			null,
			esc_html__( 'Project', 'translationmanager' ),
			esc_html__( 'Project', 'translationmanager' ),
			'manage_options',
			self::SLUG,
			[ $this, 'render_template' ]
		);

		add_submenu_page(
			'translationmanager',
			esc_html__( 'Projects', 'translationmanager' ),
			esc_html__( 'Projects', 'translationmanager' ),
			'manage_options',
			'translationmanager-project',
			'__return_false'
		);

		$submenu['translationmanager'][0][2] = admin_url( // phpcs:ignore
			'edit-tags.php?taxonomy=translationmanager_project&post_type=project_item'
		);
	}

	/**
	 * @inheritdoc
	 */
	public function render_template() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'translatemanager' ) );
		}

		$this->requires();

		$bind = (object) [
			'page_title'    => $this->page_title,
			'wp_list_table' => new ProjectItem( get_post_type_object( 'project_item' ) ),
		];

		require_once \Translationmanager\Functions\get_template( '/views/page-project.php' );

		unset( $bind );
	}

	/**
	 * @inheritdoc
	 */
	public static function url() {

		return menu_page_url( self::SLUG, false );
	}

	/**
	 * Requires Additional Stuffs
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function requires() {

		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
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
