<?php # -*- coding: utf-8 -*-
/**
 * Bridge between the translation data and the Yoast SEO Plugin.
 *
 * Build for Yoast 4.7.1
 *
 * @version 2017.03.01
 * @author  toscho
 */

$wordpress_seo = new \Tm4mlp\Module\WordPress_Seo( );

// Prepare outgoing data. These will be sent back later with the same keys.
add_filter( TM4MLP_SANITIZE_POST, array( $wordpress_seo, 'prepare_outgoing' ), 10, 2 );
