<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Translationmanager\Xliff;

use Exception;
use SimpleXMLElement;
use WP_Post;
use Translationmanager\Module\ACF\Acf;
use WPSEO_Meta;

class Xliff
{
    /**
     * Acf
     *
     * @var Acf
     */
    private $acf;

    /**
     * XliffElementCreationHelper
     *
     * @var XliffElementCreationHelper
     */
    private $xliffElementCreationHelper;

    public function __construct(Acf $acf, XliffElementCreationHelper $xliffElementCreationHelper)
    {
        $this->acf = $acf;
        $this->xliffElementCreationHelper = $xliffElementCreationHelper;
    }

    /**
     * Will create the XLIFF file based on the posts array passed
     *
     * @param array $posts Array of posts for which the XLIFF should be generated
     * @param string $xliffFilePath The path of directory where the XLIFF should be created
     * @param string $sourceLanguage The Source site language code
     * @param string $targetLanguage The target site language code
     * @return bool false if the generation of XLIFF failed and true if successful
     * @throws Exception
     */
    public function generateExport(
        array $posts,
        string $xliffFilePath,
        string $sourceLanguage,
        string $targetLanguage
    ): bool {

        if (empty($posts)) {
            return false;
        }

        $xliff = new SimpleXMLElement(
            $this->xliffElementCreationHelper->xliffHeaderFooterMarkup(
                $sourceLanguage,
                $targetLanguage,
                $xliffFilePath
            )
        );

        foreach ($posts as $post) {
            if (!$post instanceof WP_Post) {
                return false;
            }

            $postId = $post->_translationmanager_post_id;
            $sourcePost = get_post($postId);
            if (!$sourcePost instanceof WP_Post) {
                return false;
            }

            $postEntity = $this->xliffElementCreationHelper->addGroup($xliff, ['id' => (string)$sourcePost->ID]);
            $this->xliffPostDefaultTranslatableFieldsMarkup($sourcePost, $postEntity);
            $this->xliffAcfMarkup($sourcePost->ID, $postEntity);
            $this->xliffYoastMarkup($sourcePost->ID, $postEntity);
        }

        return $xliff->saveXML($xliffFilePath);
    }

    /**
     * Will generate the XLIFF part with ACF translatable fields
     *
     * @param int $sourcePostId The project item source post ID
     * @param SimpleXMLElement $group The element where the fields should be added
     */
    protected function xliffAcfMarkup(int $sourcePostId, SimpleXMLElement $group)
    {
        if (!class_exists('ACF')) {
            return;
        }

        $fields = get_field_objects($sourcePostId);
        if (empty($fields)) {
            return;
        }

        $acfFields = $this->acf->acfFieldKeys($fields, [], $sourcePostId);
        if (empty($acfFields)) {
            return;
        }

        $acfUnit= $this->xliffElementCreationHelper->addUnit($group, ['id' => 'acf_fields']);
        $this->xliffElementCreationHelper->addNotes($acfUnit, ['acf_fields' => 'The ACF fields']);
        foreach ($acfFields as $key => $value) {
            if (!is_string($value)) {
                continue;
            }
            $this->xliffElementCreationHelper->addSegment($acfUnit, ['id'=> $key, 'state'=>'initial'], $value);
        }
    }

    /**
     * Will generate the XLIFF markup part for default translatable post fields
     *
     * @param WP_Post $sourcePost The project item source post
     * @param SimpleXMLElement $group The element where the fields should be added
     */
    protected function xliffPostDefaultTranslatableFieldsMarkup(WP_Post $sourcePost, SimpleXMLElement $group)
    {
        $postDefaultsUnit= $this->xliffElementCreationHelper->addUnit($group, ['id' => 'post_defaults']);
        $notes = [
            'post_defaults' => 'The Post default translatable fields(title and content)'
        ];
        $this->xliffElementCreationHelper->addNotes($postDefaultsUnit, $notes);
        $this->xliffElementCreationHelper->addSegment(
            $postDefaultsUnit,
            ['id'=>'post_title', 'state'=>'initial'],
            $sourcePost->post_title
        );
        $this->xliffElementCreationHelper->addSegment(
            $postDefaultsUnit,
            ['id'=>'post_content', 'state'=>'initial'],
            $sourcePost->post_content
        );
    }

    /**
     * Will generate the XLIFF part with Yoast translatable fields
     *
     * @param int $sourcePostId The project item source post ID
     * @param SimpleXMLElement $group The element where the fields should be added
     */
    protected function xliffYoastMarkup(int $sourcePostId, SimpleXMLElement $group)
    {
        if (!class_exists('WPSEO_Meta')) {
            return;
        }

        $yoastUnit= $this->xliffElementCreationHelper->addUnit($group, ['id' => 'yoast_fields']);
        $this->xliffElementCreationHelper->addNotes($yoastUnit, ['yoast_fields' => 'The Yoast fields']);

        $translatableFields = ['title', 'metadesc', 'focuskw', 'bctitle'];
        foreach ($translatableFields as $key) {
            $field = get_post_meta($sourcePostId, WPSEO_Meta::$meta_prefix . $key, true);
            $this->xliffElementCreationHelper->addSegment($yoastUnit, ['id'=> $key, 'state'=>'initial'], $field);
        }
    }
}
