<?php

/**
 * Project Item Table List
 *
 * @since   1.0.0
 * @package Translationmanager\TableList
 */

namespace Translationmanager\TableList;

use Translationmanager\Exception\UnexpectedEntityException;
use Translationmanager\Functions;
use Translationmanager\Request;
use Translationmanager\View\Project\OrderInfo;
use WP_Query;
use WP_Term;
use WP_User;

use function Inpsyde\MultilingualPress\siteExists;
use function Inpsyde\MultilingualPress\siteLanguageName;

/**
 * Class ProjectItem
 *
 * @since   1.0.0
 * @package Translationmanager\TableList
 */
final class ProjectItem extends TableList
{
    /**
     * ProjectItem constructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        parent::__construct(
            [
                'plural' => 'posts',
                'singular' => 'post',
                'ajax' => false,
                'screen' => 'project_item',
            ]
        );

        $this->items = [];
    }

    /**
     * @inheritdoc
     */
    public function has_items()
    {
        return count($this->items());
    }

    /**
     * @inheritdoc
     */
    public function column_cb($post)
    {
        if (!current_user_can('edit_post', $post->ID)) {
            return;
        }
        ?>

        <label class="screen-reader-text" for="cb-select-<?= esc_attr($post->ID) ?>">
            <?php
            echo esc_html(
                sprintf(__('Select %s', 'translationmanager'), $post->post_title)
            );
            ?>
        </label>

        <input id="cb-select-<?= esc_attr($post->ID) ?>"
               type="checkbox"
               name="post[]"
               value="<?= esc_attr($post->ID) ?>"/>

        <?php
    }

    /**
     * @inheritdoc
     */
    public function get_columns()
    {
        $columns = parent::get_columns();

        $columns = $this->column_project($columns);
        $columns = $this->column_languages($columns);

        $columns['translationmanager_added_by'] = esc_html__('Added by', 'translationmanager');
        $columns['translationmanager_added_at'] = esc_html__('Added on', 'translationmanager');

        return $columns;
    }

    /**
     * Filter Sortable Columns
     *
     * @return array The filtered sortable columns
     * @since 1.0.0
     */
    public function get_sortable_columns()
    {
        return [
            'translationmanager_added_by' => 'translationmanager_added_by',
            'translationmanager_added_at' => 'translationmanager_added_at',
            'translationmanager_target_language_column' => 'translationmanager_target_language_column',
        ];
    }

    /**
     * @inheritdoc
     */
    public function prepare_items()
    {
        add_action(
            'pre_get_posts',
            function (WP_Query &$query) {

                // Filter By Language.
                $lang_id = filter_input(
                    INPUT_POST,
                    'translationmanager_target_language_filter',
                    FILTER_SANITIZE_NUMBER_INT
                );
                if ($lang_id && 'all' !== $lang_id) {
                    $query->set(
                        'meta_query',
                        [
                            [
                                'key' => '_translationmanager_target_id',
                                'value' => intval($lang_id),
                                'compare' => '=',
                            ],
                        ]
                    );
                }

                // Filter By User ID.
                $user_id = filter_input(
                    INPUT_POST,
                    'translationmanager_added_by_filter',
                    FILTER_SANITIZE_NUMBER_INT
                );
                if ($user_id && 'all' !== $user_id) {
                    $query->set('author', $user_id);
                }
            }
        );

        (new Request\ProjectItemBulk())->handle();

        $this->set_pagination();
    }

    /**
     * @inheritdoc
     */
    public function views()
    {
        /**
         * Action Project Item Table Views
         *
         * Fired before the table list.
         *
         * @param \Translationmanager\TableList\ProjectItem $this Instance of this class.
         *
         * @since 1.0.0
         */
        do_action('translationmanager_project_item_table_views', $this);
    }

    /**
     * Fill the Items list with posts instances
     *
     * @return array A list of \WP_Post elements
     * @since 1.0.0
     */
    public function items()
    {
        if (!$this->items) {
            try {
                $project = $this->project_id_by_request();

                if (!$project) {
                    return [];
                }
            } catch (UnexpectedEntityException $e) {
                return [];
            }

            $this->items = Functions\get_project_items(
                $project->term_id,
                [
                    'posts_per_page' => $this->get_items_per_page("edit_{$this->screen->id}_per_page"),
                    'paged' => filter_input(INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT),
                ]
            );
        }

        return $this->items;
    }

