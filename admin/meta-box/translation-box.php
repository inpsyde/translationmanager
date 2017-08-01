<?php /** @var \Translationmanager\Meta_Box\Translation_Box $this */ ?>
<script>
	(function($){
		$(function(){
			$('#translationmanager-inquery-button-id').on('click', '.translationmanager-inquery-button', function() {
				var checked = $("input.translationmanager_languages_class").is(':checked');
				if(!checked) {
					alert( "You must check at least one language." );
					return false;
				}
			});
		})
	})(jQuery)
</script>
<?php if ( ! $this->get_customer_key() ): ?>
	<em>
		<a href="<?php echo get_admin_url( null, '/options-general.php?page=' . \Translationmanager\Admin\Options_Page::SLUG ) ?>">
			<?php esc_html_e( 'Click here to setup the Eurotext Translation Plugin.', 'translationmanager' ) ?>
		</a>
	</em>
	<?php return; ?>
<?php endif; ?>

<!-- TODO values need to be transferred. -->
<div style="line-height: 2em;">
	<?php if( !empty($this->get_languages())):?>
	<?php if ( $this->get_projects() ): ?>
		<div class="misc-pub-section misc-pub-fff-status">
			<?php _e( 'Project', 'translationmanager' ) ?>:
			<strong>
				<span id="fff-status-display">
					<?php esc_html_e( $this->get_recent_project_name() ) ?>
				</span>
			</strong>
			<a href="#fff_status" class="edit-fff-status hide-if-no-js" role="button" style="display: inline;">
				<span aria-hidden="true"><?php _e( 'Edit' ) ?></span>
				<span class="screen-reader-text"><?php _e( 'Edit status' ) ?></span>
			</a>

			<div id="fff-status-select" class="hide-if-js" style="display: none;">
				<input type="hidden"
				       name="translationmanager_project_id"
				       id="translationmanager_project_id"
				       value="<?php echo $this->get_recent_project_id() ?>">
				<label for="fff_status" class="screen-reader-text">Set status</label>
				<select name="fff_status" id="fff_status">
					<option value="0"><?php _e( 'New project', 'translationmanager' ) ?></option>
					<?php foreach ( $this->get_projects() as $project_id => $project_label ): ?>
						<option value="<?php esc_attr_e( $project_id ) ?>">
							<?php esc_html_e( $project_label ) ?>
						</option>
					<?php endforeach; ?>
				</select>
				<a href="#fff_status" class="save-fff-status hide-if-no-js button">OK</a>
				<a href="#fff_status" class="cancel-fff-status hide-if-no-js button-cancel">Cancel</a>
			</div>

		</div>

		<script>
			var $fffStatusSelect = jQuery('#fff-status-select');

			// fff Status edit click.
			$fffStatusSelect.siblings('a.edit-fff-status').click(function (event) {
				if ($fffStatusSelect.is(':hidden')) {
					$fffStatusSelect.slideDown('fast', function () {
						$fffStatusSelect.find('select').focus();
					});
					jQuery(this).hide();
				}
				event.preventDefault();
			});

			// Save the Post Status changes and hide the options.
			$fffStatusSelect.find('.save-fff-status').click(function (event) {
				$fffStatusSelect.slideUp('fast').siblings('a.edit-fff-status').show().focus();

				jQuery('#fff-status-display').html(jQuery('#fff_status option:selected').text());
				jQuery('#translationmanager_project_id').val(jQuery('#fff_status').val());

				event.preventDefault();
			});

			// Cancel Post Status editing and hide the options.
			$fffStatusSelect.find('.cancel-fff-status').click(function (event) {
				$fffStatusSelect.slideUp('fast').siblings('a.edit-fff-status').show().focus();

				event.preventDefault();
			});
		</script>
	<?php endif; ?>

	<?php foreach ( $this->get_languages() as $key => $language ): ?>
		<div>
			<label for="language_<?php esc_attr_e( $language->get_lang_code() ) ?>">
				<input type="checkbox"
						class="translationmanager_languages_class"
				       name="translationmanager_language[]"
				       value="<?php esc_attr_e( $key ) ?>"
				       id="language_<?php esc_attr_e( $language->get_lang_code() ) ?>" />
				<?php esc_html_e( $language->get_label() ) ?>
			</label>
		</div>
	<?php endforeach; ?>
	<?php else:?>
		<p>No language found !</p>
	<?php endif?>

</div>

<?php if( !empty($this->get_languages())):?>
<p id="translationmanager-inquery-button-id">
	<button type="submit"
	        name="<?php echo TRANSLATIONMANAGER_ACTION_PROJECT_ADD_TRANSLATION ?>"
	        title="<?php esc_attr_e( 'Create a new project containing the selected languages.', 'translationmanager' ) ?>"
	        class="button button-primary translationmanager-inquery-button">
		<?php if ( ! $this->get_projects() ): ?>
			<?php esc_html_e( 'Create new project', 'translationmanager' ) ?>
		<?php else: ?>
			<?php esc_html_e( 'Add to project', 'translationmanager' ) ?>
		<?php endif; ?>
	</button>
</p>
<?php endif ?>
