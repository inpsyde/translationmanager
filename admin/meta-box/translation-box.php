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
			<label for="language_<?php esc_attr_e( $language['lang_code'] ) ?>">
				<input type="checkbox"
				       name="language[]"
				       value="<?php esc_attr_e( $key ) ?>"
				       id="language_<?php esc_attr_e( $language['lang_code'] ) ?>"/>
				<?php esc_html_e( $language['label'] ) ?>
			</label>
		</div>
	<?php endforeach; ?>
</div>

<p>
	<a href="<?php echo admin_url( 'index.php?page=tm4mlp_add_translation&type=post&id=' . get_the_ID() ) ?>"
	   title="<?php esc_attr_e( 'Request translation from Eurotext', 'tm4mlp' ) ?>"
	   class="button button-primary">
		<?php esc_html_e( 'Request translation(s)', 'tm4mlp' ) ?>
	</a>
</p>
