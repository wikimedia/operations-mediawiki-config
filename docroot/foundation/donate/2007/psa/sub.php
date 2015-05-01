<?php

// Redirector when there's no JS
if( isset( $_GET['lang'] )
	&& is_string( $_GET['lang'] )
	&& preg_match( '/^[a-z]+(-[a-z]+)*$/', $_GET['lang'] )
	&& file_exists( "subtitled-" . $_GET['lang'] . ".html" ) ) {
	//
	$lang = $_GET['lang'];
	$url = "subtitled-$lang.html";
} else {
	$url = "index.html";
}

header( "Location: $url" );

?>
