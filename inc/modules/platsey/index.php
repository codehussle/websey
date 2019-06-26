<?php

function platsey_load() {
	hook_add( 'page_load', function( $route_info ) {
		if ( isset( $route_info['template'] ) ) {
			define( 'PAGE', $route_info );
			require( template_file( $route_info['template'] ) );
			return true;
		}
	}, 0 );
}

function get_template( $template_name ) {
	require( template_file( $template_name ) );
}

function template_file( $template_name ) {
	return inc_file( "templates/$template_name.php" );
}

function get_section( $section_name ) {
	require( section_file( $section_name ) );
}

function section_file( $section_name ) {
	return inc_file( "templates/_$section_name.php" );
}
