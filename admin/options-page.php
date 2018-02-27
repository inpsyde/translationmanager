<?php /** @var \Translationmanager\Admin\Options_Page $this */ ?>
<div class="wrap">

	<h2 class="settings__headline">
		<?php esc_html_e( 'translationMANAGER' ) ?>
		<small class="settings__version">
			<sup>1.0.0</sup>
		</small>
	</h2>


	<form method="post" action="options.php" class="inpsyde-form" id="inpsyde-form">


		<?php /* the tab navigation */ ?>
		<div id="inpsyde-tabs" class="inpsyde-tabs">
			<ul class="inpsyde-tab__navigation wp-clearfix">
				<li class="inpsyde-tab__navigation-item">
					<a href="#tab--connection"><?php esc_html_e( 'Connection', 'translationmanager' ); ?></a>
				</li>

				<li class="inpsyde-tab__navigation-item">
					<a href="#tab--plugins"><?php esc_html_e( 'Plugins', 'translationmanager' ); ?></a>
				</li>

				<li class="inpsyde-tab__navigation-item">
					<a href="#tab--languages"><?php esc_html_e( 'Languages', 'translationmanager' ); ?></a>
				</li>
			</ul>

			<div id="tab--connection" class="inpsyde-tab__content">
				<h3 class="screen-reader-text">Credentials</h3>
				<?php settings_fields( $this::OPTION_GROUP ); ?>
				<?php
				do_settings_sections( 'translationmanager_api' );
				submit_button( esc_html__( 'Save changes', 'translationmanager' ), 'primary', 'save_action' );
				?>
			</div>

			<div id="tab--plugins" class="inpsyde-tab__content">
				<h3 class="screen-reader-text">Plugins</h3>
				<em>Enable or disable plugins here.</em>
			</div>

			<div id="tab--languages" class="inpsyde-tab__content">
				<em>Enable or disable languages here.</em>
			</div>

		</div>

	</form>
</div>

<script>
	jQuery(
		function( $ ) {
			"use strict";
			$( "#inpsyde-tabs" ).tabs( {
				activate: function( event, ui ) {
					var $form = $( '#inpsyde-form' ),
						$anchor = event.currentTarget,
						hash = $anchor.getAttribute( 'href' ),
						action = $form.attr( 'action' ).split( '#' )[ 0 ];

					$form.attr( 'action', action + hash );
				}
			} );
		}
	);
</script>