<?php # -*- coding: utf-8 -*-

if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
	die( 'PHP 5.6 is required to run tests.' );
}

putenv( 'TESTS_PATH=' . __DIR__ );
putenv( 'LIBRARY_PATH=' . dirname( __DIR__ ) );

$vendor = dirname( dirname( __DIR__ ) ) . '/vendor/';

if ( ! realpath( $vendor ) ) {
	die( 'Please install via Composer before running tests.' );
}

if ( ! defined( 'PHPUNIT_COMPOSER_INSTALL' ) ) {
	define( 'PHPUNIT_COMPOSER_INSTALL', $vendor . 'autoload.php' );
}

error_reporting( E_ALL );

require_once $vendor . '/antecedent/patchwork/Patchwork.php';
require_once $vendor . 'autoload.php';
require_once dirname( dirname( __DIR__ ) ) . '/bootstrap.php';

unset( $vendor );
