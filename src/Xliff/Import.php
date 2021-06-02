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

use Translationmanager\Auth\Authable;
use Translationmanager\Functions;
use Translationmanager\Plugin;
use WP_Post;
use WP_Term;
use ZipArchive;

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


        $z = new ZipArchive();
        if ($z->open($fileToImport['tmp_name'])) {
            for( $i = 0; $i < $z->numFiles; $i++ ){
                $stat = $z->statIndex( $i );
                $string = $z->getFromName(basename( $stat['name'] ));
                print_r( $string . PHP_EOL );
            }
        }

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
            $_FILES['file']['type'] !== 'application/zip' ||
            !$_FILES['file']['size'] > 0
        ) {
            return [];
        }

        return $_FILES['file'];
    }
}
