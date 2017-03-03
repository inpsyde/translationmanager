<?php /** @var \Tm4mlp\Meta_Box\Order_Info $this */ ?>
<div>
	<span class="dashicons dashicons-testimonial"></span>
	<?php _e( 'Order number', 'tm4mlp' ) ?>:
	<b>
		<?php echo esc_html( $this->get_order_id() ) ?>
	</b>
</div>

<div>
	<span class="dashicons dashicons-yes"></span>
	<?php _e( 'Status', 'tm4mlp' ) ?>:
	<b>
		<?php echo esc_html( $this->get_status() ) ?>
	</b>
</div>

<div>
	<span class="dashicons dashicons-translation"></span>
	<?php _e( 'Target language', 'tm4mlp' ) ?>:
	<b>
		<?php echo $this->get_target_language_label() ?>
	</b>
</div>

<div>
	<span class="dashicons dashicons-calendar-alt"></span>
	<?php _e( 'Ordered at', 'tm4mlp' ) ?>:
	<b>
		<?php echo $this->get_ordered_at()->format( 'Y-m-d' ) ?>
	</b>
</div>

<div>
	<span class="dashicons dashicons-calendar-alt"></span>
	<?php _e( 'Translated at', 'tm4mlp' ) ?>:
	<b>
		<?php echo $this->get_translated_at()->format( 'Y-m-d' ) ?>
	</b>
</div>