<?php

add_filter( 'tm4mlp_sanitize_post', function ( $data, $post ) {
	/** @var \WP_Post $post */

	// Add meta information so that WPML is capable of mapping ('__meta' is always given and should be an array).
	$data['__meta']['wpml_id']        = 007; // some ID that wpml needs.
	$data['__meta']['wpml_entity']    = 'post'; // Type of data if needed (could be tax too - some day).
	$data['__meta']['wpml_post_type'] = $post->post_type; // Post-Type if needed (and ID is not enough).

	return $data;
}, 10, 2 );

add_filter( 'tm4mlp_api_translation_update', function ( $data ) {
	// This would be the input:
	$tmp_data = array(
		"__meta" => array(
			"id"     => 1,
			//	( ID as given by REST - API)
			"source" => array(
				"id"       => 42,       // see tm4mlp_get_current_language filter below
				"language" => "de-DE",
				"label"    => "Deutsch"
			),
			"target" => array(
				"id"       => 1337,     // see tm4mlp_get_languages filter below
				"language" => 'fr-FR',
				"label"    => "Francais"
			), // ( see below for `tm4mlp_get_languages` filter )
		),
		0        => array(
			"__meta"       => array(
				// See 'tm4mlp_sanitize_post' filter above.
				"wpml_id"     => 1,
				"wpml_entity" => "post",
				"wpml_post_type"   => "post"
			),
			"post_title"   => "Le titre.",
			"post_content" => "Le contenu."
		),
		1        => array(
			"__meta"       => array(
				// See 'tm4mlp_sanitize_post' filter above.
				"wpml_id"     => 1,
				"wpml_entity" => "post",
				"wpml_post_type"   => "page"
			),
			"post_title"   => "La page",
			"post_content" => "Le contenu de page."
		)
	);

	// ...
} );

add_filter( 'tm4mlp_get_languages', function () {
	return array(
		42   => array(
			'lang_code' => 'de-DE',
			'label'     => 'Deutsch',
		),
		1337 => array(
			'lang_code' => 'fr-FR',
			'label'     => 'Français',
		)
	);
} );

add_filter( 'tm4mlp_get_current_language', function () {
	return array(
		42 => array(
			'lang_code' => 'de-DE',
			'label'     => 'Deutsch',
		)
	);
} );