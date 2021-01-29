<?php

/**
 * Transient Notice
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */

namespace Translationmanager\Notice;

/**
 * Class TransientNotice
 *
 * @since   1.0.0
 * @package Translationmanager\Notice
 */
class TransientNotice implements StorableNotice
{
    /**
     * Transient Key
     *
     * @since 1.0.0
     *
     * @var string The transient key
     */
    private $key;

    /**
     * TransientNotice constructor
     *
     * @param string $key The transient key.
     *
     * @since 1.0.0
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @inheritdoc
     */
    public function store($message, $severity)
    {
        $severity = sanitize_key($severity);

        $transient = $this->transient();
        $message = wp_kses_post($message);

        // If the same message is all-ready in the transient, let's skip it.
        if (isset($transient[$severity]) && in_array($message, $transient[$severity], true)) {
            return false;
        }

        $transient[$severity][] = $message;

        return set_transient($this->key, $transient);
    }

    /**
     * @inheritdoc
     */
    public function show()
    {
        $messages = $this->get_cleaned_transient_messages();

        if (!$messages) {
            return;
        }

        foreach ($messages as $severity => $list) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $message = '<li>' . implode('</li>', $list) . '</li>';
            include \Translationmanager\Functions\get_template('/views/notice/list.php');
        }

        $this->clean();
    }

    /**
     * Clean the transient
     *
     * @return void
     * @since 1.0.0
     */
    private function clean()
    {
        delete_transient($this->key);
    }

    /**
     * Get the cleaned version of the messages
     *
     * @return array
     * @uses  wp_kses_post() To clean the message string.
     * @uses  sanitize_key() To clean the severity key.
     *
     * @since 1.0.0
     */
    private function get_cleaned_transient_messages()
    {
        $cleaned = [];

        foreach ($this->transient() as $severity => $messages) {
            $severity = sanitize_key($severity);

            foreach ($messages as $message) {
                $cleaned[$severity][] = wp_kses_post($message);
            }
        }

        return $cleaned;
    }

    /**
     * Get the transient
     *
     * @return array The transient value
     * @since 1.0.0
     */
    private function transient()
    {
        return array_filter((array)get_transient($this->key));
    }
}
