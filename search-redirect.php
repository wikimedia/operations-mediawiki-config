<?php
// get params
$language = $_GET['language'];
$search   = $_GET['search'];
$fulltext = isset( $_GET['fulltext'] ) ? $_GET['fulltext'] : false;
$go       = $_GET['go'];

if ( isset( $_GET['family'] ) ) {
	$family = $_GET['family'];
} else {
	$family = 'wikipedia';
}

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
$sites = array(
	'wikipedia',
	'wiktionary',
	'wikisource',
	'wikinews',
	'wikiversity',
	'wikimedia',
	'wikiquote',
	'wikibooks',
	'wikivoyage',
);
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
