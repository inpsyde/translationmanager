<?php

class InpsydeCustomFunctions {

	public function __construct() {

	}

	public function init() {

		add_action( 'admin_head', [ $this, 'inpsyde_remove_search_box' ] );
		add_action( 'admin_menu', [ $this, 'inpsyde_translationmanager_settings_menu_item' ] );
		add_action( 'admin_menu', [ $this, 'inpsyde_translationmanager_about_page' ] );
		add_filter( 'plugin_row_meta', [ $this, 'inpsyde_euro_text_link_at_plugin_list' ], 10, 2 );
		add_filter( 'admin_footer_text', [ $this, 'admin_footer_text' ], 100 );
		add_filter( 'bulk_post_updated_messages', [ $this, 'bulk_post_updated_messages' ], 10, 2 );
		add_filter( 'get_edit_term_link', [ $this, 'inpsyde_get_edit_term_link' ], 10, 4 );
		add_action( 'manage_posts_extra_tablenav', [ $this, 'restrict_manage_posts' ], 10 );
		add_action( 'admin_post_translationmanager_project_info_save', [
			$this,
			'translationmanager_project_info_save',
		] );
		// For 'post' post type.
		add_filter( 'bulk_actions-edit-post', [ $this, 'translate_bulk_actions' ] );
		add_filter( 'handle_bulk_actions-edit-post', [ $this, 'bulk_translate_action_handler' ], 10, 3 );
		// For 'page' post type.
		add_filter( 'bulk_actions-edit-page', [ $this, 'translate_bulk_actions' ] );
		add_filter( 'handle_bulk_actions-edit-page', [ $this, 'bulk_translate_action_handler' ], 10, 3 );
	}

	public function restrict_manage_posts( $which ) {

		if ( 'top' === $which ) { ?>
			<script type="text/javascript">
				(function ( $ ) {
					$( function () {
						$( '.tablenav .actions.bulkactions' ).on( 'click', '.button.action', function ( e ) {
							var selectVal = $( this ).prev( 'select' ).val();
							if ( 'bulk_translate' === selectVal ) {
								e.preventDefault();
								var checked = $( "input[name='post[]']" ).is( ':checked' );
								if ( ! checked ) {
									alert( 'You must need to select at least one element for translate.' );
								} else {
									var buttonID = $( this ).attr( 'id' );
									if ( 'doaction' === buttonID ) {
										$( this ).parent().parent().find( '.translationmanager-language-overlay' ).css( 'visibility', 'visible' ).css( 'opacity', '1' );
									} else {
										$( this ).parent().parent().parent().find( '.tablenav.top' ).find( '.translationmanager-language-overlay' ).css( 'visibility', 'visible' ).css( 'opacity', '1' );
									}
									//

								}
								return true;
							}
						} );

						$( '.translationmanager-language-overlay .translationmanager-lang-popup' ).on( 'click', '.close', function () {
							$( this ).parent().parent().removeAttr( 'style' );
						} );

						$( '#translationmanager-lang-wrap-div' ).on( 'change', 'input[type=checkbox]', function () {
							var inputStatus = $( this ).parent().find( 'input[name="translationmanager_bulk_languages[]"]' ).is( ':checked' );

							if ( ! inputStatus ) {
								$( "#translationmanager-submit-bulk-translate" ).attr( "disabled", true );
							} else {
								$( "#translationmanager-submit-bulk-translate" ).attr( "disabled", false );
							}
							return true;
						} );
					} );
				})( jQuery )
			</script>
			<style>
				.translationmanager-language-overlay {
					position:   fixed;
					top:        0;
					bottom:     0;
					left:       0;
					right:      0;
					background: rgba(0, 0, 0, 0.7);
					transition: opacity 500ms;
					visibility: hidden;
					opacity:    0;
				}

				.translationmanager-lang-popup {
					margin:     70px auto;
					padding:    20px;
					background: #fff;
					width:      30%;
					position:   relative;
					transition: all 5s ease-in-out;
				}

				.translationmanager-lang-popup h2 {
					margin:      10px auto;
					color:       #333;
					font-family: Tahoma, Arial, sans-serif;
				}

				.translationmanager-lang-popup .close {
					position:        absolute;
					top:             20px;
					right:           30px;
					transition:      all 200ms;
					font-size:       30px;
					font-weight:     bold;
					text-decoration: none;
					color:           #333;
				}

				.translationmanager-lang-popup .content {
					max-height: 30%;
					overflow:   auto;
				}

				@media screen and (max-width: 700px) {
					.box {
						width: 70%;
					}

					.translationmanager-lang-popup {
						width: 70%;
					}
				}
			</style>

			<div class="translationmanager-language-overlay">
				<div class="translationmanager-lang-popup">
					<a class="close" href="#">&times;</a>
					<div class="content">

						<div id="translationmanager-project-wrap-div">
							<h2>Select Projects:</h2>
							<select name="translationmanager-projects" id="translationmanager-">
								<option value="-1"><?php _e( 'New project', 'translationmanager' ) ?></option>
								<?php foreach ( $this->get_projects() as $project_id => $project_label ): ?>
									<option value="<?php esc_attr_e( $project_id ) ?>">
										<?php esc_html_e( $project_label ) ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div id="translationmanager-lang-wrap-div">
							<h2>Select languages:</h2>
							<?php foreach ( $this->get_languages( get_current_blog_id() ) as $lang_key => $lang ): ?>
								<input type="checkbox" name="translationmanager_bulk_languages[]"
								       value="<?php echo $lang_key ?>"><?php echo $lang->get_label() ?><br>
							<?php endforeach; ?>
						</div>

						<?php
						submit_button(
							'Bulk Translate',
							'primary',
							'translationmanager-submit-bulk-translate',
							true,
							[
								'id'       => 'translationmanager-submit-bulk-translate',
								'disabled' => 'true',
							]
						);
						?>
					</div>
				</div>
			</div>
			<?php
		}
	}

