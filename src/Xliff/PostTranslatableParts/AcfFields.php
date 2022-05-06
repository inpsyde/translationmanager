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
use Translationmanager\Module\ACF\Acf;
use Translationmanager\Xliff\XliffElementHelper;
use WP_Post;

class AcfFields
{
    const DESCRIPTION = 'From field group: ACF Fields';

    /**
     * Will generate the XLIFF part with ACF translatable fields
     *
     * @param WP_Post $post The project item source post
     * @param SimpleXMLElement $element The element where the fields should be added
     * @param XliffElementHelper $xliffElementHelper Helper to manage XLIFF elements
     * @param Acf $acf The ACF field key generation class
     */
    public function __construct(
        WP_Post $post,
        SimpleXMLElement $element,
        XliffElementHelper $xliffElementHelper,
        Acf $acf
    ) {

        if (!class_exists('ACF')) {
            return;
        }

        $sourcePostId = $post->ID;

        $fields = get_field_objects($sourcePostId);
        if (empty($fields)) {
            return;
        }

        $acfFields = $acf->acfFieldKeys($fields, [], $sourcePostId);
        if (empty($acfFields)) {
            return;
        }

        $acfGroup = $xliffElementHelper->addGroup(
            $element,
            $this->elementId((string)$sourcePostId, 'acf_fields')
        );

        foreach ($acfFields as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            $acfUnit = $xliffElementHelper->addUnit(
                $acfGroup,
                $this->elementId((string)$sourcePostId, $key)
            );

            $notes = [
                'post_id' => "From post: $sourcePostId",
                'group_name' => self::DESCRIPTION,
                'field' => "Field: $key",
            ];
            $xliffElementHelper->addNotes($acfUnit, $notes);

            $xliffElementHelper->addSegment(
                $acfUnit,
                ['id' => $key, 'state' => 'initial'],
                $value
            );
        }

        if (!empty($acfFields['to-not-translate'])) {
            $xliffElementHelper->addIgnorable(
                $acfGroup,
                $acfFields['to-not-translate'],
                $this->elementId((string)$sourcePostId, 'ignorable_items')
            );
        }
    }

    protected function elementId(string $prefix, string $name): array
    {
        return ['id' => $prefix . '-' . $name];
    }
}
