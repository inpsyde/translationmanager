<?php

namespace Translationmanager\SystemStatus\Data;

use Translationmanager\SystemStatus\Item\Item;

class Wordpress implements Information
{
    private $collection = [];

    private $title;

    public function __construct()
    {
        $this->title = esc_html__('WordPress', 'systemstatus');
    }

    public function title()
    {
        return $this->title;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function wpVersion()
    {
        global $wp_version;

        if (! $wp_version) {
            return;
        }

        $this->collection['wp_version'] = new Item('WordPress Version', $wp_version);
    }

    public function isActiveNetwork()
    {
        $active = false;

        if (function_exists('is_multisite') && is_multisite()) {
            $active = true;
        }

        $this->collection['is_multisite'] = new Item(
            esc_html__('WP Multisite active', 'systemstatus'),
            ucfirst($this->boolToString($active))
        );
    }

    public function language()
    {
        $language = esc_html__('English (en_US)', 'systemstatus');
        $locale = get_locale();
        $languages = get_available_languages();

        // The en_US doesn't exists in the list.
        if (isset($languages[$locale])) {
            $language = $language[$locale] . ' (' . $locale . ')';
        }

        $this->collection['wp_language'] = new Item(
            'WordPress Language',
            $language
        );
    }
    
    private function boolToString(bool $bool): string
    {
        return true === $bool ? 'yes' : 'no';
    }
}
