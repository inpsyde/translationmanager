<?php

class InpsydeCustomFunctions {

	public function __construct() {

	}

	public function init() {
		add_action( 'admin_head', array( $this, 'inpsyde_remove_search_box' ) );
		add_action( 'admin_menu', array( $this, 'inpsyde_tmwp_settings_menu_item' ) );
		add_action( 'admin_menu', array( $this, 'inpsyde_tmwp_about_page' ) );
		add_filter( 'plugin_row_meta', array( $this, 'inpsyde_euro_text_link_at_plugin_list' ), 10, 2 );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 100 );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );
		add_filter( 'get_edit_term_link', array( $this, 'inpsyde_get_edit_term_link' ), 10, 4 );
		add_action( 'manage_posts_extra_tablenav', array( $this, 'restrict_manage_posts' ), 10 );
		add_action( 'admin_post_tmwp_project_info_save', array( $this, 'tmwp_project_info_save' ) );
	}

	public function restrict_manage_posts( $which ) {
		if( 'page' === $_GET['post_type'] && 'top' === $which ) { ?>
			<?php add_thickbox(); ?>

			<script type="text/javascript">
				(function($){
					$(function(){
						// Code for making the button dependable on checkboxes.
						$( ".check-column" ).on('change', "input[type='checkbox']", function( e ) {
							var checked = $("input[name='post[]']").is(':checked');
							if(!checked) {
								$("#translate_bulk_pages").attr("disabled", true);
							} else {
								$("#translate_bulk_pages").attr("disabled", false);
							}
							return true;
						});

						// Code for making the button prevent default on checkboxes.
						$("#translate_bulk_pages").on( 'click', function( e ) {
							var disabled = $(this).attr('disabled');
							if(disabled) {
								e.preventDefault();
								return false;
							}
							return true;
						})
					});
				})(jQuery)
			</script>

			<div id="my-content-id" style="display:none;">

			</div>

			<div class="alignleft actions translate_button">
				<a href="#TB_inline?width=600&height=550&inlineId=modal-window-id" id="translate_bulk_pages" class="button thickbox" disabled>Translate Bulk Post</a>
			</div>
			<?php
		}
	}

	public function inpsyde_remove_search_box() {
		$screen = get_current_screen();
		if( 'edit' == $screen->base
		    && isset($_GET['tmwp_project'])
		    && isset($_GET['post_type'])
		    && 'tmwp_cart' == $_GET['post_type']
		) {
			echo '<style type="text/css">.post-type-tmwp_cart #posts-filter .search-box {display: none !important;}</style>';
		}

		if( 'edit-tags' == $screen->base
		    && isset($_GET['taxonomy'])
		    && 'tmwp_project' == $_GET['taxonomy']
		    && isset($_GET['post_type'])
		    && 'tmwp_cart' == $_GET['post_type']
		) {
			echo '
			<style type="text/css">
				.post-type-tmwp_cart .row-actions span.edit, 
				.post-type-tmwp_cart .row-actions span.inline.hide-if-no-js, 
				.post-type-tmwp_cart .row-actions span.view {display: none !important;}
			</style>
			';
		}
	}

	/**
	 * add external link to Translation area
	 */
	public function inpsyde_tmwp_settings_menu_item() {
		global $submenu;
		unset($submenu['edit.php?post_type=tmwp_cart'][5]);
		$url = 'options-general.php?page=tmwp';
		$submenu['edit.php?post_type=tmwp_cart'][] = array('Settings', 'manage_options', $url);
	}

	/**
	 * Adds a submenu page under a custom post type parent.
	 */
	public function inpsyde_tmwp_about_page() {
		add_submenu_page(
			'edit.php?post_type=tmwp_cart',
			__( 'About', 'tmwp_cart' ),
			__( 'About', 'tmwp_cart' ),
			'manage_options',
			'inpsyde-tmwp-about',
			array( $this, 'inpsyde_tmwp_about_page_callback' )
		);
	}

	/**
	 * Display callback for the submenu page.
	 */
	public function inpsyde_tmwp_about_page_callback() {
		?>
		<style type="text/css" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
			#wpfooter .inpsyde_logo_tmwp {
				background: url(<?php echo plugins_url( '../../public/img/inpsyde.png', __FILE__ ) ?>) no-repeat;
				margin-top: 5px;
				margin-right: 15px;
				height: 25px;
				width: 80px;
				text-indent: -9999em;
				display: inline-block;
				line-height: 25px;
			}


			table.eurotext-table {
				width: 100%;
			}

			table.eurotext-table td.lefttd{
				text-align: right;
				width: 50%;
			}

			table.eurotext-table td.righttd {
				text-align: left;
				width: 50%;
			}

			a.eurotext_support {
				background: orange;
				padding: 12px 50px;
				color: white;
				text-decoration: none;
				font-weight: bolder;
				margin-right: 15px;
			}

			a.eurotext_logo_tmwp {
				margin-right: 15px;
			}

			#tmwp-about .backwpup-banner-img {
				display: block;
				height: auto;
				margin: 26px auto;
				max-width: 100%;
			}
			#tmwp-about .welcome {
				background: #fff;
			}

			#tmwp-about .welcome .welcome_inner {
				margin: 0 auto;
				max-width: 960px;
			}

			#tmwp-about .welcome .welcome_inner .welcometxt {
				margin-bottom: 40px;
				overflow: hidden;
				border-bottom: 1px #ccc dotted;
				text-align: center;
				padding-bottom: 25px;
				position: relative;
			}

			#tmwp-about .welcome .welcome_inner h1 {
				font-size: 42px;
				padding: 15px;
			}

			#tmwp-about .welcome .welcome_inner .welcometxt p {
				line-height: 20px;
				font-size: 18px;
			}

		</style>
		<div id="tmwp-about" class="wrap">
			<div class="welcome">
				<div class="welcome_inner">
					<div class="welcometxt">
						<div class="backwpup-welcome">
							<img class="backwpup-banner-img" src="<?php echo plugins_url( '../../public/img/Workflow_Industrie_EN.jpg', __FILE__ ) ?>" />
							<h1><?php esc_html_e( 'Welcome at translationMANAGER for WordPress', 'translationmanager' ); ?></h1>
							<p><?php esc_html_e( 'With the translationMANAGER for WordPress, you can export content from your online store and send it directly to the Eurotext Translation Portal with just a few clicks. It can be used for everything from product descriptions and SEO content to frameworks and other types of text. Once translated and subjected to our integrated quality assurance check, your content is transmitted back to your online store where it can immediately be published. The translationMANAGER is available for free for a large number of shop systems.', 'translationmanager' );?></p>
							<p><?php esc_html_e('Our REST API allows you to integrate processes for multilingualism and internationalization directly into your system landscape, providing you with a tailor-made solution for fast, efficient processes. We work closely with your technology and content team to achieve this integration.', 'translationmanager')?></p>
						</div>
						<table class="eurotext-table">
							<tbody>
								<tr>
									<td class="lefttd">
										<a href="https://eurotext.de/" class="eurotext_logo_tmwp" title="Eurotext AG"><img src="<?php echo plugins_url( '../../public/img/Eurotext_AG_72dpi_250.png', __FILE__ ) ?>"></a>
									</td>
									<td class="righttd">
										Eurotext AG</br>
										Schürerstraße 3</br>
										97080 Würzburg</br>
										Deutschland</br>
									</td>
								</tr>
								<tr>
									<td class="lefttd" style="vertical-align: top;">
										<a href="https://eurotext-ecommerce.com/translationmanager-dokumentation-support/" class="eurotext_support" title="">Open Help & Support</a>
									</td>
									<td class="righttd">
										Phone: +49 (0)931 35 40 50</br>
										Telefax: +49 (0)931 35 40 580</br>
										E-Mail: info@eurotext.de</br>
										Web: www.eurotext.de
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function inpsyde_euro_text_link_at_plugin_list( $links, $file ) {
		if ( strpos( $file, TMWP_FILENAME ) !== false ) {
			$links[1] = 'By <a href="https://eurotext.de/">Eurotext AG</a> & <a href="https://inpsyde.com/">Inpsyde GmbH</a>';
		}
		return $links;
	}

	/**
	 * Overrides WordPress text in Footer
	 *
	 * @param $admin_footer_text string
	 * @return string
	 */
	public function admin_footer_text( $admin_footer_text ) {

		$default_text = $admin_footer_text;

		if ( isset( $_REQUEST[ 'page' ] ) && strstr( $_REQUEST[ 'page' ], 'inpsyde-tmwp-about' ) ) {
			$admin_footer_text = '<a href="http://inpsyde.com" class="inpsyde_logo_tmwp" title="Inpsyde GmbH">Inpsyde GmbH</a></br>';

			return $admin_footer_text . $default_text;
		}

		return $admin_footer_text;
	}

	/* Filter post updated messages for custom post types. */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {

		$bulk_messages['tmwp_cart'] = array(
			'updated'   => _n( '%s translation updated.', '%s translations updated.', $bulk_counts['updated'] ),
			'locked'    => _n( '%s translation not updated, somebody is editing it.', '%s translations not updated, somebody is editing them.', $bulk_counts['locked'] ),
			'deleted'   => _n( '%s translation permanently deleted.', '%s translations permanently deleted.', $bulk_counts['deleted'] ),
			'trashed'   => _n( '%s translation removed from the project.', '%s translations removed from the project.', $bulk_counts['trashed'] ),
			'untrashed' => _n( '%s translation restored at the project.', '%s translations restored at the project.', $bulk_counts['untrashed'] ),
		);

		return $bulk_messages;

	}


	public function inpsyde_get_edit_term_link( $location, $term_id, $taxonomy, $object_type ) {
		if ( 'tmwp_project' === $taxonomy ) {
			$location = Tmwp\Taxonomy\Project::get_project_link( $term_id );
		}
		return $location;
	}

	public function tmwp_project_info_save() {
		if( 'tmwp_project_info_save' != $_POST['action'] ) {
			return;
		}
		$term = get_term_by( 'slug', $_POST['_tmwp_project_id'], TMWP_TAX_PROJECT );

		$update = wp_update_term( $term->term_id, TMWP_TAX_PROJECT, array(
			'name'          => $_POST['tag-name'],
			'description'   => $_POST['description']
		) );

		if ( is_wp_error( $update ) ) {
			wp_die('Something went wrong. Please go back and try again.');
		}

		wp_safe_redirect( wp_get_referer() );
		return;
	}
}

$InpsydeCustomFunctions = new InpsydeCustomFunctions;
$InpsydeCustomFunctions->init();