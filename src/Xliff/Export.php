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

        $projectId = (int)filter_input(
            INPUT_POST,
            'projectId',
            FILTER_SANITIZE_NUMBER_INT
        );

        if (!$projectId) {
            wp_send_json_error('Project data is missing');
        }

        $project = get_term($projectId, 'translationmanager_project');

        if (!$project instanceof WP_Term) {
            wp_send_json_error('Invalid project name.');
        }

        $projectItems = Functions\get_project_items($projectId);

        $projectItemsByTargetLanguages = [];

        foreach ($projectItems as $item) {
            $langId = get_post_meta($item->ID, '_translationmanager_target_id', true);
            $languages = Functions\get_languages();

            if ($langId && isset($languages[$langId])) {
                $language = $languages[$langId]->get_lang_code();
                $projectItemsByTargetLanguages[$language][] = $item;
            }
        }

        $sourceLanguage = Functions\current_language();
        if (!$sourceLanguage) {
            wp_send_json_error('Invalid source language.');
        }

        $xliffFiles = [];
        $sourceLanguageCode = $sourceLanguage->get_lang_code();
        $path = $this->plugin->dir('resources/xliff-translations');
        $projectName = sanitize_file_name($project->name);

        foreach ($projectItemsByTargetLanguages as $targetLanguageCode => $projectItems) {
            $fromTargetToSource = $sourceLanguageCode . '-' . $targetLanguageCode;
            $xliffFIleName = 'Translation-' . $fromTargetToSource . '-For-' . $projectName . '.xlf';
            $xliffFilePath = $path . '/' . $xliffFIleName;
            if ($this->xliff->generateExport(
                $projectItems,
                $xliffFilePath,
                $sourceLanguageCode,
                $targetLanguageCode
            )) {
                $zip = new ZipArchive;
                $xliffZipName = 'Translation-For-' . $projectName . '.zip';
                $xliffZipPath = $path . '/' . $xliffZipName;

                if ($zip->open($xliffZipPath, ZipArchive::CREATE)!==true) {
                    wp_send_json_error("cannot open <$xliffZipPath>\n");
                }
                $zip->addFile($xliffFilePath, $xliffFIleName);
                $zip->close();
            }
        }

        $url = $this->plugin->url('resources/xliff-translations');

        $xliffFileDownloadInfo = [
            'fileName' => $xliffZipName,
            'fileUrl' => $url . '/' . $xliffZipName,
        ];

        wp_send_json_success($xliffFileDownloadInfo);
        exit;
    }
}
