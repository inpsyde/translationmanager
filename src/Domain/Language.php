<?php

/**
 * Language
 *
 * @since   1.0.0
 * @package Translationmanager\Domain
 */

namespace Translationmanager\Domain;

/**
 * Class Language
 *
 * Represent a language as needed by TM.
 *
 * @since   1.0.0
 * @package Translationmanager\Domain
 */
class Language
{
    /**
     * Lang Code
     *
     * @since 1.0.0
     *
     * @var string The language iso code
     */
    protected $lang_code;

    /**
     * Label
     *
     * @since 1.0.0
     *
     * @var string The language label
     */
    protected $label;

    /**
     * Language constructor
     *
     * @param string $lang_code The language iso code.
     * @param string $label The language label.
     *
     * @since 1.0.0
     *
     * @todo  Lang Code: Need a better validation
     */
    public function __construct($lang_code, $label)
    {
        $this->lang_code = $lang_code;
        $this->label = $label;
    }

    /**
     * Get Label
     *
     * @return string The language label
     * @since 1.0.0
     */
    public function get_label()
    {
        return $this->label;
    }

    /**
     * Get Lang Code
     *
     * @return string The language code
     * @since 1.0.0
     */
    public function get_lang_code()
    {
        return $this->lang_code;
    }
}
