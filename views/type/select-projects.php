<select name="translationmanager_project_id" id="translationmanager_project_id">
	<option value="-1">
		<?php esc_html_e( 'New project', 'translationmanager' ); ?>
	</option>

	<?php foreach ( \Translationmanager\Functions\projects() as $project_id => $project_label ) : ?>
		<option value="<?php esc_attr_e( $project_id ); ?>" <?php selected( ( isset( $current ) ? $current : '' ), $project_id ) ?>>
			<?php esc_html_e( $project_label ); ?>
		</option>
	<?php endforeach; ?>

</select>
