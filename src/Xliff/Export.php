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

use Translationmanager\Functions;
use Translationmanager\Plugin;
use WP_Post;
use WP_Term;
use ZipArchive;

class Export
{
    const ACTION = 'translationmanager_export_xliff';

    /**
     * Plugin
     *
     * @var Plugin
     */
    private $plugin;

    /**
     * Xliff
     *
     * @var Xliff
     */
    private $xliff;

    /**
     * ZipArchive
     *
     * @var ZipArchive
     */
    private $zip;

    /**
     * Export XLIFF constructor
     *
     * @param Plugin $plugin The plugin instance.
     *
     * @since 1.0.0
     */
    public function __construct(Plugin $plugin, Xliff $xliff)
    {
        $this->plugin = $plugin;
        $this->xliff = $xliff;
        $this->zip = new ZipArchive;
    }

    /**
     * Handle AJAX request.
     */
    public function handle()
    {
        if (!wp_doing_ajax()) {
            return;
        }

        if (!doing_action('wp_ajax_' . self::ACTION)) {
            wp_send_json_error('Invalid action.');
        }

        $projectId = $this->projectIdFromRequest();
        if (!$projectId) {
            wp_send_json_error('Project data is missing');
        }

        $project = get_term($projectId, 'translationmanager_project');
        if (!$project instanceof WP_Term) {
            wp_send_json_error('Invalid project');
        }

        $projectItemsByTargetLanguages = $this->projectItemsByTargetLanguages($projectId);
        if (empty($projectItemsByTargetLanguages)) {
            wp_send_json_error('Project data is missing');
        }

        $sourceLanguage = Functions\current_language();
        if (!$sourceLanguage) {
            wp_send_json_error('Invalid source language.');
        }

        $projectName = sanitize_file_name($project->name);

        $xliffZipName = $this->handleExport(
            $projectItemsByTargetLanguages,
            $sourceLanguage->get_lang_code(),
            $projectName
        );

        if (!$xliffZipName) {
            wp_send_json_error('There was a problem generating XLIFF ZIP archive');
        }

        $xliffFileDownloadInfo = [
            'fileName' => $xliffZipName,
            'fileUrl' => $this->xliffZipUrl($xliffZipName),
        ];

        wp_send_json_success($xliffFileDownloadInfo);
        exit;
    }

    /**
     * Will get the current project id from Ajax request
     *
     * @return int Current project id
     */
    protected function projectIdFromRequest(): int
    {
        return (int)filter_input(
            INPUT_POST,
            'projectId',
            FILTER_SANITIZE_NUMBER_INT
        );
    }

    /**
     * The Project can have items which have different target language, so
     * the method will generate an array of project items based on target language
     * for which the project item should be translated
     *
     * @param int $projectId Current project id
     * @return array of project items based on target language
     */
    protected function projectItemsByTargetLanguages(int $projectId):array
    {
        $projectItems = Functions\get_project_items($projectId);
        $projectItemsByTargetLanguages = [];

        if (empty($projectItems)) {
            return $projectItemsByTargetLanguages;
        }

        foreach ($projectItems as $item) {
            if (!$item instanceof WP_Post) {
                continue;
            }

            $langId = get_post_meta($item->ID, '_translationmanager_target_id', true);
            $languages = Functions\get_languages();

            if (!$langId || !isset($languages[$langId])) {
                continue;
            }

            $language = $languages[$langId]->get_lang_code();
            $projectItemsByTargetLanguages[$language][] = $item;
        }

        return $projectItemsByTargetLanguages;
    }

    /**
     * Will handle the export.
     * A zip archive will be generated with XLIFF files
     * for each target language of project items
     *
     * @param array $projectItemsByTargetLanguages An array of project items based on target language
     * @param string $sourceLanguageCode Source site language code
     * @param string $projectName The Current Project name is needed to generate the name of zip archive and XLIFF files
     * @return string generated ZIP archive name
     */
    protected function handleExport(
        array $projectItemsByTargetLanguages,
        string $sourceLanguageCode,
        string $projectName
    ): string {

        $xliffZipName = $this->xliffZipName($projectName);
        foreach ($projectItemsByTargetLanguages as $targetLanguageCode => $projectItems) {
            $xliffFIleName = $this->xliffFIleName($sourceLanguageCode, $targetLanguageCode, $projectName);
            $xliffFilePath = $this->xliffFilePath($xliffFIleName);
            $isExportGenerated = $this->xliff->saveDataToFile(
                $projectItems,
                $xliffFilePath,
                $sourceLanguageCode,
                $targetLanguageCode
            );

            if (!$isExportGenerated) {
                continue;
            }

            $this->addFileIntoZip($xliffFilePath, $xliffFIleName, $xliffZipName);
            unlink($this->xliffFilePath($xliffFIleName));
        }

        if (!file_exists($this->xliffZipPath($xliffZipName))) {
            return '';
        }


        return $xliffZipName;
    }

    /**
     * WIll adda file into zip archive
     *
     * @param string $xliffFilePath The path of XLIFF file which should be added into the zip archive
     * @param string $xliffFIleName The name of XLIFF file which should be added into the zip archive
     * @param string $xliffZipName The name of zip archive to generate
     */
    protected function addFileIntoZip(
        string $xliffFilePath,
        string $xliffFIleName,
        string $xliffZipName
    ) {

        $xliffZipPath = $this->xliffZipPath($xliffZipName);

        if ($this->zip->open($xliffZipPath, ZipArchive::CREATE)!==true) {
            return;
        }

        $this->zip->addFile($xliffFilePath, $xliffFIleName);
        $this->zip->close();
    }

    /**
     * Generate the XLIFF file Name
     *
     * @param string $sourceLanguage Source site language code
     * @param string $targetLanguage Target site language code
     * @param string $projectName The Current Project name is needed to generate the XLIFF file name
     * @return string The XLIFF file Name
     */
    protected function xliffFIleName(string $sourceLanguage, string $targetLanguage, string $projectName): string
    {
        $fromTargetToSource = $sourceLanguage . '-' . $targetLanguage;
        return 'Translation-' . $fromTargetToSource . '-For-' . $projectName . '.xlf';
    }

    /**
     * Generate the zip archive name
     *
     * @param string $projectName The Current Project name is needed to generate the zip archive
     * @return string The zip archive name
     */
    protected function xliffZipName(string $projectName): string
    {
        return 'Translation-For-' . $projectName . '.zip';
    }

    /**
     * Get the XLIFF file path
     *
     * @param string $xliffFIleName The XLIFF file Name for which the path should be returned
     * @return string The XLIFF file path
     */
    protected function xliffFilePath(string $xliffFIleName): string
    {
        return $this->plugin->dir('resources/xliff-translations') . '/' . $xliffFIleName;
    }

    /**
     * Get the zip archive URL by zip archive name
     *
     * @param string $xliffZipName The name of zip archive to get it's url
     * @return string the zip archive URL
     */
    protected function xliffZipUrl(string $xliffZipName): string
    {
        return $this->plugin->url('resources/xliff-translations') . '/' . $xliffZipName;
    }

    /**
     * Get the zip path by zip archive name
     *
     * @param string $xliffZipName The name of zip archive to get it's path
     * @return string the zip archive path
     */
    protected function xliffZipPath(string $xliffZipName): string
    {
        return $this->plugin->dir('resources/xliff-translations'). '/' . $xliffZipName;
    }
}
