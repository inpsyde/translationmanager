<?php

/**
 * Class TransientNoticeService
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */

namespace Translationmanager\Notice;

/**
 * Class TransientNoticeService
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */
class TransientNoticeService
{
    /**
     * Notice
     *
     * @return \Translationmanager\Notice\TransientNotice Everytime the same instance
     * @since 1.0.0
     */
    private static function notice()
    {
        static $notice = null;

        if (null === $notice) {
            $notice = new TransientNotice('translationmanager_general_notices');
        }

        return $notice;
    }

    /**
     * Add Notice
     *
     * @param string $message The message to store.
     * @param string $severity The severity under which the message must be stored.
     *
     * @return bool True on success false on failure
     * @since 1.0.0
     */
    public static function add_notice($message, $severity)
    {
        return self::notice()->store($message, $severity);
    }

    /**
     * Show Messages
     *
     * @return void
     * @since 1.0.0
     */
    public static function show()
    {
        self::notice()->show();
    }
}
