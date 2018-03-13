<form method="post" action="options.php" class="inpsyde-form" id="inpsyde-form">
	<?php
	settings_fields( \Translationmanager\Setting\PluginSettings::OPTION_GROUP );
	do_settings_sections( 'translationmanager_api' );
	submit_button( esc_html__( 'Save changes', 'translationmanager' ), 'primary', 'save_action' );
	?>
</form>
