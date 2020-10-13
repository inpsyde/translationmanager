<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Module\ACF\Processor;

use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;
use Translationmanager\Module\ACF\Integrator;
use Translationmanager\Module\Processor\OutgoingProcessor;
use Translationmanager\Translation;

/**
 * Class MetaProcessor
 *
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
class OutgoingMetaProcessor implements OutgoingProcessor
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
    const TRANSLATABLE_FIELD_TYPES = ['text','textarea', 'wysiwyg', self::FIELD_TYPE_GROUP, self::FIELD_TYPE_REPEATER, self::FIELD_TYPE_FLEXIBLE];

    /**
     * @inheritDoc
     */
    public function processOutgoing(Translation $translation)
    {
        if (!$translation->is_valid()) {
            return;
        }

        $sourcePostId = $translation->source_post_id();

        $fields = get_field_objects($sourcePostId);
        $acfFields = $this->addACFFieldKeys($fields, [], $sourcePostId);
        $toNotTranslate = $acfFields['to-not-translate'];
        unset($acfFields['to-not-translate']);
        $translation->set_value(Integrator::ACF_FIELDS, $acfFields, self::_NAMESPACE);
        $translation->set_meta(Integrator::NOT_TRANSLATABE_ACF_FIELDS, $toNotTranslate, self::_NAMESPACE);
    }

    /**
     * This method will receive the ACF fields and
     * will find the appropriate meta keys depending on field type
     *
     * @param array $fields the array of advanced custom fields
     * @param array $keys the array of meta keys
     * where should be added the ACF field keys to be synced
     * @param RelationshipContext $context
     * @return array the array of meta keys to be synced
     *
     * * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     * * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
     */
    protected function addACFFieldKeys(array $fields, array $keys, $postID): array
    {
        // phpcs:enable
        foreach ($fields as $filedKey => $field) {
            if (!in_array($field['type'], self::TRANSLATABLE_FIELD_TYPES)) {
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
                        if (is_array($value) && array_key_exists('to-not-translate', $value)){
                            $keys['to-not-translate'][$key] = $value['to-not-translate'];
                        }else{
                            $keys[$key] = $value;
                        }
                    }
                    if ($field['type'] === self::FIELD_TYPE_FLEXIBLE) {
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
     * This Method will recursively loop over the layout fields and will generate the necessary keys
     *
     * @param array $array the array of fields
     * @param string $parentKey The key of the parent field to bind with the current key
     * @return array the array of the generated keys
     */
    protected function recursivelyFindLayoutFieldKeys(array $array, string $parentKey, $postID): array
    {
        $keys = [];
        foreach ($array as $key => $value) {
            $newKey = $parentKey . '_' . $key;

            if (is_array($array[$key])) {
                $keys = array_merge($keys, $this->recursivelyFindLayoutFieldKeys($array[$key], $newKey, $postID));
            }

            if ($key !== self::FLEXIBLE_FIELD_LAYOUT_KEY && !is_array($value)) {
                $acfKey = get_post_meta($postID,'_'.$newKey, true);
                $acfFieldObject = get_field_object($acfKey);
                if (in_array($acfFieldObject['type'], self::TRANSLATABLE_FIELD_TYPES)){
                    $keys[$newKey] = $value;
                }
            }

            if (is_array($value) && is_array($value[0])) {
                $keys[$newKey]['to-not-translate'] = count($value);
            }
        }
        return $keys;
    }
}
