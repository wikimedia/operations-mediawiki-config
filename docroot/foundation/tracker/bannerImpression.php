<?php
/* This tells the user's client to ask for this resource every single time
 * but tells any cache to just ask for it once every 40 days.
 * The result is that a request for this resource will *always* appear in the cache logs (high frequency)
 * but the script will only be executed on cache-misses or once every 40 days (low frequency)
 */

$mimetype = "text/plain";

if(isset($_REQUEST['req'])){
	if($_REQUEST['req'] == "css"){
	$mimetype = "text/css";
	}
	else if($_REQUEST['req'] == "js"){
	$mimetype = "text/javascript";
	}
}

header("Cache-Control: public, max-age=0, s-maxage=604800" );
header("Content-Type: ". $mimetype);


?>

