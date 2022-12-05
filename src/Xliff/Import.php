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
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use stdClass;
use Translationmanager\Auth\Authable;
use Translationmanager\Utils\NetworkState;
use WP_Post;
use WPSEO_Meta;
use ZipArchive;

use function Inpsyde\MultilingualPress\assignedLanguageTags;
use function Inpsyde\MultilingualPress\resolve;
use function Inpsyde\MultilingualPress\translationIds;

class Import
{
    const ACTION = 'translationmanager_import_xliff';

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
     * @param Xliff $xliff The xliff instance.
     * @param Authable $auth The auth instance.
     *
     * @since 1.0.0
     */
    public function __construct(Xliff $xliff, Authable $auth)
    {
        $this->xliff = $xliff;
        $this->auth = $auth;
        $this->zip = new ZipArchive();
    }

    /**
     * Handle AJAX request.
     */
    public function handle(): void
    {
        if (!$this->auth->can(wp_get_current_user(), self::$capability)) {
            wp_send_json_error('Invalid capability.');
        }
        $this->deleteFiles();

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

        if (!$this->extractZipFile($this->xliff->xliffZipPath($fileToImport['name']))) {
            wp_send_json_error('Could not extract the ZIP file');
        }

        $files = array_diff(scandir($this->xliff->translationsDir()), ['..', '.']);
        $this->handleImport($files);
        $this->deleteFiles();

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

    /**
     * return zip file to import
     *
     * @return array zip file to import
     *
     * phpcs:disable
     */
    protected function getFileToImport(): array
    {
        if (
            empty($_FILES) ||
            empty($_FILES['file']) ||
            empty($_FILES['file']['type']) ||
            empty($_FILES['file']['name']) ||
            empty($_FILES['file']['tmp_name']) ||
            $_FILES['file']['type'] !== 'application/zip' ||
            empty($_FILES['file']['size']) ||
            $_FILES['file']['size'] === 0
        ) {
            return [];
        }

        return $_FILES['file'];
        // phpcs:enable
    }

    /**
     * Will upload the zip file
     *
     * @param array $file data to upload
     * @return bool true or false if uploaded or not
     */
    protected function uploadZipFile(array $file): bool
    {
        $fileName = $file['name'];
        $tmpName = $file['tmp_name'];

        return move_uploaded_file($tmpName, $this->xliff->xliffZipPath($fileName));
    }

    /**
     * Will extract the zip file
     *
     * @param string $targetDirLocation the path where to extract
     * @return bool true or false if the zip is extracted or not
     */
    protected function extractZipFile(string $targetDirLocation): bool
    {
        if (!$this->zip->open($targetDirLocation)) {
            return false;
        }

        if (!$this->zip->extractTo($this->xliff->translationsDir())) {
            return false;
        }

        $this->zip->close();
        return true;
    }

    /**
     * Will get the generated data from XLIFF file and will import into target site
     *
     * @param array $files from which to get the data
     * @throws NonexistentTable
     */
    protected function handleImport(array $files): void
    {
        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            $fileParts = pathinfo($file);
            if (empty($fileParts['extension']) || $fileParts['extension'] === 'zip') {
                continue;
            }

            $importData = $this->xliff->generateDataFromFile($this->xliff->xliffFilePath($file));
            $sourceSiteId = $this->siteIdFromLocale($importData['languageInfo']['sourceLanguage'] ?? '');
            $targetSiteId = $this->siteIdFromLocale($importData['languageInfo']['targetLanguage'] ?? '');
            if (!$sourceSiteId || !$targetSiteId || empty($importData['posts'])) {
                continue;
            }

            foreach ($importData['posts'] as $postId => $posts) {
                $postVars = get_object_vars(new WP_Post(new stdClass()));
                $postData = [];
                foreach ($postVars as $key => $value) {
                    if (array_key_exists($key, $posts['post_defaults'])) {
                        $postData[$key] = $posts['post_defaults'][$key];
                    }
                    $relatedPost = translationIds($postId, 'post', $sourceSiteId);
                    $postData['ID'] = $relatedPost[$targetSiteId] ?? 0;
                }

                $targetPost = $this->importPost($postId, $targetSiteId, $postData, $posts);

                if (!$targetPost) {
                    continue;
                }

                $this->maybeConnectContent(
                    [$sourceSiteId => $postId, $targetSiteId => $targetPost->ID],
                    $postData['ID'] === 0
                );
            }
        }
    }

