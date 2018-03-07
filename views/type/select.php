<select class="<?php echo sanitize_html_class( $bind->class_attribute ); ?>"
        name="<?php echo esc_attr( $bind->name_attribute ); ?>">
	<?php foreach ( $bind->options as $value => $label ) : ?>
		<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $bind->current_value ); ?>>
			<?php echo esc_html( $label ); ?>
		</option>
	<?php endforeach; ?>
</select>
