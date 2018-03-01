<div>
	<span class="dashicons dashicons-yes"></span>
	<?php esc_html_e( 'Status', 'translationmanager' ); ?>:
	<b>
		<?php echo esc_html( $this->get_status() ); ?>
	</b>
</div>

<form id="translationmanager_order_or_update_projects"
      method="post"
      action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<input type="hidden" name="action" value="translationmanager_order_or_update_projects">
	<input type="hidden"
	       name="_translationmanager_project_id"
	       value="<?php echo filter_input( INPUT_GET, 'translationmanager_project', FILTER_SANITIZE_STRING ); ?>">

	<?php if ( $this->get_order_id() ): ?>
		<div>
			<span class="dashicons dashicons-testimonial"></span>
			<?php esc_html_e( 'Project number', 'translationmanager' ) ?>:
			<b>
				<?php echo esc_html( $this->get_order_id() ) ?>
			</b>
		</div>

		<?php if ( $this->get_ordered_at() instanceof \DateTime ): ?>
			<div>
				<span class="dashicons dashicons-calendar-alt"></span>
				<?php esc_html_e( 'Ordered at', 'translationmanager' ) ?>:
				<b>
					<?php echo $this->get_ordered_at()->format( 'Y-m-d' ) ?>
				</b>
			</div>

			<?php if ( ! $this->get_translated_at() ): ?>
				<div class="textright">
					<input type="submit"
					       name="translationmanager_action_project_update"
					       class="button button-primary"
					       onclick="jQuery('#translationmanager_action_project_update').click();"
					       value="<?php esc_html_e( 'Update', 'translationmanager' ); ?>"/>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( $this->get_translated_at() instanceof \DateTime ) : ?>
			<div>
				<span class="dashicons dashicons-calendar-alt"></span>
				<?php esc_html_e( 'Translated at', 'translationmanager' ); ?>:
				<b>
					<?php echo $this->get_translated_at()->format( 'Y-m-d' ); ?>
				</b>
			</div>
		<?php endif; ?>
	<?php
	else :
		if ( ! $this->has_projects() ) {
			printf(
				'<p style="color:red;max-width:200px;text-align:right;float:right">%s</p>',
				esc_html__( 'Please add at least one post to be able to submit the project.', 'translationmanager' )
			);
		}
		?>
		<div class="textright" style="margin-top: .63em">
			<input type="submit"
			       name="translationmanager_action_project_order"
			       id="translationmanager_action_project_order"
			       class="button button-primary"
				<?php echo( ! $this->has_projects() ? 'disabled="disabled"' : '' ); ?>
				   value="<?php esc_html_e( 'Order project', 'translationmanager' ); ?>"/>
		</div>
	<?php endif; ?>
</form>
