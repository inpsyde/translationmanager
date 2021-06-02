<?php

// -*- coding: utf-8 -*-

namespace Translationmanager\Module\Mlp\Processor;

use Translationmanager\Module\Mlp\Adapter;
use Translationmanager\Module\ModuleIntegrator;
use Translationmanager\Module\Processor\IncomingProcessor;
use Translationmanager\Translation;

class ElementorSync implements IncomingProcessor
{
    const KEYS_TO_SYNC = ['_elementor_data', '_elementor_controls_usage', '_elementor_css', '_elementor_edit_mode'];

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
        if (!$this->isElementorActive()) {
            return;
        }

        $remotePost = $translation->get_meta('POST');
        $savedPost = !empty($remotePost['saved_post']) ? $remotePost['saved_post'] : '';
        if (empty($remotePost) || !$savedPost) {
            return;
        }

        $sourcePost = $translation->source_post();

        if (!$sourcePost) {
            return;
        }

        $syncOnUpdate = true;
        $isUpdateKey = $translation->get_meta(
            PostDataBuilder::IS_UPDATE_KEY,
            ModuleIntegrator::POST_DATA_NAMESPACE
        );

        if ($isUpdateKey) {
            $syncOnUpdate = apply_filters(
                'translationmanager_mlp_module_sync_post_parent_on_update',
                true,
                $translation
            );
        }

        if (!$syncOnUpdate) {
            return;
        }

        foreach (self::KEYS_TO_SYNC as $meta) {
            $sourceMeta = get_post_meta($sourcePost->ID, $meta, true);
            if (!$sourceMeta) {
                continue;
            }
            if ($meta === '_elementor_data') {
                $sourceMeta = str_replace('\\', '\\\\', $sourceMeta);
            }

            switch_to_blog($translation->target_site_id());
            update_post_meta($savedPost->ID, $meta, $sourceMeta);
            \Elementor\Plugin::$instance->files_manager->clear_cache();
            restore_current_blog();
        }
    }

    /**
     * @return bool
     */
    protected function isElementorActive(): bool
    {
        if (!did_action('elementor/loaded')) {
            return false;
        }
        return true;
    }
}
