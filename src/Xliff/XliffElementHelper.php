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

use SimpleXMLElement;

class XliffElementHelper
{
    /**
     * Will generate the Header part of XLIFF markup
     *
     * @param string $sourceLanguage The source site language code
     * @param string $targetLanguage The Target site language code
     * @param string $path The path to the dir where the file will be generated
     * @return string Xliff header markup
     */
    public function xliffHeaderFooterMarkup(
        string $sourceLanguage,
        string $targetLanguage,
        string $path
    ): string {

        return "<?xml version='1.0' encoding='UTF-8'?><xliff xmlns='urn:oasis:names:tc:xliff:document:2.0'
            version='2.0' srcLang='{$sourceLanguage}' trgLang='{$targetLanguage}'>
            <file id='f1'><skeleton href='{$path}'/></file></xliff>";
    }

    /**
     * Will add the XLIFF <notes> and <note> elements
     *
     * @param SimpleXMLElement $element The SimpleXML Element where the notes should be added
     * @param array $notes The array of note id and note value
     */
    public function addNotes(SimpleXMLElement $element, array $notes): void
    {
        if (empty($notes)) {
            return;
        }

        $elementNotes = $element->addChild('notes');
        foreach ($notes as $noteId => $note) {
            if (!is_string($note)) {
                return;
            }
            $elementNotes->addChild('note', $note);
        }
    }

    /**
     * Will add the XLIFF <segment> element
     *
     * @param SimpleXMLElement $element The SimpleXML Element where the <segment> should be added
     * @param array $atts The array of <segment> attributes
     * @param string $source The value of <source> element of <segment>
     * @param string $target The value of <target> element of <segment>
     */
    public function addSegment(
        SimpleXMLElement $element,
        array $atts = [],
        string $source = '',
        string $target = ''
    ): void {

        $segment = $element->addChild('segment');

        if (!empty($atts)) {
            foreach ($atts as $attrName => $attrValue) {
                $segment->addAttribute($attrName, $attrValue);
            }
        }

        $segment->addChild('source', html_entity_decode($source) ?? '');
        $segment->addChild('target', !empty($target) ? html_entity_decode($target) : html_entity_decode($source));
    }

    /**
     * Will add the XLIFF <ignorable> element
     *
     * @param SimpleXMLElement $element The SimpleXML Element where the <ignorable> should be added
     * @param array $target The value of <target> element of <ignorable>
     */
    public function addIgnorable(SimpleXMLElement $element, array $target, array $id): void
    {
        if (empty($target)) {
            return;
        }

        $ignorableUnit = $this->addUnit($element, $id);
        $this->addNotes($ignorableUnit, ['Please ignore these']);
        $this->addSegment($ignorableUnit, [], '', json_encode($target));
    }

    /**
     * Will add the XLIFF <unit> element
     *
     * @param SimpleXMLElement $element The SimpleXML Element where the <unit> should be added
     * @param array $attributes The array of <unit> attributes
     * @return SimpleXMLElement created <unit> element
     */
    public function addUnit(SimpleXMLElement $element, array $attributes = []): SimpleXMLElement
    {
        $unit = $element->addChild('unit');
        if (!empty($attributes)) {
            foreach ($attributes as $attrName => $attrValue) {
                $unit->addAttribute($attrName, $attrValue);
            }
        }

        return $unit;
    }

    /**
     * Will add the XLIFF <group> element
     *
     * @param SimpleXMLElement $element The SimpleXML Element where the <unit> should be added
     * @param array $attributes The array of <group> attributes
     * @return SimpleXMLElement created <group> element
     */
    public function addGroup(SimpleXMLElement $element, array $attributes = []): SimpleXMLElement
    {
        $group = $element->addChild('group');
        if (!empty($attributes)) {
            foreach ($attributes as $attrName => $attrValue) {
                $group->addAttribute($attrName, $attrValue);
            }
        }

        return $group;
    }

    public function getElementAttribute(
        SimpleXMLElement $element,
        string $attribute,
        bool $removeIntPart = false
    ): string {

        if (!isset($element[$attribute])) {
            return '';
        }

        $attribute = (string) $element[$attribute];

        return $removeIntPart ? substr($attribute, strpos($attribute, "-") + 1) : $attribute;
    }
}
