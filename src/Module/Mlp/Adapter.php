<?php

namespace Translationmanager\Module\Mlp;

use BadFunctionCallException;
use Translationmanager\Functions;

/**
 * Class Adapter
 *
 * @since   1.0.0
 * @package Translationmanager\Module\Mlp
 */
class Adapter
{
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
        'lang_by_iso' => [
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
        'site_relations' => [
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
    private $siteRelations;

    /**
     * Content Relations
     *
     * @since 1.0.0
     *
     * @var mixed Depending on the mlp version. The instance that handle the content relations
     */
    private $contentRelations;

    /**
     * Adapter constructor
     *
     * @param string $pluginVersion
     * @param mixed $siteRelations The instance for site relations.
     * @param mixed $contentRelations The instance for content relations.
     *
     * @since 1.0.0
     */
    public function __construct($pluginVersion, $siteRelations, $contentRelations)
    {
        $this->siteRelations = $siteRelations;
        $this->contentRelations = $contentRelations;

        $this->version = Functions\version_compare('3.0.0', $pluginVersion, '<=')
            ? 3
            : 2;
    }

    /**
     * The currently active mlp version
     *
     * @return int The major version number of the currently active mlp
     * @since 1.0.0
     */
    public function version()
    {
        return $this->version;
    }

    /**
     * Blog Language
     *
     * @param int $site_id The id of the site for which retrieve the isocode language.
     *
     * @param bool $short
     *
     * @return string The iso code language of the site
     * @since 1.0.0
     */
    public function blog_language($site_id, $short = false)
    {
        $function = self::$functions_mapper[__FUNCTION__][$this->version];

        if (!function_exists($function)) {
            throw new BadFunctionCallException(
                sprintf('Function %s doesn\'t exists.', __FUNCTION__)
            );
        }

        return $function($site_id, $short);
    }

    /**
     * Language label
     *
     * @param string $lang_iso The iso code of the language for which retrieve the label.
     *
     * @return string The label name of the language by his iso code
     * @since 1.0.0
     */
    public function lang_by_iso($lang_iso)
    {
        $function = self::$functions_mapper[__FUNCTION__][$this->version];

        if (!function_exists($function)) {
            throw new BadFunctionCallException(
                sprintf('Function %s doesn\'t exists.', __FUNCTION__)
            );
        }

        $response = $function($lang_iso);
        $isLanguageInstance = is_a(
            $response,
            'Inpsyde\MultilingualPress\Framework\Language\Language'
        );

        if (!is_scalar($response) && $isLanguageInstance) {
            $response = $response->isoName();
        }

        return $response;
    }

    /**
     * Related Sites
     *
     * @param int $site_id The site for which retrieve the related sites.
     *
     * @return int[] A list of related sites ID's
     * @since 1.0.0
     */
    public function related_sites($site_id)
    {
        $cb = [
            $this->siteRelations,
            self::$methods_mapper['site_relations'][__FUNCTION__][$this->version],
        ];

        return $cb($site_id);
    }

    /**
     * Relations
     *
     * @param int $site_id The site for which retrieve the relations.
     * @param int $object_id The object ID for which retrieve the relations.
     * @param string $type The type of the object for which retrieve the relations.
     *
     * @return array The relations of the object type
     * @since 1.0.0
     */
    public function relations($site_id, $object_id, $type = 'post')
    {
        $cb = [
            $this->contentRelations,
            self::$methods_mapper['content_relations'][__FUNCTION__][$this->version],
        ];

        return $cb($site_id, $object_id, $type);
    }

    /**
     * Set Relationship for objects
     *
     * @param int $source_site_id Source blog ID.
     * @param int $target_site_id Target blog ID.
     * @param int $source_content_id Source post ID or term taxonomy ID.
     * @param int $target_content_id Target post ID or term taxonomy ID.
     * @param string $type Content type.
     *
     * @return void
     * @since 1.0.0
     */
    public function set_relation(
        $source_site_id,
        $target_site_id,
        $source_content_id,
        $target_content_id,
        $type = ''
    ) {

        if (3 === $this->version) {
            $relationship_id = $this->contentRelations->relationshipId(
                [
                    $target_site_id => $target_content_id,
                ],
                $type
            );

            $this->contentRelations->saveRelation(
                $relationship_id,
                $target_site_id,
                $target_content_id
            );

            return;
        }

        // MLP version 2.
        $this->contentRelations->set_relation(
            $source_site_id,
            $target_site_id,
            $source_content_id,
            $target_content_id,
            $type
        );
    }
}
