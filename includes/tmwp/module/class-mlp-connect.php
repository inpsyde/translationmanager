<?php # -*- coding: utf-8 -*-
/**
 * Bridge between the translation data and the MLP API
 *
 * @version 2017.03.01
 * @author  toscho
 */

namespace Tmwp\Module;

use Tmwp\Domain\Language;

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
	 * Add meta information so that MLP is capable of mapping ('__meta' is always given and should be an array).
	 *
	 * @param array    $data
	 * @param \WP_Post $post
	 * @param int      $site_id
	 *
	 *
	 * @todo Object TransportInformation machen.
	 *
	 * @return array
	 */
	public function prepare_outgoing( array $data, \WP_Post $post, $site_id ) {
		if ( ! isset( $data['__meta'] ) ) {
			$data['__meta'] = array();
		}

		$data['__meta']['site_id'] = $site_id;
		$data['__meta']['post_id'] = $post->ID;
		$data['__meta']['type']    = 'post';

		/**
		 * Filters translation data for a post, after it is edited by MLP module, before it is sent to API.
		 *
		 * @param \WP_Post $post    To-be-translated post
		 * @param array    $data    Translation data to be sent via API
		 * @param int      $site_id Post site ID
		 */
		$data = apply_filters(
			'tmwp_mlp_module_outgoing_post',
			$data,
			$post,
			(int) $site_id
		);

		return $data;
	}

	/**
	 * @return Language
	 */
	public function current_language() {

		$site_id   = get_current_site()->id;
		$lang_iso  = mlp_get_blog_language( $site_id, false );
		$lang_name = mlp_get_lang_by_iso( $lang_iso );

		return new Language($lang_iso, $lang_name);
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
	 * @param array $translation
	 *
	 * @return void
	 */
	public function update_translations( array $translation ) {

//		foreach ( $data['items'] as $translation ) {
//			$translation = $translation['data'];
			if (! switch_to_blog( $translation['__meta']['target_id'] ) ) {
				// TODO Error message, payed for nothing.
				return;
			}

			$translation['ID'] = $translation['__meta']['post_id'];

			unset ( $translation['__meta'] );

			$id = wp_update_post( $translation );

			if ( $id && ! is_wp_error( $id ) && ( $post = get_post( $id ) ) ) {

				/**
				 * Fires after a translation post is updated by MLP, giving other modules opportunity to edit/use
				 * the just translated post also accessing translation data received from the API
				 *
				 * @param \WP_Post $post        Just translated post
				 * @param array    $translation Translation data received form API
				 * @param int      $site_id     Post site ID
				 */
				do_action(
					'tmwp_mlp_module_updated_post',
					$post,
					$translation,
					(int) $translation['__meta']['target_id']
				);
			}
//		}

		restore_current_blog();
	}
}