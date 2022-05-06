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

class GeneralFields
{
    const FIELDS = ['post_title', 'post_content', 'post_excerpt'];
    const DESCRIPTION = 'From field group: The Post default translatable fields';

    /**
     * Will generate the XLIFF markup part for default translatable post fields
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

        $postDefaultsGroup = $xliffElementHelper->addGroup(
            $element,
            $this->elementId((string)$post->ID, 'post_defaults')
        );

        foreach (self::FIELDS as $field) {
            $postDefaultsUnit = $xliffElementHelper->addUnit(
                $postDefaultsGroup,
                $this->elementId((string)$post->ID, $field)
            );

            $notes = [
                'post_id' => "From post: $post->ID",
                'group_name' => self::DESCRIPTION,
                'field' => "Field: $field",
            ];

            $xliffElementHelper->addNotes($postDefaultsUnit, $notes);
            $xliffElementHelper->addSegment(
                $postDefaultsUnit,
                ['id' => $field, 'state' => 'initial'],
                $post->$field
            );
        }
    }

    protected function elementId(string $prefix, string $name): array
    {
        return ['id' => $prefix . '-' . $name];
    }
}
