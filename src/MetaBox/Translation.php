<?php

/**
 * Translation Box
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */

namespace Translationmanager\MetaBox;

use Brain\Nonces\WpNonce;
use Translationmanager\Functions;
use Translationmanager\Setting\PluginSettings;

/**
 * Class Translation
 *
 * @since   1.0.0
 * @package Translationmanager\MetaBox
 */
class Translation implements Boxable
{
    /**
     * @inheritdoc
     */
    public function add_meta_box()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $screen = get_current_screen();

        if (!$screen) {
            return;
        }

        // There shall be no translation option while creating a new entry.
        if ('add' === $screen->action) {
            return;
        }

        /**
         * Define where the translation box shall be shown.
         *
         * Add or remove post-types from the array.
         * By default it will be shown on 'post' and 'page'.
         * The value goes right in the `add_meta_box` screen argument.
         *
         * @return array The post types list
         * @var array Screens for `add_meta_box()`.
         *
         * @see add_meta_box()
         */
        $box_screen = apply_filters(
            'translationmanager_translation_box_screen',
            get_post_types(
                [
                    'show_ui' => true,
                ]
            )
        );

        add_meta_box(
            'translationmanager_translation_box',
            esc_html__('Request translation', 'translationmanager'),
            [$this, 'render_template'],
            $box_screen,
            'side'
        );
    }

    /**
     * @inheritdoc
     */
    public function render_template()
    {
        $template = Functions\get_template('views/meta-box/translation/layout.php');

        if (!$template || !file_exists($template)) {
            return;
        }

        require $template;
    }

    /**
     * @inheritdoc
     */
    public function nonce()
    {
        return new WpNonce('add_translation');
    }

    /**
     * Customer Key
     *
     * @return mixed Whatever the get_option() returns.
     * @since 1.0.0
     *
     * Actually used within the translation-box.php only, don't remove it.
     */
    private function get_customer_key()
    {
        return get_option(PluginSettings::API_KEY);
    }

    /**
     * Get Recent Project Name
     *
     * @return mixed Whatever the get_term_field returns
     * @since 1.0.0
     */
    private function get_recent_project_name()
    {
        if (!$this->get_recent_project_id()) {
            return esc_html__('New project', 'translationmanager');
        }

        return get_term_field('name', $this->get_recent_project_id(), 'translationmanager_project');
    }

    /**
     * Get Recent Project ID
     *
     * @return mixed Whatever the get_user_meta returns
     * @since 1.0.0
     */
    private function get_recent_project_id()
    {
        return get_user_meta(get_current_user_id(), 'translationmanager_project_recent', true);
    }

    /**
     * Submit Button Label
     *
     * @return string The button label string
     * @since 1.0.0
     */
    private function context_button_label()
    {
        $label = esc_html__('Add to project', 'translationmanager');

        if (!Functions\projects()) {
            $label = esc_html__('Create new project', 'translationmanager');
        }

        return $label;
    }
}
