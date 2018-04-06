<input type="<?php echo esc_attr( $bind->type ) ?>"
       value="<?php echo esc_attr( $bind->value ); ?>"
       name="<?php echo esc_attr( $bind->name ) ?>"
       placeholder="<?php echo esc_attr( $bind->placeholder ) ?>"
       pattern="<?php echo esc_attr( $bind->pattern ) ?>"
/>

<?php if ( ! empty( $bind->description ) ) : ?>
	<p class="description">
		<?php echo wp_kses_post( $bind->description ); ?>
	</p>
<?php endif; ?>
