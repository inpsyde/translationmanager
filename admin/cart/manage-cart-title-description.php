<div class="alignleft actions">
	<div class="form-field form-required term-name-wrap">
		<label for="tag-name"><?php _ex( 'Name', 'term name' ); ?></label>
		<input name="tag-name" id="tag-name" type="text" value="<?php echo ( is_object( $term ) ) ? $term->name : ''; ?>" size="40" aria-required="true" />
	</div>

	<div class="form-field term-description-wrap">
		<label for="tag-description"><?php _e( 'Description' ); ?></label>
		<textarea name="description" id="tag-description" rows="5" cols="40"><?php echo ( is_object( $term ) ) ? $term->description : ''; ?></textarea>
	</div>
</div>