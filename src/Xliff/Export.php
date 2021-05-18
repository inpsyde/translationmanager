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
use Translationmanager\Plugin;
use WP_Term;

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
     * Export XLIFF constructor
     *
     * @param Plugin $plugin The plugin instance.
     *
     * @since 1.0.0
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
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

        $simplexml= new SimpleXMLElement('<?xml version="1.0"?><books/>');

        $book1= $simplexml->addChild('book');
        $book1->addChild("booktitle", "The Wandering Oz");
        $book1->addChild("publicationdate", '2007');

        $book2= $simplexml->addChild('book');
        $book2->addChild("booktitle", "The Roaming Fox");
        $book2->addChild("publicationdate", '2009');

        $book3= $simplexml->addChild('book');
        $book3->addChild("booktitle", "The Dominant Lion");
        $book3->addChild("publicationdate", '2012');

        $path = $this->plugin->dir('resources/xliff-translations');
        $url = $this->plugin->url('resources/xliff-translations');

        $xliffFIleName = 'Translation-For-' . sanitize_file_name($project->name) . '.xml';
        $xliffFilePath = $path . '/' . $xliffFIleName;

        $simplexml->saveXML($xliffFilePath);

        $xliffFileDownloadInfo = [
            'fileName' => $xliffFIleName,
            'fileUrl' => $url . '/' . $xliffFIleName,
        ];

        wp_send_json_success($xliffFileDownloadInfo);
        exit;
    }
}
