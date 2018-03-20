<div class="wrap">

	<h1 class="settings__headline">
		<?php esc_html_e( 'translationMANAGER', 'translationamanager' ); ?>
		s
		<small class="settings__version">
			<sup><?php echo esc_html( ( new \Translationmanager\Plugin() )->version() ); ?></sup>
		</small>
	</h1>


	<div id="inpsyde-tabs" class="inpsyde-tabs">
		<?php require_once \Translationmanager\Functions\get_template( '/views/options-page/navigation.php' ); ?>

		<section id="tab--connection" class="inpsyde-tab__content inpsyde-tabs--connection">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Credentials', 'translationmanager' ); ?></h2>
			<?php include \Translationmanager\Functions\get_template( '/views/options-page/tabs/connection.php' ) ?>
		</section>

		<section id="tab--support" class="inpsyde-tab__content inpsyde-tabs--support">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Support', 'translationmanager' ); ?></h2>
			<?php include \Translationmanager\Functions\get_template( '/views/options-page/tabs/support.php' ) ?>
		</section>

		<section id="tab--system-status" class="inpsyde-tab__content inpsyde-tabs--status">
			<h2 class="screen-reader-text"><?php esc_html_e( 'System Status', 'translationmanager' ); ?></h2>
			<?php ( new \Translationmanager\SystemStatus\Controller( new \Translationmanager\Plugin() ) )->render() ?>
		</section>

		<section id="tab--about" class="inpsyde-tab__content inpsyde-tabs--about">
			<h2 class="screen-reader-text"><?php esc_html_e( 'About', 'translationmanager' ); ?></h2>
			<?php include \Translationmanager\Functions\get_template( '/views/options-page/tabs/about.php' ) ?>
		</section>
	</div>
</div>
