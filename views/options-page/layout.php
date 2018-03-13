<div class="wrap">

	<h2 class="settings__headline">
		<?php esc_html_e( 'translationMANAGER', 'translationamanager' ); ?>

		<small class="settings__version">
			<sup><?php echo esc_html( \Translationmanager\Plugin::VERSION ); ?></sup>
		</small>
	</h2>


	<form method="post" action="options.php" class="inpsyde-form" id="inpsyde-form">

		<div id="inpsyde-tabs" class="inpsyde-tabs">
			<?php require_once \Translationmanager\Functions\get_template( '/views/options-page/navigation.php' ); ?>

			<div id="tab--connection" class="inpsyde-tab__content inpsyde-tabs--connection">
				<h3 class="screen-reader-text"><?php esc_html_e( 'Credentials', 'translationmanager' ); ?></h3>
				<?php include \Translationmanager\Functions\get_template( '/views/options-page/tabs/connection.php' ) ?>
			</div>

			<div id="tab--system-status" class="inpsyde-tab__content inpsyde-tabs--status">
				<h3 class="screen-reader-text"><?php esc_html_e( 'System Status', 'translationmanager' ); ?></h3>
				<?php ( new \Translationmanager\SystemStatus\Controller( new \Translationmanager\Plugin() ) )->render() ?>
			</div>

			<div id="tab--about" class="inpsyde-tab__content inpsyde-tabs--about">
				<h3 class="screen-reader-text"><?php esc_html_e( 'About', 'translationmanager' ); ?></h3>
				<?php include \Translationmanager\Functions\get_template( '/views/options-page/tabs/about.php' ) ?>
			</div>
		</div>

	</form>
</div>
