<?php

/**
 * ServiceProvider
 *
 * @author    Guido Scialfa <dev@guidoscialfa.com>
 * @package   Translation Manager
 * @copyright Copyright (c) 2018, Guido Scialfa
 * @license   GNU General Public License, version 2
 *
 * Copyright (C) 2018 Guido Scialfa <dev@guidoscialfa.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
