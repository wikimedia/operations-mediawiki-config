<?php

// Fetch a suggestion set from the OpenSearch API and translate it
// to the form that Apple expects for their Dictionary app lookups.
//
// Brion Vibber <brion@wikimedia.org>
// 2007-12-28

function dieOut( $msg='' ) {
	header( "HTTP/1.0 500 Internal Server Error" );
	die( "Wikimedia search service internal error.\n\n$msg" );
}

error_reporting(E_ALL);
ini_set("display_errors", false);

$caching = true;

$site = 'wikipedia';
$lang = 'en';
$search = '';
$limit = 20;

$allowedSites = array(
	'wikipedia',
	'wiktionary',
	'wikinews',
	'wikisource' );

if( isset( $_GET['site'] ) ) {
	if( is_string( $_GET['site'] ) && in_array( $_GET['site'], $allowedSites ) ) {
		$site = $_GET['site'];
	} else {
		dieOut( "Invalid parameter." );
	}
}

if( isset( $_GET['lang'] ) ) {
	if( preg_match( '/^[a-z]+(-[a-z]+)*$/', $_GET['lang'] ) ) {
		$lang = $_GET['lang'];
	} else {
		dieOut( "Invalid language parameter." );
	}
}

if( isset( $_GET['search'] ) ) {
	if( is_string( $_GET['search'] ) ) {
		$search = $_GET['search'];
	} else {
		dieOut( "Invalid search parameter." );
	}
}

if( isset( $_GET['limit'] ) ) {
	if( is_string( $_GET['limit'] ) && preg_match( '/^\d+/', $_GET['limit'] )
		&& intval( $_GET['limit'] ) > 0 && intval( $_GET['limit'] ) < 100 ) {
		$limit = intval( $_GET['limit'] );
	} else {
		dieOut( "Invalid limit parameter." );
	}
}

$urlSearch = urlencode( $search );

# OpenSearch JSON suggest API
$url = "http://$lang.$site.org/w/api.php?action=opensearch&search=$urlSearch&limit=$limit";
$c = curl_init( $url );
curl_setopt_array( $c, array(
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT_MS => 5000,
	CURLOPT_USERAGENT => 'Wikimedia OpenSearch to Apple Dictionary bridge'
) );
$result = curl_exec( $c );
$code = curl_getinfo( $c, CURLINFO_HTTP_CODE );
if( $result === false || !$code ) {
	dieOut( "Backend failure." );
}
if ( $code != 200 ) {
	dieOut( "Backend failure: it returned HTTP code $code." );
}

$suggest = json_decode( $result );

// Confirm return result was format we expect
// for opensearch...
if( is_array( $suggest ) && count( $suggest ) >= 2
 	&& is_string( $suggest[0] ) && is_array( $suggest[1] ) ) {
		$returnedTerm = $suggest[0];
		$results = $suggest[1];
} else {
	dieOut( "Unexpected result format." );
}

if( $caching ) {
	header( "Cache-Control: public, max-age: 1200, s-maxage: 1200" );
}
print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n" .
	"<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"></head>\n" .
	"<body>";

foreach( $results as $result ) {
	$htmlResult = htmlspecialchars( str_replace( ' ', '_', $result ) );
	print "<div><span class=\"language\">$lang</span>:<span class=\"key\">$htmlResult</span></div>";
}

if( empty( $results ) ) {
	$htmlSearch = htmlspecialchars( $search );
	print "<p>No entries found for \"$lang:$htmlSearch\"</p>";
}

print "</body></html>\n";
