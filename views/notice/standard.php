<div class="notice notice-<?php echo sanitize_html_class( $this->severity ); ?>">
	<p>
		<?php echo wp_kses_post( $this->message ); ?>
	</p>
</div>

