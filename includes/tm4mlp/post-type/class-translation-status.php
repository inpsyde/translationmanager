<?php

namespace Tm4mlp\Post_Type;

class Translation_Status {
	const POST_TYPE = 'translation_status';

	public function to_array() {
		return call_user_func( 'get_object_vars', $this );
	}
}