    /**
     * @inheritdoc
     */
    protected function extra_tablenav($which)
    {
        ?>
        <div class="alignleft actions">
            <?php
            if ('top' === $which && !is_singular()) {
                ob_start();

                do_action('restrict_manage_project', $this->screen->id, $which);

                // Filters.
                $this->target_language_filter_template();
                $this->added_by_filter_template();

                $output = ob_get_clean();

                if (!empty($output)) {
                    echo Functions\kses_post($output); // phpcs:ignore
                    submit_button(
                        esc_html__('Filter', 'translationmanager'),
                        '',
                        'filter_action',
                        false,
                        ['id' => 'post-query-submit']
                    );
                }

                do_action('after_filter_options', $which);
            }
            ?>
        </div>
        <?php
    }

    /**
     * @inheritdoc
     */
    protected function get_bulk_actions()
    {
        if (current_user_can('manage_options')) {
            $actions['trash'] = esc_html__('Remove from project', 'translationmanager');
        }

        return $actions;
    }

    /**
     * Project Column
     *
     * @param \WP_Post $item The post instance.
     *
     * @param          $column_name
     *
     * @since 1.0.0
     */
    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'translationmanager_source_language_column':
                $languages = Functions\current_language();

                if ($languages) {
                    echo esc_html($languages->get_label());
                    break;
                }

                // In case of failure.
                echo esc_html__('Unknown', 'translationmanager');
                break;

            case 'translationmanager_target_language_column':
                $lang_id = get_post_meta($item->ID, '_translationmanager_target_id', true);
                $languages = Functions\get_languages();

                if ($lang_id && isset($languages[$lang_id])) {
                    printf(
                        '<a href="%1$s">%2$s</a>',
                        esc_url(get_blog_details(intval($lang_id))->siteurl),
                        esc_html($languages[$lang_id]->get_label())
                    );
                    break;
                }

                $deactivatedLanguageName = siteLanguageName($lang_id);
                if ($deactivatedLanguageName !== '' && !siteExists($lang_id)) {
                    $project_id = $this->project_id_by_request()->term_id;
                    $orderInfo = new OrderInfo($project_id);
                    $orderStatus = $orderInfo->get_status_label();
                    $deactivatedLanguageNotice = $orderStatus === 'Ready to order'
                        ? __('The site has been deactivated, the item will not be sent for translation', 'translationmanager')
                        : __('The site has been deactivated, the item will not be imported', 'translationmanager');
                    printf(
                        '<span class="deactivated-site">%1$s : </span></br>
                                <span class="deactivated-notice" style="color:#D54E21">%2$s</span>',
                        esc_html($deactivatedLanguageName),
                        esc_html($deactivatedLanguageNotice)
                    );
                    break;
                }

                // In case of failure.
                echo esc_html__('Unknown', 'translationmanager');
                break;

            case 'translationmanager_added_by':
                $user = new WP_User(get_post($item->ID)->post_author);
                echo esc_html(Functions\username($user));
                break;

