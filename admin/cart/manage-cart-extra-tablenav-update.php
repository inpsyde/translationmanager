<?php /** @var array $request */ ?>
<div class="alignleft actions">
		<input type="hidden" name="_tmwp_project_id" value="<?php esc_attr_e( $request[ TMWP_TAX_PROJECT ] ) ?>">
		<input type="submit"
		       id="<?php echo TMWP_ACTION_PROJECT_UPDATE ?>"
		       name="<?php echo TMWP_ACTION_PROJECT_UPDATE ?>"
		       class="button button-primary"
		       value="<?php _e( 'Update status', 'tmwp' ) ?>"/>
</div>