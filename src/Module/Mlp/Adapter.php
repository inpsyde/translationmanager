<?php
/**
 * Class Adapter
 *
 * @since   1.0.0
 * @package Translationmanager\Module\Mlp
 */

namespace Translationmanager\Module\Mlp;

use Translationmanager\Functions;

/**
 * Class Adapter
 *
 * @since   1.0.0
 * @package Translationmanager\Module\Mlp
 */
class Adapter {

	/**
	 * Plugin Data
	 *
	 * @since 1.0.0
	 *
	 * @var array The Plugin Data
	 */
	private $plugin;

	/**
	 * Version
	 *
	 * @since 1.0.0
	 *
	 * @var int The Mlp Plugin version
	 */
	private $version;

	/**
	 * Functions Mapper
	 *
	 * @since 1.0.0
	 *
	 * @var array List of functions mapped for version 2 and 3 of Mlp
	 */
	private static $functions_mapper = [
		'blog_language' => [
			2 => 'mlp_get_blog_language',
			3 => 'Inpsyde\\MultilingualPress\\siteLanguageTag',
		],
		'lang_by_iso'   => [
			2 => 'mlp_get_lang_by_iso',
			3 => 'Inpsyde\\MultilingualPress\\languageByTag',
		],
	];

	/**
	 * Methods Mapper
	 *
	 * @since 1.0.0
	 *
	 * @var array List of methods mapped for version 2 and 3 of Mlp
	 */
	private static $methods_mapper = [
		'site_relations'    => [
			'related_sites' => [
				2 => 'get_related_sites',
				3 => 'relatedSiteIds',
			],
		],
		'content_relations' => [
			'relations' => [
				2 => 'get_relations',
				3 => 'relations',
			],
		],
	];

	/**
	 * Site Relations
	 *
	 * @since 1.0.0
	 *
	 * @var mixed Depending on the mlp version. The instance that handle the site relations
	 */
	private $site_relations = null;

	/**
	 * Content Relations
	 *
	 * @since 1.0.0
	 *
	 * @var mixed Depending on the mlp version. The instance that handle the content relations
	 */
	private $content_relations = null;

	/**
	 * Adapter constructor
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_file       The plugin file path from which retrieve the plugin info.
	 * @param mixed  $site_relations    The instance for site relations.
	 * @param mixed  $content_relations The instance for content relations.
	 */
	public function __construct( $plugin_file, $site_relations, $content_relations ) {

		$this->site_relations    = $site_relations;
		$this->content_relations = $content_relations;

		$this->plugin = get_file_data( $plugin_file, [
			'version' => 'Version',
		] );

		$this->version = Functions\version_compare( '3.0.0', $this->plugin['version'], '<=' )
			? 3
			: 2;
	}

	/**
	 * The currently active mlp version
	 *
	 * @since 1.0.0
	 *
	 * @return int The major version number of the currently active mlp
	 */
	public function version() {

		return $this->version;
	}

	/**
	 * Blog Language
	 *
	 * @since 1.0.0
	 *
	 * @param int $site_id The id of the site for which retrieve the isocode language.
	 *
	 * @return string The iso code language of the site
	 */
	public function blog_language( $site_id, $short = false ) {

		$function = self::$functions_mapper[ __FUNCTION__ ][ $this->version ];

		if ( ! function_exists( $function ) ) {
			throw new \BadFunctionCallException( sprintf(
				'Function %s doesn\'t exists.',
				__FUNCTION__
			) );
		}

		return call_user_func( $function, $site_id, $short );
	}

	/**
	 * Language label
	 *
	 * @since 1.0.0
	 *
	 * @param string $lang_iso The iso code of the language for which retrieve the label.
	 *
	 * @return string The label name of the language by his iso code
	 */
	public function lang_by_iso( $lang_iso ) {

		$function = self::$functions_mapper[ __FUNCTION__ ][ $this->version ];

		if ( ! function_exists( $function ) ) {
			throw new \BadFunctionCallException( sprintf(
				'Function %s doesn\'t exists.',
				__FUNCTION__
			) );
		}

		$response = call_user_func( $function, $lang_iso );

		if ( ! is_scalar( $response ) && is_a( $response, 'Inpsyde\MultilingualPress\Framework\Language\Language' ) ) {
			$response = $response->isoName();
		}

		return $response;
	}

	/**
	 * Related Sites
	 *
	 * @since 1.0.0
	 *
	 * @param int $site_id The site for which retrieve the related sites.
	 *
	 * @return int[] A list of related sites ID's
	 */
	public function related_sites( $site_id ) {

		$cb = [
			$this->site_relations,
			self::$methods_mapper['site_relations'][ __FUNCTION__ ][ $this->version ],
		];

		return $cb( $site_id );
	}

	/**
	 * Relations
	 *
	 * @since 1.0.0
	 *
	 * @param int    $site_id   The site for which retrieve the relations.
	 * @param int    $object_id The object ID for which retrieve the relations.
	 * @param string $type      The type of the object for which retrieve the relations.
	 *
	 * @return array The relations of the object type
	 */
	public function relations( $site_id, $object_id, $type = 'post' ) {

		$cb = [
			$this->content_relations,
			self::$methods_mapper['content_relations'][ __FUNCTION__ ][ $this->version ],
		];

		return $cb( $site_id, $object_id, $type );
	}

	/**
	 * Set Relationship for objects
	 *
	 * @since 1.0.0
	 *
	 * @param int    $source_site_id    Source blog ID.
	 * @param int    $target_site_id    Target blog ID.
	 * @param int    $source_content_id Source post ID or term taxonomy ID.
	 * @param int    $target_content_id Target post ID or term taxonomy ID.
	 * @param string $type              Content type.
	 *
	 * @return void
	 */
	public function set_relation(
		$source_site_id,
		$target_site_id,
		$source_content_id,
		$target_content_id,
		$type = ''
	) {

		if ( 3 === $this->version ) {
			$relationship_id = $this->content_relations->relationshipId(
				[
					$target_site_id => $target_content_id,
				],
				$type
			);

			$this->content_relations->saveRelation(
				$relationship_id,
				$target_site_id,
				$target_content_id
			);

			return;
		}

		// MLP version 2.
		$this->content_relations->set_relation(
			$source_site_id,
			$target_site_id,
			$source_content_id,
			$target_content_id,
			$type
		);
	}
}
