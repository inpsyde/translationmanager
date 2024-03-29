<?php

use function Brain\Monkey\Functions\when;

when('wp_remote_retrieve_response_code')->alias(function ($response) {

    if (is_a($response, 'WP_Error') || !isset($response['response']['code'])) {
        return '';
    }

    return $response['response']['code'];
});
when('wp_remote_retrieve_body')->alias(function ($response) {

    if (is_a($response, 'WP_Error') || !isset($response['body'])) {
        return '';
    }

    return $response['body'];
});

when('wp_json_encode')->alias(function ($item) {

        return json_encode($item);
    });
when('is_wp_error')->alias(function ($response) {

        return is_a($response, 'WP_Error');
    });
