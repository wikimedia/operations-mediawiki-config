<?php

/**
 * Utility function to extract type-checked string values from $_GET, avoiding issues with
 * faulty/attempted breach inputs like `?family[$hello]=1`.
 *
 * @param string $key
 * @param string $default
 * @return string
 */
function wmfGetFromGET( string $key, string $default ): string {
	if ( isset( $_GET[$key] ) && is_string( $_GET[$key] ) ) {
		return $_GET[$key];
	} else {
		return $default;
	}
}

$language = wmfGetFromGET( 'language', 'en' );
$search   = wmfGetFromGET( 'search', '' );
$fulltext = (bool)wmfGetFromGET( 'fulltext', '0' );
$go       = (bool)wmfGetFromGET( 'go', '0' );
$family   = strtolower( wmfGetFromGET( 'family', 'wikipedia' ) );

if ( ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' )
	|| ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' )
) {
	$proto = 'https';
} else {
	$proto = 'http';
}

// validate $language
if ( !preg_match( '/^[a-zA-Z\-]*$/', $language ) ) {
	$language = 'en';
}

// validate $family
$sites = [
	'wikipedia',
	'wiktionary',
	'wikisource',
	'wikinews',
	'wikiversity',
	'wikimedia',
	'wikiquote',
	'wikibooks',
	'wikivoyage',
];

if ( !in_array( $family, $sites ) ) {
	$family = 'wikipedia';
}

// make url
$url = "$proto://" . $language . '.' . $family . '.org/wiki/Special:Search?search=' . urlencode( $search );
if ( $fulltext ) {
	$url .= '&fulltext=Search';
}
if ( $go ) {
	$url .= '&go=Go';
}

// Redirect
header( "Location: {$url}" );
