<?php

# -*- coding: utf-8 -*-

namespace Translationmanager\Module\ACF;

/**
 * Class Acf
 *
 * To generate the translatable Acf keys
 */
class Acf
{
    /**
     * ACF flexible field's layout key
     * It is used to exclude the key from sync keys
     */
    const FLEXIBLE_FIELD_LAYOUT_KEY = 'acf_fc_layout';

    const _NAMESPACE = 'ACF';

    /**
     * ACF field Types
     */
    const FIELD_TYPE_GROUP = 'group';
    const FIELD_TYPE_REPEATER = 'repeater';
    const FIELD_TYPE_FLEXIBLE = 'flexible_content';
    const TRANSLATABLE_FIELD_TYPES = [
        'text',
        'textarea',
        'wysiwyg',
        self::FIELD_TYPE_GROUP,
        self::FIELD_TYPE_REPEATER,
        self::FIELD_TYPE_FLEXIBLE,
    ];

    /**
     * Find the appropriate ACF meta keys
     *
     * This method will receive the ACF fields and
     * will find the appropriate meta keys depending on field type
     *
     * @param array $fields the array of advanced custom fields
     * @param array $keys the array of meta keys to translate
     * @param int $postID The source post id
     * @return array the array of meta keys to be synced
     *
     * phpcs:disable Generic.Metrics.NestingLevel.MaxExceeded
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.MaxExceeded
     * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
     */
    public function acfFieldKeys(array $fields, array $keys, int $postID): array
    {
        // phpcs:enable
        foreach ($fields as $filedKey => $field) {
            if (!in_array($field['type'], self::TRANSLATABLE_FIELD_TYPES, true)) {
                continue;
            }
            switch ($field['type']) {
                case self::FIELD_TYPE_GROUP:
                case self::FIELD_TYPE_REPEATER:
                case self::FIELD_TYPE_FLEXIBLE:
                    if (empty($field['value']) || empty($field['name'])) {
                        break;
                    }
                    $foundKeys = $this->recursivelyFindLayoutFieldKeys($field['value'], $field['name'], $postID);
                    foreach ($foundKeys as $key => $value) {
                        $fieldType = $this->getFieldTypeByKey($key, $postID);
                        if ($fieldType === self::FIELD_TYPE_REPEATER && !empty($value)) {
                            $keys['to-not-translate'][$key] = count($value);
                            continue;
                        }

                        if ($fieldType === self::FIELD_TYPE_GROUP || $fieldType === self::FIELD_TYPE_FLEXIBLE) {
                            continue;
                        }

                        $keys[$key] = $value;
                    }
                    if ($field['type'] === self::FIELD_TYPE_FLEXIBLE) {
                        $layoutArr = [];
                        foreach ($field['value'] as $value) {
                            if (isset($value['acf_fc_layout'])) {
                                $layoutArr[] = $value['acf_fc_layout'];
                            }
                        }
                        $keys['to-not-translate'][$filedKey] = $layoutArr;
                    }
                    if ($field['type'] === self::FIELD_TYPE_REPEATER) {
                        $keys['to-not-translate'][$field['name']] = count($field['value']);
                    }
                    break;
                default:
                    $keys[$filedKey] = $field['value'];
            }
        }

        return $keys;
    }

    /**
     * Recursively loop over the layout fields
     *
     * This Method will recursively loop over the layout fields and will generate the necessary keys
     *
     * @param array $array the array of fields
     * @param string $parentKey The key of the parent field to bind with the current key
     * @param int $postID The source post id
     * @return array the array of the generated keys
     */
    protected function recursivelyFindLayoutFieldKeys(array $array, string $parentKey, int $postID): array
    {
        $keys = [];
        foreach ($array as $key => $value) {
            $newKey = $parentKey . '_' . $key;
            $fieldType = $this->getFieldTypeByKey($newKey, $postID);

            if (is_array($value)) {
                $keys = array_merge($keys, $this->recursivelyFindLayoutFieldKeys($value, $newKey, $postID));
            }

            if (
                $key === self::FLEXIBLE_FIELD_LAYOUT_KEY ||
                !in_array($fieldType, self::TRANSLATABLE_FIELD_TYPES, true)
            ) {
                continue;
            }

            $keys[$newKey] = $value;
        }
        return $keys;
    }

    /**
     * Get post's ACF field type by field key
     *
     * @param string $key The ACF field Key
     * @param int $postID the source project post id
     * @return string Field type of ACF field
     */
    protected function getFieldTypeByKey(string $key, int $postID): string
    {
        if (empty($key) || empty($postID)) {
            return '';
        }

        $acfKey = get_post_meta($postID, '_' . $key, true);
        $acfFieldObject = get_field_object($acfKey);

        if (empty($acfFieldObject) && !empty($acfKey)) {
            $fieldKeyPosition = $this->getClonedFieldKeyPosition($acfKey, 'field_', 2);
            $acfFieldObject = get_field_object(substr($acfKey, -$fieldKeyPosition));
        }

        return !empty($acfFieldObject) ? $acfFieldObject['type'] : '';
    }

    /**
     * Will get the cloned filed position, the real key position
     *
     * @param string $key The ACF field Key
     * @param string $needle The needle to find prefix and real key
     * @param int $number from which occurrence of needle to find the position
     * @return false|int
     */
    protected function getClonedFieldKeyPosition(string $key, string $needle, int $number = 0)
    {
        return strpos(
            $key,
            $needle,
            $number > 1 ?
                    $this->getClonedFieldKeyPosition($key, $needle, $number - 1) + strlen($needle) : 0
        ) - 1;
    }
}
