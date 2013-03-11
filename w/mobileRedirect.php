<?php
// Used to set a 3-year-long cookie to stop mobile redirects from happening
// Created by Hampton Catlin
if($_GET['expires_in_days']) {
  $date_of_expiry = time() + (intval($_GET['expires_in_days']) * 60 * 60 * 24);
} else {
  $date_of_expiry = 0;
}

setcookie( "stopMobileRedirect", "true", $date_of_expiry, "/");
$redir_to = str_replace( "\n", '', $_GET['to'] );

if ( !preg_match( "%^".preg_quote("http://".$_SERVER['SERVER_NAME']."/")."%", $redir_to ) ) {
	header("HTTP/1.1 403 Forbidden");
	die( 'Invalid target' );
}

header( 'Location: ' . $redir_to );
