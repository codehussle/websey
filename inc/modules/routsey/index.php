<?php

$routsey = array();
require( 'status.php' );

function routsey_load( $config ) {
	if ( !isset( $config['routes'] ) ) return;
	foreach( $config['routes'] as $route ) {
		register_route( $route );
	}
	hook_add( 'route_request', 'route_request', 0 );
	hook_register( 'page_load' );
	hook_add( 'page_load', 'page_fallback' );
}

function register_route( $route ) {
	global $routsey;
	if ( !isset( $route ) ) die( 'Route Error: Route parameter does not exist' );
	if ( !isset( $route['action'] ) ) die( 'Route Error: Required route parameter Action not set' );
	if ( isset( $routsey[$route['action']] ) ) die( 'Route Error: Route already registered: ' . $route['action'] );
	if ( !isset( $route['file'] ) ) die( 'Route Error: Required route parameter File not set' );

	$route['url'] = site_url( $route['action'] );
	$route['file'] = site_file( $route['file'] );
	if ( isset( $route['title'] ) ) {
		$route['title'] = strlen( trim( $route['title'] ) ) > 0 ? $route['title'] : $route['url'];
	} else {
		$route['title'] = $route['url'];
	}
	$routsey[$route['action']] = $route;
}

function route_request( $action ) {
	global $routsey;
	if ( !isset( $routsey[$action] ) ) route_to_error( 404 );
	$file_path = $routsey[$action]['file'];
	if ( !is_file( $file_path ) ) route_to_error( 404 );
	hook_exec( 'page_load', $routsey[$action] );
	return true;
}

function route_to_error( $error_code ) {
	header( 'HTTP/1.0 ' . $error_code . STATUS[$error_code] );
	$error_text = $error_code . ' - ' . STATUS[$error_code];
	if ( module_exists( 'platsey' ) ) {
		define( 'PAGE', array(
			'title' => $error_text,
			'status' => $error_text
		) );
		require( template_file( 'error' ) );
	} else {
		echo '<h1>' . $error_text . '</h1>';
	}
	die;
}

function page_fallback( $route_info ) {
	require( $route_info['file'] );
	return true;
}
