<?php

$hooks = array();

function hook_register( $hook_name ) {
	global $hooks;
	$hooks[$hook_name] = array();
}

function hook_add( $hook_name, $function_name, $priority = 1000 ) {
	global $hooks;
	$hooks[$hook_name][] = array(
		'function' => $function_name,
		'priority' => $priority
	);
}

function hook_sort( $hook_name ) {
	global $hooks;
	usort( $hooks[$hook_name], function( $hookA, $hookB ) {
		return $hookA['priority'] <=> $hookB['priority'];
	} );
}

function hook_exec( $hook_name, $user_data = '' ) {
	global $hooks;
	hook_sort( $hook_name );
	foreach ( $hooks[$hook_name] as $hook ) {
		$result = call_user_func( $hook['function'], $user_data );
		if ( $result == true ) break;
	}
}