            case 'translationmanager_added_at':
                echo esc_html(
                    get_the_date(
                        get_option('date_format') . ' ' . get_option('time_format'),
                        $item->ID
                    )
                );
                break;
        }
    }

    /**
     * Set languages found in posts
     *
     * The function store all of the target languages found in the project items.
     * This list is then used to build the target language filter.
     *
     * @return array A list of Languages instances
     * @since 1.0.0
     */
    private function languages()
    {
        static $languages = [];

        if (empty($languages)) {
            $all_languages = Functions\get_languages();

            foreach ($all_languages as $index => $language) {
                $languages[$index] = esc_html($language->get_label());
            }
        }

        return $languages;
    }

    /**
     * Retrieve Users
     *
     * @return array An array of \WP_Users instances
     * @since 1.0.0
     */
    private function users()
    {
        static $users = null;

        if (null === $users) {
            $users = get_users(
                [
                    'fields' => 'all',
                ]
            );

            $users = $this->filter_users_by_items($users);
        }

        return $users;
    }

    /**
     * Filter users that are also post authors for the items in the list
     *
     * @param \WP_User[] $users The users list to filter.
     *
     * @return array The filtered users
     * @since 1.0.0
     */
    private function filter_users_by_items($users)
    {
        // Retrieve all of the users that has an item.
        $userItems = array_map(
            function ($item) {

                return (int)$item->post_author;
            },
            $this->items
        );

        // Filter the user that has an item associated.
        $users = array_filter(
            $users,
            function ($user) use ($userItems) {

                return in_array($user->ID, $userItems, true);
            }
        );

        return $users;
    }

    /**
     * The Target Language Filter
     *
     * @return void
     * @since 1.0.0
     *
     * phpcs:disable WordPress.Security.NonceVerification
     */
    private function target_language_filter_template()
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $bind = (object)[
            'class_attribute' => 'target-language-filter',
            'name_attribute' => 'translationmanager_target_language_filter',
            'options' => [
                    'all' => esc_html__('All Languages', 'translationmanager'),
                ] + $this->languages(),
            'current_value' => (int)sanitize_text_field(wp_unslash($_POST['translationmanager_target_language_filter'] ?? '')),
        ];

        include Functions\get_template('/views/type/select.php');
    }

    /**
     * The User Filter
     *
     * @return void
     * @since 1.0.0
     */
    private function added_by_filter_template()
    {
        $users = [];
        foreach ($this->users() as $user) {
            $users[$user->ID] = Functions\username($user);
        }

        /** @noinspection PhpUnusedLocalVariableInspection */
        $bind = (object)[
            'class_attribute' => 'added-by-filter',
            'name_attribute' => 'translationmanager_added_by_filter',
            'options' => ['all' => esc_html__('All Users', 'translationmanager')] + $users,
            'current_value' => (int)sanitize_text_field(wp_unslash($_POST['translationmanager_added_by_filter'] ?? '')),
        ];
        unset($users);

        include Functions\get_template('/views/type/select.php');
    }

    /**
     * Filter Project Column
     *
     * @param array $columns The columns items to filter.
     *
     * @return array The filtered columns
     * @since 1.0.0
     */
    private function column_project($columns)
    {
        static $request = null;

        if (null === $request) {
            $request = $_GET; // phpcs:ignore
            foreach ($request as $key => $val) {
                $request[$key] = sanitize_text_field(wp_unslash($_GET[$key] ?? ''));
            }

            $request = wp_parse_args(
                $request,
                [
                    'translationmanager_project_id' => '-1',
                ]
            );
        }

        if (isset($request['post_status']) && 'trash' === $request['post_status']) {
            // This is trash so we show no project column.
            return $columns;
        }

        if ($request['translationmanager_project_id']) {
            // Term/Project filter is active so this col is not needed.
            return $columns;
        }

        $columns['translationmanager_project'] = esc_html__('Project', 'translationmanager');

        return $columns;
    }

    /**
     * Filter Column Language
     *
     * @param array $columns The columns items to filter.
     *
     * @return array The filtered columns
     * @since 1.0.0
     */
    private function column_languages($columns)
    {
        $columns['translationmanager_source_language_column'] = esc_html__(
            'Source Language',
            'translationmanager'
        );
        $columns['translationmanager_target_language_column'] = esc_html__(
            'Target Language',
            'translationmanager'
        );

        return $columns;
    }

    /**
     * Set Pagination
     *
     * @return void
     * @since 1.0.0
     */
    private function set_pagination()
    {
        try {
            $project_id = $this->project_id_by_request()->term_id;
        } catch (UnexpectedEntityException $e) {
            return;
        }

        if (!$project_id) {
            return;
        }

        $count = count(Functions\get_project_items($project_id));

        $this->set_pagination_args(
            [
                'total_items' => $count,
                'per_page' => $this->get_items_per_page("edit_{$this->screen->id}_per_page"),
            ]
        );
    }

    /**
     * Retrieve Project ID By GET request
     *
     * @return WP_Term The term retrieved by the request.
     * @throws UnexpectedEntityException
     */
    private function project_id_by_request()
    {
        $project = Functions\filter_input(
            ['translationmanager_project_id' => FILTER_SANITIZE_NUMBER_INT],
            INPUT_GET
        );

        if (is_array($project) && array_key_exists('translationmanager_project_id', $project)) {
            $project = $project['translationmanager_project_id'];
        }

        if (!$project) {
            return null;
        }

        $project = get_term($project, 'translationmanager_project');

        if (!$project instanceof WP_Term) {
            throw UnexpectedEntityException::forTermValue($project, '');
        }

        return $project;
    }
}
