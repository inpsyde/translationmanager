<?php # -*- coding: utf-8 -*-
/**
 * Bridge between the translation data and the Yoast SEO Plugin.
 *
 * Build for Yoast 4.7.1
 */

$wordpress_seo = new \Tmwp\Module\WordPress_Seo( );

add_filter( TMWP_OUTGOING_DATA, array( $wordpress_seo, 'prepare_outgoing' ) );
add_action( TMWP_UPDATED_POST, array( $wordpress_seo, 'update_translation' ), 10, 2 );