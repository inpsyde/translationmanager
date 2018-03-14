<?php
if ( ! $this->get_customer_key() ):

	require_once \Translationmanager\Functions\get_template( '/views/meta-box/translation/no-key-customer.php' );

else :
	?>
	<!-- TODO values need to be transferred. -->
	<div class="translationmanager-translation-box">
		<?php
		if ( ! empty( \Translationmanager\Functions\get_languages() ) ) :
			if ( \Translationmanager\Functions\projects() ) {
				require_once \Translationmanager\Functions\get_template( 'views/meta-box/translation/projects.php' );
			}

			foreach ( \Translationmanager\Functions\get_languages() as $key => $language ) : ?>
				<div>
					<label for="language_<?php esc_attr_e( $language->get_lang_code() ); ?>">
						<input type="checkbox"
						       class="translationmanager_languages_class"
						       name="translationmanager_language[]"
						       value="<?php esc_attr_e( $key ) ?>"
						       id="language_<?php esc_attr_e( $language->get_lang_code() ); ?>"/>
						<?php esc_html_e( $language->get_label() ); ?>
					</label>
				</div>
			<?php endforeach; ?>

			<p id="translationmanager-inquery-button-id">
				<button type="submit"
				        name="translationmanager_action_project_add_translation"
				        title="<?php esc_attr_e( 'Create a new project containing the selected languages.', 'translationmanager' ) ?>"
				        class="button button-primary translationmanager-inquery-button">
					<?php echo esc_html( $this->context_button_label() ); ?>
				</button>
			</p>

			<input type="hidden"
			       name="<?php echo esc_attr( $this->nonce()->action() ); ?>"
			       value="<?php echo esc_attr( $this->nonce() ); ?>"/>

		<?php else : ?>
			<p><?php esc_html_e( 'No language found !', 'translationmanager' ); ?></p>
		<?php endif; ?>
	</div>

	<script>
		(function ( $ ) {
			$( function () {
				$( '#translationmanager-inquery-button-id' ).on( 'click', '.translationmanager-inquery-button', function () {
					var checked = $( "input.translationmanager_languages_class" ).is( ':checked' );
					if ( ! checked ) {
						alert( "You must check at least one language." );
						return false;
					}
				} );
			} )
		})( jQuery );
	</script>

<?php endif; ?>
