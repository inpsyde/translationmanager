<form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
	<input type="hidden" name="action" value="tmwp_project_info_save">
	<input type="hidden" name="_tmwp_project_id" value="<?php echo $_GET['tmwp_project']?>">
	<div class="alignleft actions" style="padding-bottom: 20px">
		<div class="form-field form-required term-name-wrap">
			<label for="tag-name"><?php _ex( 'Name', 'term name' ); ?></label>
			<input name="tag-name" id="tag-name" type="text" value="<?php echo ( is_object( $term ) ) ? $term->name : ''; ?>" size="40" aria-required="true" />
		</div>

		<div class="form-field term-description-wrap">
			<label for="tag-description"><?php _e( 'Description' ); ?></label>
			<textarea name="description" id="tag-description" rows="5" cols="40"><?php echo ( is_object( $term ) ) ? $term->description : ''; ?></textarea>
		</div>
		<?php wp_referer_field() ?>
		<input type="submit" name="tmwp_project_info_save" class="button button-primary" value="Save Project">
	</div>
</form>