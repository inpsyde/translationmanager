<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php echo esc_html( $bind->page_title ); ?>
	</h1>

	<hr class="wp-header-end"/>
	<div id="ajax-response"></div>

	<div id="col-container" class="wp-clearfix">

		<?php
		$bind->wp_list_table->prepare_items();
		$bind->wp_list_table->views();
		?>

		<form id="posts-filter"
		      method="post"
		      action="<?php echo esc_url( \Translationmanager\Functions\current_url() ) ?>">
			<?php $bind->wp_list_table->display(); ?>
		</form>

		<?php $bind->wp_list_table->has_items() && $bind->wp_list_table->inline_edit(); ?>

		<div id="ajax-response"></div>
		<br class="clear"/>
	</div>
</div>
