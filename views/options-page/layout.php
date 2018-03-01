<?php /** @var \Translationmanager\Admin\Options_Page $this */ ?>
<div class="wrap">

	<h2 class="settings__headline">
		<?php esc_html_e( 'translationMANAGER', 'translationamanager' ); ?>

		<small class="settings__version">
			<sup><?php echo esc_html( \Translationmanager\Plugin::VERSION ); ?></sup>
		</small>
	</h2>


	<form method="post" action="options.php" class="inpsyde-form" id="inpsyde-form">

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
				<h3 class="screen-reader-text"><?php esc_html_e( 'Credentials', 'translationmanager' ); ?></h3>
				<?php
				settings_fields( $this::OPTION_GROUP );
				do_settings_sections( 'translationmanager_api' );
				submit_button( esc_html__( 'Save changes', 'translationmanager' ), 'primary', 'save_action' );
				?>
			</div>

			<div id="tab--plugins" class="inpsyde-tab__content">
				<h3 class="screen-reader-text"><?php esc_html_e( 'Plugins', 'translationmanager' ); ?></h3>
				<em><?php esc_html_e( 'Enable or disable plugins here.', 'translationmanager' ); ?></em>
			</div>

			<div id="tab--languages" class="inpsyde-tab__content">
				<em><?php esc_html_e( 'Enable or disable languages here.', 'translationmanager' ); ?></em>
			</div>

		</div>

	</form>
</div>
