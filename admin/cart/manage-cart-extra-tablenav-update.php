<?php /** @var array $request */ ?>
<div class="alignleft actions">
		<input type="hidden" name="_translationmanager_project_id" value="<?php esc_attr_e( $request[ TRANSLATIONMANAGER_TAX_PROJECT ] ) ?>">
		<input type="submit"
		       id="<?php echo TRANSLATIONMANAGER_ACTION_PROJECT_UPDATE ?>"
		       name="<?php echo TRANSLATIONMANAGER_ACTION_PROJECT_UPDATE ?>"
		       class="button button-primary"
		       value="<?php _e( 'Update status', 'translationmanager' ) ?>"/>
</div>