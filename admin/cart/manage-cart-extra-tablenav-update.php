<?php /** @var array $request */ ?>
<div class="alignleft actions">
		<input type="hidden" name="_tm4mlp_project_id" value="<?php esc_attr_e( $request[ TM4MLP_TAX_PROJECT ] ) ?>">
		<input type="submit"
		       id="<?php echo TM4MLP_ACTION_PROJECT_UPDATE ?>"
		       name="<?php echo TM4MLP_ACTION_PROJECT_UPDATE ?>"
		       class="button button-primary"
		       value="<?php _e( 'Update status', 'translationmanager' ) ?>"/>
</div>