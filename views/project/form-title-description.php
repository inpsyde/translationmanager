<form method="post"
      action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
      class="translationmanager-project-details-form">
	<input type="hidden" name="action" value="translationmanager_project_info_save">
	<input type="hidden"
	       name="_translationmanager_project_id"
	       value="<?php echo filter_input( INPUT_GET, 'translationmanager_project', FILTER_SANITIZE_STRING ) ?>">
	<div class="alignleft actions">
		<div class="form-field form-required term-name-wrap">
			<label for="tag-name">
				<?php _ex( 'Name', 'term name' ); ?>
			</label>
			<input name="tag-name"
			       id="tag-name"
			       type="text"
			       value="<?php echo( is_object( $term ) ? $term->name : '' ); ?>"
			       size="40"
			       aria-required="true"/>
		</div>

		<div class="form-field term-description-wrap">
			<label for="description"><?php esc_html_e( 'Description', 'translationmanager' ); ?></label>
			<textarea
				name="description"
				id="description"
				rows="5"
				cols="40"><?php echo ( is_object( $term ) ) ? $term->description : ''; ?></textarea>
			<p>
				<i>
					<?php esc_html_e( 'Note: Only plain text allowed. No markup', 'translationmanager' ); ?>
				</i>
			</p>
		</div>

		<?php include \Translationmanager\Functions\get_template( '/views/type/nonce.php' ) ?>

		<input type="submit"
		       name="translationmanager_project_info_save"
		       class="button button-primary"
		       value="<?php esc_html_e( 'Save Project', 'translationmanager' ) ?>">
	</div>
</form>