<?php

require( 'hooks.php' );

hook_register( 'framework_init' );
hook_register( 'modules_loaded' );

hook_register( 'route_request' );
hook_add( 'route_request', 'route_fallback' );

hook_add( 'framework_init', 'detect_path_and_url' );
hook_exec( 'framework_init' );

$__modules = array();
$__config = json_decode( file_get_contents( site_file( 'config.json' ) ), true );
if ( isset( $__config['modules'] ) ) {
	foreach( $__config['modules'] as $module_name ) {
		module_load( $module_name, $__config );
	}
}
hook_exec( 'modules_loaded' );
hook_exec( 'route_request', ACTION );

function detect_path_and_url() {
	define( 'SITE_ROOT', normalize_slashes( dirname( dirname( __FILE__ ) ) ) );
	$__url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http' ) . '://';
	$__url .= $_SERVER['HTTP_HOST'] . str_replace( $_SERVER['DOCUMENT_ROOT'], '', SITE_ROOT );
	define( 'SITE_URL', normalize_slashes( $__url ) );
	$__request_file = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
	define( 'ACTION', normalize_slashes( str_replace( SITE_ROOT, '', $__request_file ) ) );
}

function route_fallback() {
	header( 'HTTP/1.0 404 Not Found' );
	echo '<h1>404 - Not Found</h1>';
	return true;
}

function module_load( $module_name, $config ) {
	global $__modules;
	if ( !module_exists( $module_name ) ) die( "Module does not exist: $module_name" );
	require( module_file( $module_name ) );
	$__modules[$module_name] = array();
	$load_function = $module_name . '_load';
	if ( function_exists( $load_function ) )
		call_user_func( $load_function, $config );
}

function module_enabled( $module_name ) {
	global $__modules;
	return module_exists( $module_name ) && isset( $__modules[$module_name] );
}

function module_exists( $module_name ) {
	return is_file( module_file( $module_name ) );
}

function module_file( $module_name, $file_name = '' ) {
	if ( empty( $file_name ) ) $file_name = 'index.php';
	return inc_file( "modules/$module_name/$file_name" );
}

function inc_file( $file_name ) {
	return site_file( "inc/$file_name" );
}

function site_file( $file_name ) {
	return SITE_ROOT . '/' . normalize_slashes( $file_name );
}

function site_url( $resource_name ) {
	return SITE_URL . '/' . normalize_slashes( $resource_name );
}

function normalize_slashes( $uri ) {
	return trim( str_replace( '\\', '/', $uri ), '/' );
}
