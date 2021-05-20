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

class Xliff
{

    public function __construct()
    {
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

            $xmlBody .= "
                <unit id='{$post->ID}'>
                    <segment id='post_title' state='initial'>
                        <source>{$post->post_title}</source>
                        <target>{$post->post_title}</target>
                    </segment>
                    <segment id='post_content' state='initial'>
                        <source>{$post->post_content}</source>
                        <target>{$post->post_content}</target>
                    </segment>
                </unit>
            ";
        }

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
