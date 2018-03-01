<?php esc_html_e( $prefix ) ?>
<input <?php foreach ( $field as $key => $value ) : ?>
<?php if ( ! is_numeric( $key ) ): ?>
<?php echo $key ?>="<?php echo esc_attr( $value ) ?>"
<?php else: ?>
	<?php echo $key ?>
<?php endif; ?>
<?php endforeach; ?>
/>
<?php esc_html_e($suffix) ?>
