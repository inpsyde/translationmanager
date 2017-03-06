<?php /** @var \Tm4mlp\Meta_Box\Translation_Box $this */ ?>
<a href="http://eurotext.de">
	<img src="<?php echo plugins_url( 'public/eurotext-logo.png', TM4MLP_FILE ) ?>"
	     alt=""
	     style="width: 80%; margin: 0.5em 0;">
</a>

<!-- TODO values need to be transferred. -->
<div style="line-height: 2em;">
	<?php foreach ( $this->get_languages() as $key => $language ): ?>
		<div>
			<label for="language_<?php esc_attr_e( $language->get_lang_code() ) ?>">
				<input type="checkbox"
				       name="tm4mlp_language[]"
				       value="<?php esc_attr_e( $key ) ?>"
				       id="language_<?php esc_attr_e( $language->get_lang_code() ) ?>"/>
				<?php esc_html_e( $language->get_label() ) ?>
			</label>
		</div>
	<?php endforeach; ?>
</div>

<p>
	<?php if ( ! $this->get_projects() ): ?>
		<button type="submit"
		        name="<?php echo TM4MLP_ACTION_PROJECT_ADD_TRANSLATION ?>"
		        title="<?php esc_attr_e( 'Create a new project containing the selected languages.', 'tm4mlp' ) ?>"
		        class="button button-primary">
			<?php esc_html_e( 'Create new project', 'tm4mlp' ) ?>
		</button>
	<?php endif; ?>
</p>
