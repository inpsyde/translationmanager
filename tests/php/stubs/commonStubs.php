<?php
\Brain\Monkey\Functions\when('wp_json_encode')
    ->alias(function ($item) {

        return json_encode($item);
    });
\Brain\Monkey\Functions\when('is_wp_error')
    ->alias(function ($response) {

        return is_a($response, 'WP_Error');
    });
