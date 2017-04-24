<?php
/**
 * Register the options page.
 *
 * @package pixxio-api
 */

$_pixxio_api_options = new \Tm4mlp\Admin\Options_Page();

add_action( 'admin_menu', array( $_pixxio_api_options, 'add_options_page' ) );
add_action( 'admin_init', array( $_pixxio_api_options, 'register_setting' ) );
add_action( 'admin_head', array( $_pixxio_api_options, 'enqueue_style' ) );
add_action( 'admin_head', array( $_pixxio_api_options, 'enqueue_script' ) );
