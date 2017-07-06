<?php # -*- coding: utf-8 -*-
/**
 * Bridge between the translation data and the MLP API
 */

namespace Tmwp\Module\Mlp;

use Tmwp\Domain\Language;
use Tmwp\Translation_Data;

class Connector {

	const TMWP_MLP_UPDATED_POST = 'tmwp_mlp_module_updated_post';
	const DATA_NAMESPACE = 'MLP';
	const UPDATED_BY_MLP = 'updated-by-multilingualpress';
	const CREATED_BY_MLP = 'created-by-multilingualpress';

	/**
	 * @var \Mlp_Site_Relations
	 */
	private $site_relations;

	/**
	 * @var \Mlp_Content_Relations
	 */
	private $content_relations;

	/**
	 * @var Processor_Bus
	 */
	private $processors;

	/**
	 * @param \Mlp_Site_Relations    $site_relations
	 * @param \Mlp_Content_Relations $content_relations
	 */
	public function __construct( \Mlp_Site_Relations $site_relations, \Mlp_Content_Relations $content_relations ) {

		$this->site_relations    = $site_relations;
		$this->content_relations = $content_relations;
	}

	/**
	 * @wp-hook tmwp_outgoing_data
	 *
	 * @param Translation_Data $data
	 */
	public function prepare_outgoing( Translation_Data $data ) {

		$this->init_processors();
		$this->processors->process( Processor_Bus::OUTGOING, $data, $this->site_relations, $this->content_relations );
	}

	/**
	 * @wp-hook tmwp_post_updater
	 *
	 * @return callable
	 */
	public function prepare_updater() {

		return array( $this, 'update_translations' );
	}

	/**
	 * @param Translation_Data $data
	 *
	 * @return null|\WP_Post
	 */
	public function update_translations( Translation_Data $data ) {

		if ( ! $data->is_valid() ) {
			return null;
		}

		$this->init_processors();
		$this->processors->process( Processor_Bus::INGOING, $data, $this->site_relations, $this->content_relations );

		return $data->get_meta( Processor\Post_Saver::SAVED_POST_KEY );
	}

	/**
	 * @wp-hook tmwp_get_current_language
	 *
	 * @return Language
	 */
	public function current_language() {

		$site_id   = get_current_site()->id;
		$lang_iso  = mlp_get_blog_language( $site_id, false );
		$lang_name = mlp_get_lang_by_iso( $lang_iso );

		return new Language( $lang_iso, $lang_name );
	}

	/**
	 * @wp-hook tmwp_get_languages
	 *
	 * @param array $languages
	 * @param int   $site_id
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
	 * Initialize processors bus and add default processors to it.
	 */
	private function init_processors() {

		$this->processors = new Processor_Bus();
		$this->processors
			->push_processor( new Processor\Post_Data_Builder() )
			->push_processor( new Processor\Post_Parent_Sync() )
			->push_processor( new Processor\Post_Saver() );
	}
}