<div id="translationmanager_language_overlay" class="translationmanager-language-overlay">
	<div class="translationmanager-lang-popup">
		<a class="close" href="#">&times;</a>
		<div class="content">

			<div id="translationmanager-project-wrap-div">
				<h2><?php esc_html_e( 'Select projects:', 'translationmanager' ); ?></h2>
				<?php require_once \Translationmanager\Functions\get_template( '/views/type/select-projects.php' ); ?>
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
