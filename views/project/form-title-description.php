<form method="post"
      action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
      class="translationmanager-project-details-form">
	<input type="hidden" name="action" value="translationmanager_project_info_save">
	<input type="hidden"
	       name="translationmanager_project_id"
	       value="<?php echo intval( $this->project->term_id ) ?>">
	<div class="alignleft actions">
		<div class="form-field form-required term-name-wrap">
			<label for="tag-name">
				<?php _ex( 'Name', 'term name' ); ?>
			</label>
			<input name="tag-name"
			       id="tag-name"
			       type="text"
			       value="<?php echo esc_attr( $this->project->name ); ?>"
			       size="40"
			       aria-required="true"/>
		</div>

		<div class="form-field term-description-wrap">
			<label for="description"><?php esc_html_e( 'Description', 'translationmanager' ); ?></label>
			<textarea
				name="description"
				id="description"
				rows="5"
				cols="40"><?php echo esc_attr( $this->project->description ) ?></textarea>
			<p>
				<i>
					<?php esc_html_e( 'Note: Only plain text is allowed. No markup.', 'translationmanager' ); ?>
				</i>
			</p>
		</div>

		<input type="hidden"
		       name="<?php echo esc_attr( $this->nonce->action() ) ?>"
		       value="<?php echo esc_attr( $this->nonce ) ?>"/>

		<input type="submit"
		       name="translationmanager_project_info_save"
		       class="button button-primary"
		       value="<?php esc_html_e( 'Save Project', 'translationmanager' ) ?>">
	</div>
</form>