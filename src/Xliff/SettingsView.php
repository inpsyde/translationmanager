<?php

/**
 * ServiceProvider
 *
 * @author    Guido Scialfa <dev@guidoscialfa.com>
 * @package   Translation Manager
 * @copyright Copyright (c) 2018, Guido Scialfa
 *
 * Copyright (C) 2018 Guido Scialfa <dev@guidoscialfa.com>
 */

namespace Translationmanager\Xliff;

/**
 * Class SettingsView
 */
class SettingsView
{
    /**
     * Will add the import button in project edit screen
     */
    public function renderExportButton()
    {
        ?>
            <button id="export-XLIFF" class="button" name="export-XLIFF">
                <?php esc_html_e('Export XLIFF Data', 'translationmanager'); ?>
            </button>
        <?php
    }

    /**
     * Will add the import view in project edit screen
     */
    public function renderImportOptions()
    {
        ?>
        <div id="import-xliff" class="postbox import-xliff">
            <h2><?php esc_html_e('Import Translation From XLIFF', 'translationmanager'); ?></h2>
            <div class="inside">
                <form id="translationmanager-import-xliff"
                      class="translationmanager-import-xliff"
                      method="post"
                      enctype="multipart/form-data"
                      action="">
                    <input type="file" name="xliff-file" id="xliff-file" accept=".zip"/>
                    <input type="submit" id="import-XLIFF" class="button button-primary"
                           name="import-XLIFF" value="<?= esc_attr('Import XLIFF Data'); ?>"/>
                    <?php wp_nonce_field('translationmanager_import_xliff');?>
                </form>
            </div>
        </div>
        <?php
    }
}
