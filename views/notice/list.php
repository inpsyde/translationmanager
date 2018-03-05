<div class="notice notice-<?php echo sanitize_html_class( $severity ) ?>">
	<ul>
		<?php echo wp_kses_post( $message ) ?>
	</ul>
</div>
