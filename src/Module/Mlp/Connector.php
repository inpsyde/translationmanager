<?php // -*- coding: utf-8 -*-

/**
 * Bridge between the translation data and the MLP API
 */

namespace Translationmanager\Module\Mlp;

use Translationmanager\Domain\Language;
use Translationmanager\TranslationData;

class Connector {

	const DATA_NAMESPACE = 'MLP';

	/**
	 * @var Utils\Registry;
	 */
	private static $utils;

	/**
	 * @var ProcessorBus
	 */
	private $processors;

	/**
	 * @var \Translationmanager\Module\Mlp\Adapter
	 */
	private $adapter;

	/**
	 * @return Utils\Registry
	 */
	public static function utils() {

		self::$utils or self::$utils = new Utils\Registry();

		return self::$utils;
	}

	/**
	 * Connector constructor
	 *
	 * @param \Translationmanager\Module\Mlp\Adapter $adapter
	 */
	public function __construct( Adapter $adapter ) {

		$this->adapter = $adapter;
	}

	/**
	 * @wp-hook translationmanager_outgoing_data
	 *
	 * @param TranslationData $data
	 */
	public function prepare_outgoing( TranslationData $data ) {

		$this->init_processors();
		$this->processors->process( $data, $this->adapter );
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
	 * @param TranslationData $data
	 *
	 * @return null|\WP_Post
	 */
	public function update_translations( TranslationData $data ) {

		if ( ! $data->is_valid() ) {
			return null;
		}

		$this->init_processors();
		$this->processors->process( $data, $this->adapter );

		$saved_post = $data->get_meta( Processor\PostSaver::SAVED_POST_KEY, self::DATA_NAMESPACE );

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

		$site_id   = get_current_blog_id();
		$lang_iso  = $this->adapter->blog_language( $site_id, false );
		$lang_name = $this->adapter->lang_by_iso( $lang_iso );

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

		$sites = $this->adapter->related_sites( $site_id );

		foreach ( $sites as $site ) {
			$lang_iso = $this->adapter->blog_language( $site, false );

			$languages[ $site ] = new Language( $lang_iso, $this->adapter->lang_by_iso( $lang_iso ) );
		}

		return $languages;
	}

	/**
	 * Initialize processors bus and add default processors to it.
	 */
	private function init_processors() {

		$this->processors = new ProcessorBus();
		$this->processors
			->push_processor( new Processor\PostDataBuilder() )
			->push_processor( new Processor\PostParentSync() )
			->push_processor( new Processor\PostSaver() )
			->push_processor( new Processor\PostThumbSync() )
			->push_processor( new Processor\TaxonomiesSync() );
	}
}
