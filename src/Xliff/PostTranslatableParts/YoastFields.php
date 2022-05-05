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

namespace Translationmanager\Xliff\PostTranslatableParts;

use SimpleXMLElement;
use Translationmanager\Xliff\XliffElementHelper;
use WP_Post;
use WPSEO_Meta;

class YoastFields
{
    const TRANSLATABLE_FIELDS = ['title', 'metadesc', 'focuskw', 'bctitle'];
    const DESCRIPTION = 'From field group: Yoast fields';

    /**
     * Will generate the XLIFF part with Yoast translatable fields
     *
     * @param WP_Post $post The project item source post
     * @param SimpleXMLElement $element The element where the fields should be added
     * @param XliffElementHelper $xliffElementHelper Helper to manage XLIFF elements
     */
    public function __construct(
        WP_Post $post,
        SimpleXMLElement $element,
        XliffElementHelper $xliffElementHelper
    ) {

        if (!class_exists('WPSEO_Meta')) {
            return;
        }

        $sourcePostId = $post->ID;

        $yoastGroup = $xliffElementHelper->addGroup($element, ['id' => $sourcePostId . '-yoast_fields']);

        foreach (self::TRANSLATABLE_FIELDS as $key) {
            $yoastUnit = $xliffElementHelper->addUnit(
                $yoastGroup,
                $this->elementId((string)$sourcePostId, $key)
            );

            $notes = [
                'post_id' => "From post: $post->ID",
                'group_name' => self::DESCRIPTION,
                'field' => "Field: $key",
            ];
            $xliffElementHelper->addNotes($yoastUnit, $notes);

            $field = get_post_meta($sourcePostId, WPSEO_Meta::$meta_prefix . $key, true);
            $xliffElementHelper->addSegment(
                $yoastUnit,
                ['id' => $key, 'state' => 'initial'],
                $field
            );
        }
    }

    protected function elementId(string $prefix, string $name): array
    {
        return ['id' => $prefix . '-' . $name];
    }
}
