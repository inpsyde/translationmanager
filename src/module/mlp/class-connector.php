<?php # -*- coding: utf-8 -*-
/**
 * Bridge between the translation data and the MLP API
 */

namespace Translationmanager\Module\Mlp;

use Translationmanager\Domain\Language;
use Translationmanager\Translation_Data;

class Connector {

	const DATA_NAMESPACE = 'MLP';

	/**
	 * @var Utils\Registry;
	 */
	private static $utils;

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
	 * @return Utils\Registry
	 */
	public static function utils() {

		self::$utils or self::$utils = new Utils\Registry();

		return self::$utils;
	}

	/**
	 * @param \Mlp_Site_Relations    $site_relations
	 * @param \Mlp_Content_Relations $content_relations
	 */
	public function __construct( \Mlp_Site_Relations $site_relations, \Mlp_Content_Relations $content_relations ) {

		$this->site_relations    = $site_relations;
		$this->content_relations = $content_relations;
	}

	/**
	 * @wp-hook translationmanager_outgoing_data
	 *
	 * @param Translation_Data $data
	 */
	public function prepare_outgoing( Translation_Data $data ) {

		$this->init_processors();
		$this->processors->process( $data, $this->site_relations, $this->content_relations );
	}

	/**
	 * @wp-hook translationmanager_post_updater
	 *
	 * @return callable
	 */
	public function prepare_updater() {

		return [ $this, 'update_translations' ];
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
		$this->processors->process( $data, $this->site_relations, $this->content_relations );

		$saved_post = $data->get_meta( Processor\Post_Saver::SAVED_POST_KEY, Connector::DATA_NAMESPACE );

		return ( $saved_post instanceof \WP_Post && $saved_post->ID )
			? $saved_post
			: null;
	}

	/**
	 * @wp-hook translationmanager_current_language
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
	 * @wp-hook translationmanager_languages
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
			->push_processor( new Processor\Post_Saver() )
			->push_processor( new Processor\Post_Thumb_Sync() )
			->push_processor( new Processor\Taxonomies_Sync() );
	}
}