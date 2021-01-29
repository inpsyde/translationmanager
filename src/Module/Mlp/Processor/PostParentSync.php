<?php

// -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Processor;

use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Module\ModuleIntegrator;
use Translationmanager\Module\Processor\IncomingProcessor;
use Translationmanager\Translation;

class PostParentSync implements IncomingProcessor
{
    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * TaxonomiesSync constructor
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param Translation $translation
     *
     * @return void
     */
    public function processIncoming(Translation $translation)
    {
        $source_post = $translation->source_post();

        if (!$source_post || !$source_post->post_parent) {
            return;
        }

        $sync_on_update = true;
        $isUpdateKey = $translation->get_meta(
            PostDataBuilder::IS_UPDATE_KEY,
            ModuleIntegrator::POST_DATA_NAMESPACE
        );

        if ($isUpdateKey) {
            $sync_on_update = apply_filters(
                'translationmanager_mlp_module_sync_post_parent_on_update',
                true,
                $translation
            );
        }

        if (!$sync_on_update) {
            return;
        }

        $target_site_id = $translation->target_site_id();
        $source_site_id = $translation->source_site_id();

        $related_parents = $this->adapter->relations($source_site_id, $source_post->post_parent);

        $parent = array_key_exists($target_site_id, $related_parents)
            ? $related_parents[$target_site_id]
            : 0;

        $translation->set_value('post_parent', $parent);
    }
}
