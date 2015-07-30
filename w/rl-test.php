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

$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '-';

$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '-';

$line = "[$date] vers\nion: $version; ip: $ip; ua: $ua";

// Clean up
$line = strtr( $line, array(
	"\r" => ' ',
	"\n" => ' ',
	"\t" => ' ',
) );

$ok = file_put_contents( '/tmp/rl-test.log', "$line\n", FILE_APPEND );
echo $ok ? "/* yep */\n" : "/* nope */\n";
exit;