    /**
     * Will Import the post data
     *
     * @param int $sourcePostId the post id from which the data is taken
     * @param int $targetSiteId the site id where the data should be imported
     * @param array $postData the post data to import
     * @param array $posts The xliff data
     * @return false|WP_Post false if the post is not inserted otherwise inserted post object
     * @throws NonexistentTable
     */
    protected function importPost(
        int $sourcePostId,
        int $targetSiteId,
        array $postData,
        array $posts
    ) {

        $networkState = NetworkState::create();
        $networkState->switch_to($targetSiteId);

        $targetPost = get_post($postData['ID']) ?? false;
        if ($targetPost) {
            $postData = array_merge((array)$targetPost, $postData);
        }
        $targetPostId = wp_insert_post($postData, true);
        $targetPost = $targetPostId ? get_post($targetPostId) : null;

        if (!$targetPost) {
            return false;
        }

        $this->importAcfFields($targetPost, $posts['acf_fields'] ?? []);
        $this->importYoastFields($targetPost, $posts['yoast_fields'] ?? [], $sourcePostId);

        $networkState->restore();

        return $targetPost;
    }

    /**
     * will update target post meta for ACF fields
     *
     * @param WP_Post|null $targetPost the target post object for which to update the meta
     * @param array $acfFieldKeys the ACF field keys to update
     */
    protected function importAcfFields(?WP_Post $targetPost, array $acfFieldKeys): void
    {
        if (!$targetPost || empty($acfFieldKeys)) {
            return;
        }

        $ignorableFields = $acfFieldKeys['ignorable_items'] ?? [];
        unset($acfFieldKeys['ignorable_items']);

        $fieldsToImport = array_merge($acfFieldKeys, (array)json_decode($ignorableFields));
        if (empty($fieldsToImport)) {
            return;
        }

        foreach ($fieldsToImport as $fieldKey => $fieldValue) {
            update_post_meta($targetPost->ID, $fieldKey, $fieldValue);
        }
    }

    /**
     * will update target post meta for YOAST fields
     *
     * @param WP_Post|null $targetPost the target post object for which to update the meta
     * @param array $yoastFieldKeys the YOAST field keys to update
     * @param int $sourcePostId the source post id from which the data is generated to get ignorable YOAST field keys
     */
    protected function importYoastFields(?WP_Post $targetPost, array $yoastFieldKeys, int $sourcePostId): void
    {
        if (!$targetPost || empty($yoastFieldKeys)) {
            return;
        }

        $yoastIgnorableFieldKeys = ['meta-robots-noindex', 'meta-robots-nofollow', 'meta-robots-adv'];
        $yoastIgnorableFields = [];
        foreach ($yoastIgnorableFieldKeys as $key) {
            $yoastIgnorableFields[$key] = get_post_meta($sourcePostId, WPSEO_Meta::$meta_prefix . $key, true);
        }

        $fieldsToImport = array_filter(array_merge($yoastFieldKeys, $yoastIgnorableFields));
        if (empty($fieldsToImport)) {
            return;
        }

        foreach ($fieldsToImport as $fieldKey => $fieldValue) {
            update_post_meta($targetPost->ID, WPSEO_Meta::$meta_prefix . $fieldKey, $fieldValue);
        }
    }

    /**
     * If the target post wasn't existed and was created then we need to connect it with the source post
     *
     * @param array $contentIds The content ids to connect ['siteId' => 'postId', 'targetSiteId' => 'targetPostId']
     * @param bool $targetPostIsNew should be true if the target post was created, false if was updated
     */
    protected function maybeConnectContent(array $contentIds, bool $targetPostIsNew): void
    {
        if (empty($contentIds) || !$targetPostIsNew) {
            return;
        }

        $api = resolve(ContentRelations::class);
        $relationshipId = $api->relationshipId(
            $contentIds,
            'post',
            true
        );
        foreach ($contentIds as $siteId => $contentId) {
            $api->saveRelation($relationshipId, $siteId, $contentId);
        }
    }

    /**
     * Will get the Site Id from locale
     *
     * @param string $locale the locale for which to get the site Id
     * @return false|int false if it doesn't exist, otherwise the site id
     * @throws NonexistentTable
     */
    protected function siteIdFromLocale(string $locale)
    {
        return array_search($locale, assignedLanguageTags(false));
    }

    /**
     * We need to delete the files if the import was successful or even not,
     * That's why this method should be called before doing the import and after import is done.
     */
    protected function deleteFiles(): void
    {
        array_map('unlink', array_filter((array) glob($this->xliff->translationsDir() . "*")));
    }
}
