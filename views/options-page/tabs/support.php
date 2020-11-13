<section class="support-section">
	<h3 class="support-section__title">
		<?php esc_html_e( 'Frequently asked questions', 'translationmanager' ); ?>
	</h3>

	<ul class="support-most-asked-questions-list">
		<li class="support-most-asked-questions-list__item">
			<a class="support-most-asked-questions-list__link"
			   href="http://help.eurotext.de/"
			   target="_blank"
			   rel="noopener noreferrer">
				<?php esc_html_e( 'What steps do I need to take before setting up translationMANAGER?', 'translationmanager' ); ?>
			</a>
		</li>
		<li class="support-most-asked-questions-list__item">
			<a class="support-most-asked-questions-list__link"
			   href="http://help.eurotext.de/"
			   target="_blank"
			   rel="noopener noreferrer">
				<?php esc_html_e( 'How do I set up my connection with Eurotext?', 'translationmanager' ); ?>
			</a>
		</li>
		<li class="support-most-asked-questions-list__item">
			<a class="support-most-asked-questions-list__link"
			   href="http://help.eurotext.de/"
			   target="_blank"
			   rel="noopener noreferrer">
				<?php esc_html_e( 'How can I create my first translation project?', 'translationmanager' ); ?>
			</a>
		</li>
		<li class="support-most-asked-questions-list__item">
			<a class="support-most-asked-questions-list__link"
			   href="http://help.eurotext.de/"
			   target="_blank"
			   rel="noopener noreferrer">
				<?php esc_html_e( 'How much will my translation cost?', 'translationmanager' ); ?>
			</a>
		</li>
		<li class="support-most-asked-questions-list__item">
			<a class="support-most-asked-questions-list__link"
			   href="http://help.eurotext.de/"
			   target="_blank"
			   rel="noopener noreferrer">
				<?php esc_html_e( 'How do I request support?', 'translationmanager' ); ?>
			</a>
		</li>
	</ul>

	<p class="support-documentation-link">
		<a href="https://eurotext-ecommerce.com/dokumentation/" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'Open complete documentation' ); ?>
		</a>
	</p>
</section>

<section class="support-section">
	<h3 class="support-section__title">
		<?php esc_html_e( 'Ask for help', 'translationmanager' ); ?>
	</h3>

	<p class="support-section__description">
		<?php esc_html_e( "Please provide the specific URL(s) where we can look at each issue, e.g. the translation doesn't work on this page: domain.com/en/my-translated-page.", 'translationmanager' ); ?>
		<br/>
		<?php esc_html_e( 'Please let us know how we will recognize the issue or can reproduce the issue. What is supposed to happen, and what is actually happening instead?', 'translationmanager' ); ?>
	</p>

	<form class="support-request-form"
	      name="support_request"
	      id="support_request"
	      method="post"
	      enctype="multipart/form-data"
	      action="<?php echo esc_url( \Translationmanager\Functions\current_url( [ '#tab--support' ] ) ); ?>">

		<p class="support-request-input-wrapper support-request-summary">
			<label for="support_request_summary"><?php esc_html_e( 'Summary', 'translationmanager' ); ?></label>
			<input type="text"
			       maxlength="64"
			       name="support_request_summary"
			       id="support_request_summary"
			       required="required"
			/>
		</p>

		<p class="support-request-input-wrapper support-request-description">
			<label for="support_request_description"><?php esc_html_e( 'Description', 'translationmanager' ); ?></label>
			<textarea name="support_request_description"
			          id="support_request_description"
			          rows="10"
			          required="required"></textarea>
		</p>

		<p class="support-request-input-wrapper support-request-upload">
			<label for="support_request_upload"><?php esc_html_e( 'Upload', 'translationmanager' ); ?></label>
			<span class="support-request-files-wrapper">
				<?php for ( $count = 0; 3 > $count; $count ++ ) : ?>
					<input type="file"
					       name="support_request_upload[]"
					       id="support_request_upload[]"
					       accept=".png, .jpeg, .jpg, .gif"/>
				<?php endfor; ?>

				<small>
					<?php esc_html_e( 'Max. file size: 5MB. Supported formats: .png, .jpeg, .jpg, .gif.', 'translationmanager' ); ?>
				</small>
			</span>
		</p>

		<p class="support-request-input-wrapper support-request-agreement">
			<input type="checkbox"
			       name="support_request_agreement"
			       id="support_request_agreement"
			       required="required"/>
			<label for="support_request_agreement">
				<?php printf(
					wp_kses_post( __( "I've read the %s, and I agree for Eurotext to automatically collect information about my WordPress installation.", 'translationmanager' ) ),
					'<a href="https://eurotext-ecommerce.com/dokumentation/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'documentation', 'translationmanager' ) . '</a>'
				); ?>
			</label>
		</p>

		<input type="submit"
		       name="support_request"
		       id="support_request"
		       class="button button-primary"
		       value="<?php esc_attr_e( 'Submit ticket', 'translationmanager' ); ?>"/>

		<?php $nonce = new \Brain\Nonces\WpNonce( 'support_request' ) ?>
		<input type="hidden"
		       name="<?php echo esc_attr( $nonce->action() ) ?>"
		       value="<?php echo esc_attr( $nonce ) ?>"/>
	</form>
</section>
