<?php

define( 'SITE_ROOT', normalize_slashes( dirname( dirname( __FILE__ ) ) ) );
$__url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http' ) . '://';
$__url .= $_SERVER['HTTP_HOST'] . str_replace( $_SERVER['DOCUMENT_ROOT'], '', SITE_ROOT );
define( 'SITE_URL', normalize_slashes( $__url ) );

function site_file( $file_name ) {
	return SITE_ROOT . '/' . normalize_slashes( $file_name );
}

function site_url( $resource_name ) {
	return SITE_URL . '/' . normalize_slashes( $resource_name );
}

$__request_file = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
define( 'ACTION', normalize_slashes( str_replace( SITE_ROOT, '', $__request_file ) ) );

function normalize_slashes( $uri ) {
	return trim( str_replace( '\\', '/', $uri ), '/' );
}

function print_lolek_i_bolek() {
	echo '<h2>Lolek i bolek</h2>';
}

require( site_file( ACTION . '.php' ) );
