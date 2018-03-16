<?php

use TranslationManager\Functions;

?>
<div class="modal-overlay" style="display: none">
	<section id="<?php echo sanitize_title( $this->title ); ?>"
	         class="modal <?php echo esc_attr( Functions\sanitize_html_class( $this->attributes['class'] ) ); ?>"
	         style="display: none">

		<?php if ( $this->title ) : ?>
			<h3 class="modal__title">
				<span class="<?php echo esc_attr( Functions\sanitize_html_class( $this->icon['attributes']['class'] ) ); ?>"></span>
				<?php echo esc_html( $this->title ); ?>
			</h3>
		<?php endif; ?>

		<?php if ( is_callable( $this->callback ) ) : ?>
			<div class="modal__content">
				<?php ( $this->callback )(); ?>
			</div>
		<?php endif; ?>

	</section>
</div>
