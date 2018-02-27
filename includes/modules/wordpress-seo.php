<?php # -*- coding: utf-8 -*-
/**
 * Bridge between the translation data and the Yoast SEO Plugin.
 *
 * Build for Yoast 4.7.1
 */

$wordpress_seo = new \Translationmanager\Module\WordPress_Seo( );

add_action( 'translationmanager_outgoing_data', array( $wordpress_seo, 'prepare_outgoing' ) );
add_action( 'translationmanager_updated_post', array( $wordpress_seo, 'update_translation' ), 10, 2 );