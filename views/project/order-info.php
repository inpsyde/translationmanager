<div id="postbox-container-1" class="postbox-container translationmanager-postbox-container">
	<div id="submitdiv" class="postbox">

		<h2 class="box-status-title">
			<?php esc_html_e( 'Status', 'translationmanager' ); ?>
		</h2>

		<div class="inside">
			<form id="translationmanager_order_or_update_projects"
			      class="translationmanager-order-or-update-projects"
			      method="post"
			      action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

				<ul class="translationmanager-order-info">
					<li class="translationmanager-order-info-item">
						<span class="dashicons dashicons-yes"></span>
						<?php esc_html_e( 'Status', 'translationmanager' ); ?>:
						<b>
							<?php echo esc_html( $this->get_status_label() ); ?>
						</b>
					</li>

					<?php if ( $this->get_order_id() ): ?>
						<li class="translationmanager-order-info-item">
							<span class="dashicons dashicons-testimonial"></span>
							<?php esc_html_e( 'Project number', 'translationmanager' ) ?>:
							<b>
								<?php echo esc_html( $this->get_order_id() ) ?>
							</b>
						</li>

						<?php if ( $this->get_ordered_at() instanceof \DateTime ) : ?>
							<li class="translationmanager-order-info-item">
								<span class="dashicons dashicons-calendar-alt"></span>
								<?php esc_html_e( 'Ordered on', 'translationmanager' ) ?>:
								<b>
									<?php echo esc_html(
										$this->get_ordered_at()
										     ->format( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) )
									); ?>
								</b>
							</li>
						<?php endif; ?>

						<?php if ( $this->get_translated_at() instanceof \DateTime ) : ?>
							<li class="translationmanager-order-info-item">
								<span class="dashicons dashicons-calendar-alt"></span>
								<?php esc_html_e( 'Translated on', 'translationmanager' ); ?>:
								<b>
									<?php echo esc_html(
										$this->get_translated_at()
										     ->format( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) )
									); ?>
								</b>
							</li>

							<input type="submit"
							       name="translationmanager_import_project_translation"
							       class="button button-primary"
							       onclick="jQuery('#translationmanager_action_project_update').click();"
							       value="<?php esc_html_e( 'Import', 'translationmanager' ); ?>"/>
						<?php endif; ?>

						<?php if ( $this->get_latest_update_request_date() instanceof \DateTime ) : ?>
							<li class="translationmanager-order-info-item">
								<span class="dashicons dashicons-calendar-alt"></span>
								<?php esc_html_e( 'Latest update on', 'translationmanager' ); ?>:
								<b>
									<?php echo esc_html(
										$this->get_latest_update_request_date()
										     ->format( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) )
									); ?>
								</b>
							</li>
						<?php endif; ?>

						<?php if ( ! $this->get_translated_at() ) : ?>
							<input type="submit"
							       name="translationmanager_action_project_update"
							       class="button button-primary"
							       onclick="jQuery('#translationmanager_action_project_update').click();"
							       value="<?php esc_html_e( 'Update', 'translationmanager' ); ?>"/>
						<?php endif; ?>
					<?php endif; ?>
				</ul>

				<?php if ( ! $this->get_order_id() ) : ?>
					<?php if ( ! $this->has_projects() ) {
						printf(
							'<p class="no-projects-found">%s</p>',
							esc_html__( 'Please add at least one post in order to submit the project.', 'translationmanager' )
						);
					}
					?>
					<input type="submit"
					       name="translationmanager_action_project_order"
					       id="translationmanager_action_project_order"
					       class="button button-primary"
						<?php echo( ! $this->has_projects() ? ' disabled="disabled" ' : '' ); ?>
						   value="<?php esc_attr_e( 'Place Order', 'translationmanager' ); ?>"/>
				<?php endif; ?>

				<input type="hidden" name="action" value="<?php echo esc_attr( $this->action() ) ?>">
				<input type="hidden"
				       name="translationmanager_project_id"
				       value="<?php echo filter_input( INPUT_GET, 'translationmanager_project_id', FILTER_SANITIZE_NUMBER_INT ); ?>">
				<input type="hidden"
				       name="<?php echo esc_attr( $this->nonce()->action() ) ?>"
				       value="<?php echo esc_attr( $this->nonce() ) ?>"/>
			</form>
		</div>
	</div>
    <?php do_action('after_order_info');?>
</div>
