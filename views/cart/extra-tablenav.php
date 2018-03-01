<?php /** @var array $request */ ?>
<div class="alignleft actions">
	<?php if ( ! $request['translationmanager_project'] ): ?>
		<?php esc_html_e( 'Please select the project you like to order.', 'translationmanager' ) ?>
	<?php endif; ?>
</div>
