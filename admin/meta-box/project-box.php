<div id="postbox-container-1" class="postbox-container" style="float: right; margin-left: 1em;">
	<div id="" class="">
		<div id="submitdiv" class="postbox ">
			<h2 class="" style="margin-left: 1em;">
				Status
			</h2>
			<div class="inside">
				<div>
					<span class="dashicons dashicons-testimonial"></span>
					<?php _e( 'Order number', 'tm4mlp' ) ?>:
					<b>
						<?php echo esc_html( $info->get_order_id() ) ?>
					</b>
				</div>

				<div>
					<span class="dashicons dashicons-yes"></span>
					<?php _e( 'Status', 'tm4mlp' ) ?>:
					<b>
						<?php echo esc_html( $info->get_status() ) ?>
					</b>
				</div>

				<div>
					<span class="dashicons dashicons-calendar-alt"></span>
					<?php _e( 'Ordered at', 'tm4mlp' ) ?>:
					<b>
						<?php echo $info->get_ordered_at()->format( 'Y-m-d' ) ?>
					</b>
				</div>

				<div>
					<span class="dashicons dashicons-calendar-alt"></span>
					<?php _e( 'Translated at', 'tm4mlp' ) ?>:
					<b>
						<?php echo $info->get_translated_at()->format( 'Y-m-d' ) ?>
					</b>
				</div>
			</div>
		</div>
	</div>
</div>