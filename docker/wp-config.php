<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'wordpress' );

/** MySQL database password */
define( 'DB_PASSWORD', 'wordpress' );

/** MySQL hostname */
define( 'DB_HOST', 'mariadb' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'yAJX])5Gu,&fr0W}DX?xmh8.@)nAWTa(9vB GP$SlvyA?,{oHT%F?0u#Sc4{e_Oz' );
define( 'SECURE_AUTH_KEY',   'bJYGP-FM@&^~4a$)9>_46kdz}: ~`W9dCqS{D<Xx4UW@[hpp[4zNAPf.<jiTSE%!' );
define( 'LOGGED_IN_KEY',     '}II=kBnDXNNrYMT]$y/dEV9b?VaVC2Gz;^Yu}I7TL_SvY^(p,S[6m!b;?{P!!~%a' );
define( 'NONCE_KEY',         'al9T^.sh%r/~S@>@-#:WVY/)DU4qx9^yJ~mcZ3g^/bZ4?IT5Ps(Jc{!L:e^)O%&8' );
define( 'AUTH_SALT',         'f;%0J<]p8y*<@8hszmDH03TkMJ9;Cbvd}_2(e69>y=q&=}[H-cV^8!]<GuS8tS/J' );
define( 'SECURE_AUTH_SALT',  'uZv`p%QDlKfb~wnJo]Q~/9U7B=rS;,zG67<wKGTP:FNXQoIk+0c(-uow_?4k9YPR' );
define( 'LOGGED_IN_SALT',    'V_G5T`6:BJ2zKh,!;9M3Rdi|sBVc5t4wl)V3Eh;^iRI5=&5?<f0Fd3Sp_ZJ1Z.Ka' );
define( 'NONCE_SALT',        '.%$!Ruv_<FyBKAWA|Z+z744.&A2bSBa9<jSya`Osr=&$LFf}0m8=g3*_ONV?kIlA' );
define( 'WP_CACHE_KEY_SALT', '1AUDY$Q%0Mh)]-[oYji. JW;gtVsS|!~P?|TETNLhp3<xbI%}><d0mu7cE,gGw?{' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

define('ALTERNATE_WP_CRON', true);
define( 'WP_DEBUG', true );
define( 'SCRIPT_DEBUG', true );
define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
