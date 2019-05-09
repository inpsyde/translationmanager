<?php

namespace Translationmanager\Utils;

use DateTimeZone;

/**
 * Class TimeZone
 *
 * @since   1.0.0
 * @package Translationmanager\Utils
 */
class TimeZone
{
    /**
     * Time Zone
     *
     * @since  1.0.0
     *
     * @var \DateTimeZone The date time zone based on option
     */
    private $timezone;

    /**
     * TimeZoneOption constructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        // Set the time zone.
        $this->timezone = $this->create_timezone();
    }

    /**
     * Get the Time Zone
     *
     * @return \DateTimeZone The \DateTimeZone instance
     * @since  1.0.0
     * @access public
     */
    public function value()
    {
        return $this->timezone;
    }

    /**
     * Get the TimeZone
     *
     * Retrieve the timezone based on WordPress option.
     *
     * @return string The timezone option value
     * @since  1.0.0
     */
    private function get_timezone_option()
    {
        // Timezone_string is empty when the option is set to Manual Offset. So we use gmt_offset.
        $option = get_option('timezone_string') ? get_option('timezone_string') : get_option('gmt_offset');
        // Set to UTC in order to prevent issue if used with DateTimeZone constructor.
        $option = (in_array($option, ['', '0'], true) ? 'UTC' : $option);
        // And remember to add the symbol.
        if (is_numeric($option) && 0 < $option) {
            $option = '+' . $option;
        }

        return $option;
    }

    /**
     * Create the time zone instance
     *
     * @return \DateTimeZone
     * @since  1.0.0
     */
    private function create_timezone()
    {
        // Get the option.
        $option = $this->get_timezone_option();

        // Return the new instance.
        return new DateTimeZone($option);
    }
}
