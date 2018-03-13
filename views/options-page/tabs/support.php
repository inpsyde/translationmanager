<section class="support-section">
	<h3 class="support-section__title">
		<?php esc_html_e( 'Most asked questions', 'translationmanager' ); ?>
	</h3>

	<ul class="support-most-asked-questions-list">
		<li class="support-most-asked-questions-list__item">
			<a class="support-most-asked-questions-list__link"
			   href="http://help.eurotext.de/"
			   target="_blank"
			   rel="noopener noreferrer">
				<?php esc_html_e( 'Which steps need to be done before setting up translationMANAGER?', 'translationmanager' ); ?>
			</a>
		</li>
		<li class="support-most-asked-questions-list__item">
			<a class="support-most-asked-questions-list__link"
			   href="http://help.eurotext.de/"
			   target="_blank"
			   rel="noopener noreferrer">
				<?php esc_html_e( 'How do I set up my connection to Eurotext?', 'translationmanager' ); ?>
			</a>
		</li>
		<li class="support-most-asked-questions-list__item">
			<a class="support-most-asked-questions-list__link"
			   href="http://help.eurotext.de/"
			   target="_blank"
			   rel="noopener noreferrer">
				<?php esc_html_e( 'How do I create my first translation project?', 'translationmanager' ); ?>
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
				<?php esc_html_e( 'How can I get support?', 'translationmanager' ); ?>
			</a>
		</li>
	</ul>

	<p class="support-documentation-link">
		<a href="http://help.eurotext.de" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'Open the complete documentation' ); ?>
		</a>
	</p>
</section>

<section class="support-section">
	<h3 class="support-section__title">
		<?php esc_html_e( 'Request Support', 'translationmanager' ); ?>
	</h3>

	<form class="support-request-form"
	      name="support_request"
	      id="support_request"
	      method="post"
	      enctype="multipart/form-data">

		<p class="support-request-input-wrapper support-request-summary">
			<label for="summary"><?php esc_html_e( 'Summary', 'translationmanager' ); ?></label>
			<input type="text" maxlength="64" name="summary" id="summary"/>
		</p>

		<p class="support-request-input-wrapper support-request-description">
			<label for="description"><?php esc_html_e( 'Description', 'translationmanager' ); ?></label>
			<textarea name="description" id="summary" rows="10"></textarea>
		</p>

		<p class="support-request-input-wrapper support-request-upload">
			<label for="upload"><?php esc_html_e( 'Upload', 'translationmanager' ); ?></label>
			<input type="file" name="upload" id="upload"/>
		</p>

		<p class="support-request-input-wrapper support-request-agreement">
			<input type="checkbox" name="aggreement" id="agreement"/>
			<span>
				<?php printf(
					wp_kses_post( __( 'I\'ve read the %s, and I agree to allow Eurotext to automatically collect information of my WordPress installation.', 'translationmanager' ) ),
					'<a href="http://help.eurotext.de/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'documentation', 'translationmanager' ) . '</a>'
				); ?>
			</span>
		</p>

		<input type="submit"
		       name="support_request"
		       id="support_request"
		       class="button button-primary"
		       value="<?php esc_attr_e( 'Submit the ticket', 'translationmanager' ); ?>"/>
	</form>
</section>
