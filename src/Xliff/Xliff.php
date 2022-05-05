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
use Translationmanager\Plugin;
use WP_Post;
use Translationmanager\Module\ACF\Acf;
use Translationmanager\Xliff\PostTranslatableParts\GeneralFields;
use Translationmanager\Xliff\PostTranslatableParts\AcfFields;
use Translationmanager\Xliff\PostTranslatableParts\YoastFields;

class Xliff
{
    /**
     * Acf
     *
     * @var Acf
     */
    private $acf;

    /**
     * XliffElementHelper
     *
     * @var XliffElementHelper
     */
    private $xliffElementHelper;

    /**
     * Plugin
     *
     * @var Plugin
     */
    private $plugin;

    public function __construct(Acf $acf, XliffElementHelper $xliffElementHelper, Plugin $plugin)
    {
        $this->acf = $acf;
        $this->xliffElementHelper = $xliffElementHelper;
        $this->plugin = $plugin;
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
    public function saveDataToFile(
        array $posts,
        string $xliffFilePath,
        string $sourceLanguage,
        string $targetLanguage
    ): bool {

        if (empty($posts)) {
            return false;
        }

        $xliff = new SimpleXMLElement(
            $this->xliffElementHelper->xliffHeaderFooterMarkup(
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

            $postEntity = $this->xliffElementHelper->addGroup($xliff->file, ['id' => (string)$sourcePost->ID]);
            new GeneralFields($sourcePost, $postEntity, $this->xliffElementHelper);
            new AcfFields($sourcePost, $postEntity, $this->xliffElementHelper, $this->acf);
            new YoastFields($sourcePost, $postEntity, $this->xliffElementHelper);
        }

        return $xliff->saveXML($xliffFilePath);
    }

    /**
     * Will generate the Post data from the XLIFF file
     *
     * @param string $file from which to extract the post data
     * @return array|array[] The generated posts array to import
     */
    public function generateDataFromFile(string $file): array
    {
        if (!file_exists($file)) {
            return [];
        }

        $xliffData = simplexml_load_file($file);
        if (!$xliffData) {
            return[];
        }

        $postsToImport = [
            'languageInfo' => [
                'sourceLanguage' => $this->xliffElementHelper->getElementAttribute($xliffData, 'srcLang'),
                'targetLanguage' => $this->xliffElementHelper->getElementAttribute($xliffData, 'trgLang'),
            ],
        ];

        foreach ($xliffData->file->children() as $child) {
            $sourcePostId = $this->xliffElementHelper->getElementAttribute($child, 'id');
            if (!$sourcePostId) {
                continue;
            }

            foreach ($child->children() as $postEntities) {
                $postEntity = $this->xliffElementHelper->getElementAttribute($postEntities, 'id', true);
                foreach ($postEntities->children() as $unit) {
                    $unitName = $this->xliffElementHelper->getElementAttribute($unit, 'id', true);
                    $postsToImport['posts'][$sourcePostId][$postEntity][$unitName] = (string)$unit->segment->target;
                    $postsToImport['posts'][$sourcePostId][$postEntity]['post_type'] = get_post_type((int)$sourcePostId);
                }
            }
        }

        return $postsToImport;
    }

    /**
     * Generate the XLIFF file Name
     *
     * @param string $sourceLanguage Source site language code
     * @param string $targetLanguage Target site language code
     * @param string $projectName The Current Project name is needed to generate the XLIFF file name
     * @return string The XLIFF file Name
     */
    public function xliffFIleName(string $sourceLanguage, string $targetLanguage, string $projectName): string
    {
        $fromTargetToSource = $sourceLanguage . '-' . $targetLanguage;
        return 'Translation-' . $fromTargetToSource . '-For-' . $projectName . '.xlf';
    }

    /**
     * Path to translations dir
     *
     * @return string path to translations dir
     */
    public function translationsDir(bool $url = false): string
    {
        return $url
            ? $this->plugin->url('resources/xliff-translations') . '/'
            : $this->plugin->dir('resources/xliff-translations') . '/';
    }

    /**
     * Generate the zip archive name
     *
     * @param string $projectName The Current Project name is needed to generate the zip archive
     * @return string The zip archive name
     */
    public function xliffZipName(string $projectName): string
    {
        return 'Translation-For-' . $projectName . '.zip';
    }

    /**
     * Get the XLIFF file path
     *
     * @param string $xliffFIleName The XLIFF file Name for which the path should be returned
     * @return string The XLIFF file path
     */
    public function xliffFilePath(string $xliffFIleName): string
    {
        return $this->translationsDir() . $xliffFIleName;
    }

    /**
     * Get the zip archive URL by zip archive name
     *
     * @param string $xliffZipName The name of zip archive to get it's url
     * @return string the zip archive URL
     */
    public function xliffZipUrl(string $xliffZipName): string
    {
        return $this->translationsDir(true) . $xliffZipName;
    }

    /**
     * Get the zip path by zip archive name
     *
     * @param string $xliffZipName The name of zip archive to get it's path
     * @return string the zip archive path
     */
    public function xliffZipPath(string $xliffZipName): string
    {
        return $this->translationsDir() . $xliffZipName;
    }
}
