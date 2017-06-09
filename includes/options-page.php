<?php
/**
 * Register the options page.
 *
 * @package tmwp
 */

$_tmwp_api_options = new \Tmwp\Admin\Options_Page();

add_action( 'admin_menu', array( $_tmwp_api_options, 'add_options_page' ) );
add_action( 'admin_init', array( $_tmwp_api_options, 'register_setting' ) );
add_action( 'admin_head', array( $_tmwp_api_options, 'enqueue_style' ) );
add_action( 'admin_head', array( $_tmwp_api_options, 'enqueue_script' ) );
