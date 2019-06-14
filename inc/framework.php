<?php

define( 'SITE_ROOT', normalize_slashes( dirname( dirname( __FILE__ ) ) ) );
$__url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http' ) . '://';
$__url .= $_SERVER['HTTP_HOST'] . str_replace( $_SERVER['DOCUMENT_ROOT'], '', SITE_ROOT );
define( 'SITE_URL', normalize_slashes( $__url ) );

$routes = array();

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

function register_route( $route ) {
	global $routes;
	if ( !isset( $route ) ) die( 'Route Error: Route parameter does not exist' );
	if ( !isset( $route['action'] ) ) die( 'Route Error: Required route parameter Action not set' );
	if ( isset( $routes[$route['action']] ) ) die( 'Route Error: Route already registered: ' . $route['action'] );
	if ( !isset( $route['file'] ) ) die( 'Route Error: Required route parameter File not set' );

	$routes[$route['action']] = array(
		'url' => site_url( $route['action'] ),
		'file' => site_file( $route['file'] ),
		'data' => $route
	);
}

function route_request( $action ) {
	global $routes;
	if ( !isset( $routes[$action] ) ) route_to_404();
	$file_path = $routes[$action]['file'];
	if ( !is_file( $file_path ) ) route_to_404();
	require( $file_path );
}

function route_to_404() {
	echo '<h1>404 - Not Found<h1>';
	die;
}

// require( site_file( ACTION . '.php' ) );
register_route( array(
	'action' => '',
	'file' => 'index.php'
) );
register_route( array(
	'action' => 'about',
	'file' => 'about.php'
) );

route_request( ACTION );
