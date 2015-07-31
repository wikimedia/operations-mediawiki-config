<?php
require '/srv/mediawiki/w/MWVersion.php';
require getMediaWiki( 'includes/WebStart.php' );
#require_once __DIR__ . '/includes/WebStart.php';

global $wgRequest;

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

$ua = @$_SERVER['HTTP_USER_AGENT'] ?: '-';
$xff = @$_SERVER['HTTP_X_FORWARDED_FOR'] ?: '-';

$line = "[$date] version: $version; xff: $xff; ua: $ua";

// Clean up
$line = strtr( $line, array(
	"\r" => ' ',
	"\n" => ' ',
	"\t" => ' ',
) );

$ok = file_put_contents( '/tmp/rl-test.log', "$line\n", FILE_APPEND );
echo $ok ? "/* yep */\n" : "/* nope */\n";
exit;
