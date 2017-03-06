<?php

namespace Tm4mlp\Module;

use Tm4mlp\Domain\Language;

class Mlp_Connect {
	/**
	 * @var \Mlp_Site_Relations
	 */
	private $site_relations;

	/**
	 * MlpConnect constructor.
	 *
	 * @param \Mlp_Site_Relations $site_relations
	 */
	public function __construct( \Mlp_Site_Relations $site_relations ) {

		$this->site_relations = $site_relations;
	}

	/**
	 * @param array    $data
	 * @param \WP_Post $post
	 * @param int      $site_id
	 *
	 * @return array
	 */
	public function prepare_outgoing( array $data, \WP_Post $post, $site_id ) {

		// Add meta information so that MLP is capable of mapping ('__meta' is always given and should be an array).
		$data['__meta'] = array(
			'site_id' => $site_id,
			'post_id' => $post->ID,
			'type'    => 'post',
		);

		return $data;
	}

	/**
	 * @return array
	 */
	public function current_language() {

		$site_id   = get_current_site()->id;
		$lang_iso  = mlp_get_blog_language( $site_id, false );
		$lang_name = mlp_get_lang_by_iso( $lang_iso );

		return array(
			$site_id => array(
				'lang_code' => $lang_iso,
				'label'     => $lang_name,
			)
		);
	}

	/**
	 * @param int $site_id
	 *
	 * @return Language[]
	 */
	public function related_sites( $languages, $site_id ) {

		$sites = $this->site_relations->get_related_sites( $site_id );

		foreach ( $sites as $site ) {
			$lang_iso = mlp_get_blog_language( $site, false );

			$languages[ $site ] = new Language( $lang_iso, mlp_get_lang_by_iso( $lang_iso ) );
		}

		return $languages;
	}

	/**
	 * @param array $data
	 *
	 * @return void
	 */
	public function update_translations( array $data ) {

		if ( empty( $data['__meta']['target']['id'] ) ) {
			return;
		}

		switch_to_blog( $data['__meta']['target']['id'] );
		unset( $data['__meta'] );

		foreach ( $data as $translation ) {

			$translation['ID'] = $translation['__meta']['post_id'];

			unset ( $translation['__meta'] );

			wp_update_post( $translation );
		}

		restore_current_blog();
	}
}