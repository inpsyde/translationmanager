<p class="default-field default-field--<?php echo sanitize_html_class( $this->type ); ?>">
	<input type="<?php echo esc_attr( $this->type ) ?>"
	       class="default-field__input"
	       value="<?php echo esc_attr( $this->value ); ?>"
	       name="<?php echo esc_attr( $this->name ) ?>"
	       placeholder="<?php echo esc_attr( $this->placeholder ) ?>"
	/>

	<?php if ( $this->description ) : ?>
		<i class="default-field__description">
			<?php echo wp_kses_post( $this->description ); ?>
		</i>
	<?php endif; ?>
</p>
