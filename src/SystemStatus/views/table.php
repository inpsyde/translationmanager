<?php
// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
// phpcs:disable PSR12.Files.FileHeader

foreach ($this as $information) :
    ?>

    <table class="systemstatus systemstatus--table widefat">

        <thead>
        <tr>
            <th>
                <h2>
                    <?php echo wp_kses_post($information->title()) ?>
                </h2>
            </th>
            <th></th>
        </tr>
        </thead>

        <?php
        /**
         * Before Tbody
         *
         * @param Inpsyde\SystemStatus\Data\Information The collection instance.
         */
        do_action('system_status.before_tbody', $information) ?>

        <body class="systemstatus__items">

        <?php foreach ($information->collection() as $item) : ?>
            <tr class="systemstatus__item">
                <th class="systemstatus__item-title">

                    <?php
                    /**
                     * Before Item Title
                     *
                     * @param Inpsyde\SystemStatus\Data\Information The collection instance.
                     * @param Inpsyde\SystemStatus\Item\Informative The instance of the class.
                     */
                    do_action('system_status.before_item_title', $information, $item);

                    // Item Title
                    echo esc_html($item->name());

                    /**
                     * After Item Title
                     *
                     * @param Inpsyde\SystemStatus\Data\Information The collection instance.
                     * @param Inpsyde\SystemStatus\Item\Informative The instance of the class.
                     */
                    do_action('system_status.after_item_title', $information, $item) ?>
                </th>

                <td class="systemstatus__item-content">

                    <?php
                    /**
                     * Before Item Content
                     *
                     * @param Inpsyde\SystemStatus\Data\Information The collection instance.
                     * @param Inpsyde\SystemStatus\Item\Informative The instance of the class.
                     */
                    do_action('system_status.before_item_content', $information, $item);

                    echo wp_kses_post($item->info());

                    if ($item->shortDescription()) : ?>
                        <span class="systemstatus__item-short-description">
                            <?php echo esc_html(wp_strip_all_tags($item->shortDescription())) ?>
                        </span>
                    <?php endif;

                    /**
                     * After Item Content
                     *
                     * @param Inpsyde\SystemStatus\Data\Information The collection instance.
                     * @param Inpsyde\SystemStatus\Item\Informative The instance of the class.
                     */
                    do_action('system_status.after_item_content', $information, $item) ?>
                </td>
            </tr>
        <?php endforeach; ?>

        </body>

        <?php
        /**
         * After Tbody
         *
         * @param Inpsyde\SystemStatus\Data\Information The collection instance.
         */
        do_action('system_status.after_tbody', $information) ?>

    </table>

    <?php
endforeach;