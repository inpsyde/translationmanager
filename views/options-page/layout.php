<div class="wrap">

	<h2 class="settings__headline">
		<?php esc_html_e( 'translationMANAGER', 'translationamanager' ); ?>

		<small class="settings__version">
			<sup><?php echo esc_html( \Translationmanager\Plugin::VERSION ); ?></sup>
		</small>
	</h2>

	<form id="inpsyde-form"
	      class="inpsyde-form"
	      method="post"
	      action="<?php echo esc_url( $this->action() ) ?>">

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

				<li class="inpsyde-tab__navigation-item">
					<a href="#tab--about"><?php esc_html_e( 'About', 'translationmanager' ); ?></a>
				</li>
			</ul>

			<div id="tab--connection" class="inpsyde-tab__content inpsyde-tabs--connection">
				<h3 class="screen-reader-text"><?php esc_html_e( 'Credentials', 'translationmanager' ); ?></h3>
				<?php include \Translationmanager\Functions\get_template( '/views/options-page/tabs/connection.php' ) ?>
			</div>

			<div id="tab--plugins" class="inpsyde-tab__content inpsyde-tabs--plugins">
				<h3 class="screen-reader-text"><?php esc_html_e( 'Plugins', 'translationmanager' ); ?></h3>
				<em><?php esc_html_e( 'Enable or disable plugins here.', 'translationmanager' ); ?></em>
			</div>

			<div id="tab--languages" class="inpsyde-tab__content inpsyde-tabs--languages">
				<h3 class="screen-reader-text"><?php esc_html_e( 'Languages', 'translationmanager' ); ?></h3>
				<em><?php esc_html_e( 'Enable or disable languages here.', 'translationmanager' ); ?></em>
			</div>

			<div id="tab--about" class="inpsyde-tab__content inpsyde-tabs--about">
				<h3 class="screen-reader-text"><?php esc_html_e( 'About', 'translationmanager' ); ?></h3>
				<?php include \Translationmanager\Functions\get_template( '/views/options-page/tabs/about.php' ) ?>
			</div>

		</div>

		<?php include \Translationmanager\Functions\get_template( '/views/type/nonce.php' ) ?>

	</form>
</div>
