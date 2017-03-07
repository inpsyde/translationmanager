<?php /** @var array $request */ ?>
<div class="alignleft actions">
	<?php if ( ! $request[ TM4MLP_TAX_PROJECT ] ): ?>
		<?php _e( 'Please select the project you like to order.', 'tm4mlp' ) ?>
	<?php else: ?>
		<input type="submit"
		       id="tm4mlp_order_translation"
		       name="tm4mlp_order_translation"
		       class="button button-primary"
		       onclick="return confirm('<?php
		       esc_attr_e( 'Do you really want to order all translations (not only selected)?' ) ?>');"
		       value="<?php _e( 'Order project', 'tm4mlp' ) ?>"/>
	<?php endif; ?>
</div>

