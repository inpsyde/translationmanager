<?php // -*- coding: utf-8 -*-

/*
 * This file is part of the Translation Manager package.
 *
 * (c) Guido Scialfa <dev@guidoscialfa.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Translationmanager\Module;

use Translationmanager\TranslationData;
use WP_Post;

/**
 * Class DataProcessor
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
interface DataProcessable
{

    /**
     * Prepare Data for Outgoing.
     *
     * The method have to modify the status of the Data passed to it.
     *
     * @param TranslationData $data
     *
     * @return void
     */
    public function prepare_outgoing(TranslationData $data);

    /**
     * Update Post with Translated Data
     *
     * The method could or not modify the status of the Data passed to it.
     *
     * @param \WP_Post $post
     * @param \Translationmanager\TranslationData $data
     *
     * @return void
     */
    public function update_translation(WP_Post $post, TranslationData $data);
}
