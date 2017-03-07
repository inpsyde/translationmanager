<?php /** @var array $request */ ?>
<div class="alignleft actions">
	<?php if ( ! $request[ TM4MLP_TAX_PROJECT ] ): ?>
		<?php _e( 'Please select the project you like to order.', 'tm4mlp' ) ?>
	<?php else: ?>
		<input type="hidden" name="_tm4mlp_project_id" value="<?php esc_attr_e( $request[ TM4MLP_TAX_PROJECT ] ) ?>">
		<input type="submit"
		       id="<?php echo TM4MLP_ACTION_PROJECT_ORDER ?>"
		       name="<?php echo TM4MLP_ACTION_PROJECT_ORDER ?>"
		       class="button button-primary"
		       onclick="return confirm('<?php
		       esc_attr_e( 'This will lock the project. Do you really want to order this project?' ) ?>');"
		       value="<?php _e( 'Order project', 'tm4mlp' ) ?>"/>
	<?php endif; ?>
</div>