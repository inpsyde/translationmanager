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

use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use stdClass;
use Translationmanager\Auth\Authable;
use Translationmanager\Functions;
use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Plugin;
use Translationmanager\Utils\NetworkState;
use WP_Post;
use WP_Term;
use WPSEO_Meta;
use ZipArchive;
use function Inpsyde\MultilingualPress\assignedLanguageTags;
use function Inpsyde\MultilingualPress\resolve;
use function Inpsyde\MultilingualPress\translationIds;

class Import
{
    const ACTION = 'translationmanager_import_xliff';

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
     * Auth
     *
     * @since 1.0.0
     *
     * @var Authable The instance to use to verify the request
     */
    private $auth;

    /**
     * User Capability
     *
     * @since 1.0.0
     *
     * @var string The capability needed by the user to be able to perform the request
     */
    private static $capability = 'manage_options';

    /**
     * Export XLIFF constructor
     *
     * @param Plugin $plugin The plugin instance.
     *
     * @since 1.0.0
     */
    public function __construct(Plugin $plugin, Xliff $xliff, Authable $auth)
    {
        $this->plugin = $plugin;
        $this->xliff = $xliff;
        $this->auth = $auth;
        $this->zip = new ZipArchive;
    }

    /**
     * Handle AJAX request.
     */
    public function handle()
    {
        if (!$this->auth->can(wp_get_current_user(), self::$capability)) {
            wp_send_json_error('Invalid capability.');
        }

        if (!wp_doing_ajax()) {
            return;
        }

        if (!doing_action('wp_ajax_' . self::ACTION)) {
            wp_send_json_error('Invalid action.');
        }

        $fileToImport = $this->getFileToImport();
        if (empty($fileToImport)) {
            wp_send_json_error('Invalid file. Please upload the correct zip file containing XLIFF translations');
        }

        if (!$this->uploadZipFile($fileToImport)) {
            wp_send_json_error('Something went wrong when uploading ZIP file, please check the ZIP file');
        }

        $targetDirLocation = $this->plugin->dir('resources/xliff-translations') . '/' . $fileToImport['name'];

        if (!$this->zip->open($targetDirLocation)) {
            wp_send_json_error('Could not open the ZIP file');
        }

        $this->zip->extractTo($this->plugin->dir('resources/xliff-translations'));
        $this->zip->close();

        $files = array_diff(scandir($this->plugin->dir('resources/xliff-translations') . '/'), ['..', '.']);
        foreach ($files as $file) {
            $fileParts = pathinfo($file);
            if (empty($fileParts['extension']) || $fileParts['extension'] !== 'zip') {
                continue;
            }

            $path = $this->plugin->dir('resources/xliff-translations'). '/' . $file;
            $importData = $this->xliff->generateDataFromFile($path);
            $sourceLanguage = $importData['languageInfo']['sourceLanguage'] ?? '';
            $targetLanguage = $importData['languageInfo']['targetLanguage'] ?? '';

            if (empty($importData['languageInfo']) || empty($sourceLanguage) || empty($targetLanguage)) {
                continue;
            }

            $allLanguageSites = assignedLanguageTags(false);
            if (!in_array($targetLanguage, $allLanguageSites)) {
                continue;
            }


            foreach ($importData['posts'] as $postId => $posts) {
                $postVars = get_object_vars(new WP_Post(new stdClass()));
                $postData = [];
                foreach ($postVars as $key => $value) {
                    if (array_key_exists($key, $posts['post_defaults'])) {
                        $postData[$key] = $posts['post_defaults'][$key];
                    }
                }
                $sourceSiteId = array_search($sourceLanguage, $allLanguageSites);
                $targetSiteId = array_search($targetLanguage, $allLanguageSites);
                $relatedPost = translationIds($postId, 'post', $sourceSiteId);

                $networkState = NetworkState::create();
                $networkState->switch_to($targetSiteId);

                $postData['ID'] = $relatedPost[$targetSiteId] ?? 0;
                $targetPostId = wp_insert_post($postData, true);

                $targetPost = $targetPostId ? get_post($targetPostId) : null;

                if ($targetPost && !empty($posts['acf_fields'])) {
                    $ignorableFields = $posts['acf_fields']['ignorable_items'] ?? [];
                    unset($posts['acf_fields']['ignorable_items']);
                    $fieldsToImport = array_merge($posts['acf_fields'], (array)json_decode($ignorableFields));
                    if (!empty($fieldsToImport)) {
                        foreach ($fieldsToImport as $fieldKey => $fieldValue) {
                            update_post_meta($targetPost->ID, $fieldKey, $fieldValue);
                        }
                    }
                }

                if ($targetPost && !empty($posts['yoast_fields'])) {
                    $yoastIgnorableFieldKeys = ['meta-robots-noindex', 'meta-robots-nofollow', 'meta-robots-adv'];
                    $yoastIgnorableFields = [];
                    foreach ($yoastIgnorableFieldKeys as $key) {
                        $yoastIgnorableFields[$key] = get_post_meta($postId, WPSEO_Meta::$meta_prefix . $key, true);
                    }
                    $fieldsToImport = array_filter(array_merge($posts['yoast_fields'], $yoastIgnorableFields));
                    if (!empty($fieldsToImport)) {
                        foreach ($fieldsToImport as $fieldKey => $fieldValue) {
                            update_post_meta($targetPost->ID, WPSEO_Meta::$meta_prefix . $fieldKey, $fieldValue);
                        }
                    }
                }

                $networkState->restore();

                if (!$targetPost) {
                    continue;
                }

                $api = resolve(ContentRelations::class);

                if (!$postData['ID'] === 0) {
                    continue;
                }

                $contentIds = [
                    $sourceSiteId => $postId,
                    $targetSiteId => $targetPost->ID,
                ];

                $api->createRelationship($contentIds, 'post');
            }
        }

        array_map('unlink', array_filter((array) glob($this->plugin->dir('resources/xliff-translations')."/*")));
        wp_send_json_success('success');
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

    protected function getFileToImport(): array
    {
        if (
            empty($_FILES) ||
            empty($_FILES['file']) ||
            empty($_FILES['file']['type']) ||
            empty($_FILES['file']['name']) ||
            empty($_FILES['file']['tmp_name']) ||
            $_FILES['file']['type'] !== 'application/zip' ||
            !$_FILES['file']['size'] > 0
        ) {
            return [];
        }

        return $_FILES['file'];
    }

    protected function uploadZipFile(array $file):bool
    {
        $fileName = $file['name'];
        $tmpName = $file['tmp_name'];
        $targetDirLocation = $this->plugin->dir('resources/xliff-translations') . '/' . $fileName;

        return move_uploaded_file($tmpName, $targetDirLocation);
    }
}
