<?php
/**
 * Register the options page.
 *
 * @package translationmanager
 */

$_translationmanager_api_options = new \Translationmanager\Admin\Options_Page();

add_action( 'admin_menu', array( $_translationmanager_api_options, 'add_options_page' ) );
add_action( 'admin_init', array( $_translationmanager_api_options, 'register_setting' ) );
add_action( 'admin_head', array( $_translationmanager_api_options, 'enqueue_style' ) );
add_action( 'admin_head', array( $_translationmanager_api_options, 'enqueue_script' ) );
