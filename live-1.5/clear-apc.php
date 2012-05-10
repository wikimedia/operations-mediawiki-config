<?php

$ip = $_SERVER['REMOTE_ADDR'];
$subnets = array(
	'127.0.0.1',
	'10.',
	'66.230.200.',
	'208.80.15',
);
$good = false;
foreach ( $subnets as $subnet ) {
	if ( substr( $ip, 0, strlen( $subnet ) ) == $subnet ) {
		$good = true;
		break;
	}
}
if ( !$good ) {
	header( 'HTTP/1.1 403 Not Authorized' );
	echo "Not Authorized\n";
	exit;
}

if ( function_exists( 'apc_clear_cache' ) ) {
	apc_clear_cache();
	echo "Cache cleared\n";
} else {
	echo "Unable to clear cache: APC disabled\n";
}
?>
