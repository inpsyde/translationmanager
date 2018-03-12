<div id="translationmanager_language_overlay" class="translationmanager-language-overlay">
	<div class="translationmanager-lang-popup">
		<a class="close" href="#">&times;</a>
		<div class="content">

			<div id="translationmanager-project-wrap-div">
				<h2><?php esc_html_e( 'Select Projects:', 'translationmanager' ); ?></h2>

				<select name="translationmanager-projects" id="translationmanager-">
					<option value="-1"><?php esc_html_e( 'New project', 'translationmanager' ); ?></option>
					<?php foreach ( \Translationmanager\Functions\projects() as $project_id => $project_label ) : ?>
						<option value="<?php echo esc_attr( $project_id ); ?>">
							<?php echo esc_html( $project_label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div id="translationmanager-lang-wrap-div">
				<h2><?php esc_html_e( 'Select languages:', 'translationmanager' ) ?></h2>

				<?php foreach ( \Translationmanager\Functions\get_languages_by_site_id( get_current_blog_id() ) as $lang_key => $lang ) : ?>
					<input type="checkbox"
					       name="translationmanager_bulk_languages[]"
					       value="<?php echo esc_attr( $lang_key ) ?>"/>
					<?php echo esc_html( $lang->get_label() ); ?>
				<?php endforeach; ?>
			</div>

			<?php
			submit_button( 'Bulk Translate', 'primary', 'translationmanager_submit_bulk_translate', true, [
				'id'       => 'translationmanager_submit_bulk_translate',
				'disabled' => true,
			] );
			?>
		</div>
	</div>
</div>
