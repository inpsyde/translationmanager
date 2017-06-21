<?php /** @var \Tmwp\Meta_Box\Order_Info $this */ ?>
<div>
	<span class="dashicons dashicons-yes"></span>
	<?php _e( 'Status', 'tmwp' ) ?>:
	<b>
		<?php echo esc_html( $this->get_status() ) ?>
	</b>
</div>
<form id="tmwp_order_or_update_projects" method="get" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
	<input type="hidden" name="action" value="tmwp_order_or_update_projects">
	<input type="hidden" name="_tmwp_project_id" value="<?php echo $_GET['tmwp_project']?>">
<?php if ( $this->get_order_id() ): ?>
	<div>
		<span class="dashicons dashicons-testimonial"></span>
		<?php _e( 'Project number', 'tmwp' ) ?>:
		<b>
			<?php echo esc_html( $this->get_order_id() ) ?>
		</b>
	</div>

	<?php if ( $this->get_ordered_at() instanceof \DateTime ): ?>
		<div>
			<span class="dashicons dashicons-calendar-alt"></span>
			<?php _e( 'Ordered at', 'tmwp' ) ?>:
			<b>
				<?php echo $this->get_ordered_at()->format( 'Y-m-d' ) ?>
			</b>
		</div>

		<?php if ( ! $this->get_translated_at() ): ?>
			<div class="textright">
				<input type="submit"
				       name="<?php echo TMWP_ACTION_PROJECT_UPDATE ?>"
				       class="button button-primary"
				       onclick="jQuery('#<?php echo TMWP_ACTION_PROJECT_UPDATE ?>').click();"
				       value="<?php _e( 'Update', 'tmwp' ) ?>"/>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $this->get_translated_at() instanceof \DateTime ): ?>
		<div>
			<span class="dashicons dashicons-calendar-alt"></span>
			<?php _e( 'Translated at', 'tmwp' ) ?>:
			<b>
				<?php echo $this->get_translated_at()->format( 'Y-m-d' ) ?>
			</b>
		</div>
	<?php endif; ?>
<?php else: ?>
	<br>
	<div class="textright">
		<input type="submit"
		       name="<?php echo TMWP_ACTION_PROJECT_ORDER ?>"
		       class="button button-primary"
		       onclick="jQuery('#<?php echo TMWP_ACTION_PROJECT_ORDER ?>').click();"
		       value="<?php _e( 'Order project', 'tmwp' ) ?>"/>
	</div>
<?php endif; ?>
</form>