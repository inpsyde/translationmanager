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
use WP_Post;
use Translationmanager\Module\ACF\Acf;

class Xliff
{
    /**
     * Acf
     *
     * @var Acf
     */
    private $acf;

    public function __construct($acf)
    {
        $this->acf = $acf;
    }

    /**
     * Handle AJAX request.
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

        $xmlHeader = $this->xliffHeaderMarkup($sourceLanguage, $targetLanguage, $xliffFilePath);
        $xmlFooter = "</file></xliff>";

        $xmlBody = '';
        foreach ($posts as $post) {
            if (!$post instanceof WP_Post) {
                return false;
            }

            $postId = $post->_translationmanager_post_id;
            $realPost = get_post($postId);

            $fields = get_field_objects($realPost->ID);
            $acfFields = $this->acf->acfFieldKeys($fields, [], $realPost->ID);
            $acfPart = "<unit id='acf_fields'>
                            <notes>
                                <note id='acf_fields'>The ACF fields</note>
                            </notes>";
            foreach ($acfFields as $key => $value) {
                $acfPart .= "<segment id='{$key}' state='initial'>
                                <source>{$value}</source>
                                <target>{$value}</target>
                            </segment>";
            }
            $acfPart .= "</unit>";

            $xmlBody .= "
                <group id='{$realPost->ID}'>
                    <unit id='post_defaults'>
                        <notes>
                            <note id='post_defaults'>The Post default translatable fields(title and content)</note>
                        </notes>
                        <segment id='post_title' state='initial'>
                            <source>{$realPost->post_title}</source>
                            <target>{$realPost->post_title}</target>
                        </segment>
                        <segment id='post_content' state='initial'>
                            <source>{$realPost->post_content}</source>
                            <target>{$realPost->post_content}</target>
                        </segment>
                    </unit>
                    {$acfPart};
                </group>
            ";

        }write_log($xmlBody);

        $xliffStructure = $xmlHeader . $xmlBody . $xmlFooter;


        $xliff = new SimpleXMLElement($xliffStructure);

        return $xliff->saveXML($xliffFilePath);
    }

    protected function xliffHeaderMarkup(
        string $sourceLanguage,
        string $targetLanguage,
        string $path
    ): string {

        return "<?xml version='1.0' standalone='yes'?>
            <xliff xmlns='urn:oasis:names:tc:xliff:document:2.0'
            version='2.0' srcLang='{$sourceLanguage}' trgLang='{$targetLanguage}'>
            <file>
                <skeleton href='{$path}'/>";
    }
}
