<div class="misc-pub-section misc-pub-fff-status">
	<?php esc_html_e( 'Project:', 'translationmanager' ) ?>
	<strong>
	<span id="fff-status-display">
		<?php esc_html_e( $this->get_recent_project_name() ) ?>
	</span>
	</strong>

	<a href="#translationmanager_projects_list" class="edit-fff-status hide-if-no-js" role="button">
		<span aria-hidden="true">
			<?php esc_html_e( 'Edit', 'translationmanager' ) ?>
		</span>
		<span class="screen-reader-text">
			<?php esc_html_e( 'Edit status', 'translationmanager' ) ?>
		</span>
	</a>

	<div id="fff-status-select" class="fff-status-select hide-if-js">
		<input type="hidden"
		       name="translationmanager_project_id"
		       id="translationmanager_project_id"
		       value="<?php echo intval( $this->get_recent_project_id() ); ?>">

		<label for="fff_status" class="screen-reader-text">
			<?php esc_html_e( 'Set status', 'translationmanager' ); ?>
		</label>

		<?php require_once \Translationmanager\Functions\get_template( '/views/type/select-projects.php' ); ?>

		<a href="#translationmanager_projects_list" class="save-fff-status hide-if-no-js button">
			<?php esc_html_e( 'OK', 'translation manager' ); ?>
		</a>
		<a href="#translationmanager_projects_list" class="cancel-fff-status hide-if-no-js button-cancel">
			<?php esc_html_e( 'Cancel', 'translationmanager' ); ?>
		</a>
	</div>

</div>

<script>
	var $fffStatusSelect = jQuery( '#fff-status-select' );

	// fff Status edit click.
	$fffStatusSelect.siblings( 'a.edit-fff-status' ).click( function ( event ) {
		if ( $fffStatusSelect.is( ':hidden' ) ) {
			$fffStatusSelect.slideDown( 'fast', function () {
				$fffStatusSelect.find( 'select' ).focus();
			} );
			jQuery( this ).hide();
		}
		event.preventDefault();
	} );

	// Save the Post Status changes and hide the options.
	$fffStatusSelect.find( '.save-fff-status' ).click( function ( event ) {
		$fffStatusSelect.slideUp( 'fast' ).siblings( 'a.edit-fff-status' ).show().focus();

		jQuery( '#fff-status-display' ).html( jQuery( '#translationmanager_projects_list option:selected' ).text() );
		jQuery( '#translationmanager_project_id' ).val( jQuery( '#translationmanager_projects_list' ).val() );

		event.preventDefault();
	} );

	// Cancel Post Status editing and hide the options.
	$fffStatusSelect.find( '.cancel-fff-status' ).click( function ( event ) {
		$fffStatusSelect.slideUp( 'fast' ).siblings( 'a.edit-fff-status' ).show().focus();

		event.preventDefault();
	} );
</script>
