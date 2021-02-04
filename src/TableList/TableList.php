<?php

/**
 * Base Table List
 *
 * @since   1.0.0
 * @package Translationmanager\TableList
 */

namespace Translationmanager\TableList;

use WP_List_Table;

/**
 * Class TableList
 *
 * @since   1.0.0
 * @package Translationmanager\TableList
 */
class TableList extends WP_List_Table
{
    /**
     * @inheritdoc
     */
    protected function get_sortable_columns()
    {
        return [
            'title' => 'title',
        ];
    }

    /**
     * Column Title
     *
     * @param \WP_Post $item The post from which retrieve the title.
     *
     * @return string The post title
     * @since 1.0.0
     */
    public function column_title($item)
    {
        return '<strong>' . esc_html($item->post_title) . '</strong>';
    }

    /**
     * @inheritdoc
     */
    public function get_columns()
    {
        $posts_columns = [];
        $posts_columns['cb'] = '<input type="checkbox" />';
        $posts_columns['title'] = esc_html__('Title', 'translationmanager');

        $posts_columns = apply_filters(
            "translationmanager_manage_{$this->screen->id}_columns",
            $posts_columns
        );

        return $posts_columns;
    }

    /**
     * @inheritdoc
     */
    protected function handle_row_actions($item, $column_name, $primary)
    {
        if ($primary !== $column_name) {
            return '';
        }

        return $this->row_actions(apply_filters(
            "translationmanager_{$this->screen->id}_row_actions",
            [],
            $item
        ));
    }
}
