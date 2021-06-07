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
     * xliffElementHelper
     *
     * @var XliffElementHelper
     */
    private $xliffElementHelper;

    public function __construct(Acf $acf, XliffElementHelper $xliffElementHelper)
    {
        $this->acf = $acf;
        $this->xliffElementHelper = $xliffElementHelper;
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

    public function generateDataFromFile($file): array
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
                'targetLanguage' => $this->xliffElementHelper->getElementAttribute($xliffData, 'trgLang')
            ]
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
                }
            }
        }

        return $postsToImport;
    }
}
