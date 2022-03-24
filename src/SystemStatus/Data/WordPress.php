<?php

namespace Translationmanager\SystemStatus\Data;

use Translationmanager\SystemStatus\Item\Item;

class WordPress implements Information
{
    /**
     * @var array
     */
    private $collection = [];

    /**
     * @var string
     */
    private $title;

    public function __construct()
    {
        $this->title = esc_html__('WordPress', 'translationmanager');
    }

    public function title(): string
    {
        return $this->title;
    }

    public function collection(): array
    {
        return $this->collection;
    }

    public function wpVersion(): void
    {
        global $wp_version;

        if (! $wp_version) {
            return;
        }

        $this->collection['wp_version'] = new Item('WordPress Version', $wp_version);
    }

    public function isActiveNetwork(): void
    {
        $active = false;

        if (function_exists('is_multisite') && is_multisite()) {
            $active = true;
        }

        $this->collection['is_multisite'] = new Item(
            esc_html__('WP Multisite active', 'translationmanager'),
            ucfirst($this->boolToString($active))
        );
    }

    public function language(): void
    {
        $language = esc_html__('English (en_US)', 'translationmanager');
        $locale = get_locale();
        $languages = get_available_languages();

        // The en_US doesn't exist in the list.
        if (isset($languages[$locale])) {
            $language = $languages[$locale] . ' (' . $locale . ')';
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
