<?php

date_default_timezone_set( 'UTC' );
header( 'Access-Control-Allow-Origin: *' );
header( 'Content-Type: text/plain; charset=utf-8' );

$version = isset( $_GET['version'] ) && is_numeric( $_GET['version'] ) ? intval( $_GET['version'] ) : '-';
$date = date( 'r' );

$uname = posix_uname();
if ( !isset( $uname['nodename'] ) || $uname['nodename'] !== 'mw1017' ) {
	header( 'HTTP/1.0 404 Not Found' );
	echo "/* nooo */\n";
	exit;
}

$ok = file_put_contents( '/tmp/rl-test.log', "[$date] version: $version\n", FILE_APPEND );
echo $ok ? "/* yep */\n" : "/* nope */\n";
exit;