	public function get_languages( $site_id ) {

		global $wpdb;
		$languages      = [];
		$site_relations = new \Mlp_Site_Relations( $wpdb, 'mlp_site_relations' );

		$sites = $site_relations->get_related_sites( $site_id );

		foreach ( $sites as $site ) {
			$lang_iso = mlp_get_blog_language( $site, false );

			$languages[ $site ] = new Translationmanager\Domain\Language( $lang_iso, mlp_get_lang_by_iso( $lang_iso ) );
		}

		return $languages;
	}

	public function inpsyde_remove_search_box() {

		$screen = get_current_screen();
		if ( 'edit' == $screen->base
		     && isset( $_GET['translationmanager_project'] )
		     && isset( $_GET['post_type'] )
		     && 'tmanager_cart' == $_GET['post_type']
		) {
			echo '<style type="text/css">.post-type-tmanager_cart #posts-filter .search-box {display: none !important;}</style>';
		}

		if ( 'edit-tags' == $screen->base
		     && isset( $_GET['taxonomy'] )
		     && 'translationmanager_project' == $_GET['taxonomy']
		     && isset( $_GET['post_type'] )
		     && 'tmanager_cart' == $_GET['post_type']
		) {
			echo '
			<style type="text/css">
				.post-type-tmanager_cart .row-actions span.edit, 
				.post-type-tmanager_cart .row-actions span.inline.hide-if-no-js, 
				.post-type-tmanager_cart .row-actions span.view {display: none !important;}
			</style>
			';
		}
	}

	/**
	 * add external link to Translation area
	 */
	public function inpsyde_translationmanager_settings_menu_item() {

		global $submenu;
		unset( $submenu['edit.php?post_type=tmanager_cart'][5] );
		$url                                           = 'options-general.php?page=translationmanager_settings';
		$submenu['edit.php?post_type=tmanager_cart'][] = array( 'Settings', 'manage_options', $url );
	}

	/**
	 * Adds a submenu page under a custom post type parent.
	 */
	public function inpsyde_translationmanager_about_page() {

		add_submenu_page(
			'edit.php?post_type=tmanager_cart',
			__( 'About', 'tmanager_cart' ),
			__( 'About', 'tmanager_cart' ),
			'manage_options',
			'inpsyde-translationmanager-about',
			array( $this, 'inpsyde_translationmanager_about_page_callback' )
		);
	}

	/**
	 * Display callback for the submenu page.
	 */
	public function inpsyde_translationmanager_about_page_callback() {

		?>
		<style type="text/css" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
			#wpfooter .inpsyde_logo_translationmanager {
				background:   url(<?php echo plugins_url( '../../public/img/inpsyde.png', __FILE__ ) ?>) no-repeat;
				margin-top:   5px;
				margin-right: 15px;
				height:       25px;
				width:        80px;
				text-indent:  -9999em;
				display:      inline-block;
				line-height:  25px;
			}

			table.eurotext-table {
				width: 100%;
			}

			table.eurotext-table td.lefttd {
				text-align: right;
				width:      50%;
			}

			table.eurotext-table td.righttd {
				text-align: left;
				width:      50%;
			}

			a.eurotext_support {
				background:      orange;
				padding:         12px 50px;
				color:           white;
				text-decoration: none;
				font-weight:     bolder;
				margin-right:    15px;
			}

			a.eurotext_logo_translationmanager {
				margin-right: 15px;
			}

			#translationmanager-about .backwpup-banner-img {
				display:   block;
				height:    auto;
				margin:    26px auto;
				max-width: 100%;
			}

			#translationmanager-about .welcome {
				background: #fff;
			}

			#translationmanager-about .welcome .welcome_inner {
				margin:    0 auto;
				max-width: 960px;
			}

			#translationmanager-about .welcome .welcome_inner .welcometxt {
				margin-bottom:  40px;
				overflow:       hidden;
				border-bottom:  1px #ccc dotted;
				text-align:     center;
				padding-bottom: 25px;
				position:       relative;
			}

			#translationmanager-about .welcome .welcome_inner h1 {
				font-size: 42px;
				padding:   15px;
			}

			#translationmanager-about .welcome .welcome_inner .welcometxt p {
				line-height: 20px;
				font-size:   18px;
			}

		</style>
		<div id="translationmanager-about" class="wrap">
			<div class="welcome">
				<div class="welcome_inner">
					<div class="welcometxt">
						<div class="backwpup-welcome">
							<img class="backwpup-banner-img"
							     src="<?php echo plugins_url( '../../public/img/Workflow_Industrie_EN.jpg', __FILE__ ) ?>"/>
							<h1><?php esc_html_e( 'Welcome to translationMANAGER for WordPress', 'translationmanager' ); ?></h1>
							<p><?php esc_html_e( 'With the translationMANAGER, exporting content from your WordPress Multisite is simple and straightforward. Select posts, pages and customized content (custom post types) with just a few clicks and send the texts to the Eurotext Translation Portal. Once translated, texts can be reimported just as quickly and then immediately published. All of this from the comfort of the WordPress admin area. Professional translations with WordPress have never been so simple!', 'translationmanager' ); ?></p>
							<p><?php esc_html_e( 'Eurotext works together with more than 4,000 native-speaking translators working in over 50 languages. Modern translation technology and automated quality control processes allow us to offer high-quality translations at fair prices. See for yourself!', 'translationmanager' ) ?></p>
						</div>
						<table class="eurotext-table">
							<tbody>
							<tr>
								<td class="lefttd">
									<a href="https://eurotext.de/" class="eurotext_logo_translationmanager"
									   title="Eurotext AG"><img
											src="<?php echo plugins_url( '../../public/img/Eurotext_AG_72dpi_250.png', __FILE__ ) ?>"></a>
								</td>
								<td class="righttd">
									Eurotext AG</br>
									Schürerstraße 3</br>
									97080 Würzburg</br>
									Germany</br>
								</td>
							</tr>
							<tr>
								<td class="lefttd" style="vertical-align: top;">
									<a href="https://eurotext-ecommerce.com/translationmanager-dokumentation-support/"
									   class="eurotext_support" title="">Documentation</a>
								</td>
								<td class="righttd">
									Phone: +49 (0)931 35 40 50</br>
									Telefax: +49 (0)931 35 40 580</br>
									E-Mail: info@eurotext.de</br>
									Web: www.eurotext.de/en/
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

		if ( strpos( $file, TRANSLATIONMANAGER_FILENAME ) !== false ) {
			$links[1] = 'By <a href="https://eurotext.de/">Eurotext AG</a> & <a href="https://inpsyde.com/">Inpsyde GmbH</a>';
		}

		return $links;
	}

	/**
	 * Overrides WordPress text in Footer
	 *
	 * @param $admin_footer_text string
	 *
	 * @return string
	 */
	public function admin_footer_text( $admin_footer_text ) {

		$default_text = $admin_footer_text;

		if ( isset( $_REQUEST['page'] ) && strstr( $_REQUEST['page'], 'inpsyde-translationmanager-about' ) ) {
			$admin_footer_text = '<a href="http://inpsyde.com" class="inpsyde_logo_translationmanager" title="Inpsyde GmbH">Inpsyde GmbH</a></br>';

			return $admin_footer_text . $default_text;
		}

		return $admin_footer_text;
	}

	/* Filter post updated messages for custom post types. */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {

		$bulk_messages['tmanager_cart'] = array(
			'updated'   => _n( '%s translation updated.', '%s translations updated.', $bulk_counts['updated'] ),
			'locked'    => _n( '%s translation not updated, somebody is editing it.', '%s translations not updated, somebody is editing them.', $bulk_counts['locked'] ),
			'deleted'   => _n( '%s translation permanently deleted.', '%s translations permanently deleted.', $bulk_counts['deleted'] ),
			'trashed'   => _n( '%s translation removed from the project.', '%s translations removed from the project.', $bulk_counts['trashed'] ),
			'untrashed' => _n( '%s translation restored at the project.', '%s translations restored at the project.', $bulk_counts['untrashed'] ),
		);

		return $bulk_messages;

	}


	public function inpsyde_get_edit_term_link( $location, $term_id, $taxonomy, $object_type ) {

		if ( 'translationmanager_project' === $taxonomy ) {
			$location = Translationmanager\Taxonomy\Project::get_project_link( $term_id );
		}

		return $location;
	}

	public function translationmanager_project_info_save() {

		if ( 'translationmanager_project_info_save' != $_POST['action'] ) {
			return;
		}
		$term = get_term_by( 'slug', $_POST['_translationmanager_project_id'], 'translationmanager_project' );

		$update = wp_update_term( $term->term_id, 'translationmanager_project', array(
			'name'        => $_POST['tag-name'],
			'description' => $_POST['description'],
		) );

		if ( is_wp_error( $update ) ) {
			wp_die( __( 'Something went wrong. Please go back and try again.', 'translationmanager' ) );
		}

		wp_safe_redirect( wp_get_referer() );

		return;
	}

	public function translate_bulk_actions( $actions ) {

		$actions['bulk_translate'] = __( 'Bulk Translate', 'translationmanager' );

		return $actions;
	}


	public function get_projects() {

		/** @var \WP_Term[] $terms */
		$terms = get_terms(
			array(
				'taxonomy'   => 'translationmanager_project',
				'hide_empty' => false,
				'meta_query' => array(
					array(
						'key'     => '_tmanager_order_id',
						'compare' => 'NOT EXISTS',
						'value'   => '',
					),
				),
			)
		);

		$projects = array();
		foreach ( $terms as $term ) {
			$projects[ $term->term_id ] = $term->name;
		}

		return $projects;
	}

	/**
	 * Handles the bulk action.
	 */
	public function bulk_translate_action_handler( $redirect_to, $action, $post_ids ) {

		if ( $action !== 'bulk_translate' || empty( $post_ids ) || ! isset( $_GET['translationmanager_bulk_languages'] ) ) {
			return $redirect_to;
		}

		$languages = $_GET['translationmanager_bulk_languages'];
		$project   = (int) $_GET['translationmanager-projects'];

		//print_r($languages);die();

		$handler = new Translationmanager\Admin\Handler\Project_Handler;

		if ( '-1' == $project ) {
			$project = (int) $handler->create_project(
				sprintf( __( 'Project %s', 'translationmanager' ), date( 'Y-m-d H:i:s' ) )
			);
		}

		// Iterate translations
		foreach ( $post_ids as $post_id ) {
			foreach ( $languages as $lang_id ) {
				$handler->add_translation( $project, $post_id, $lang_id );
			}
		}

		$redirect_to = Translationmanager\Taxonomy\Project::get_project_link( $project );;

		return $redirect_to;

	}
}

$InpsydeCustomFunctions = new InpsydeCustomFunctions;
$InpsydeCustomFunctions->init();