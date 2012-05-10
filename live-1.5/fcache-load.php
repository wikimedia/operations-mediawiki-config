<?php
if (php_sapi_name() == 'cgi-fcgi' 
    && (count($_COOKIE) == 0) 
    && (!isset($_REQUEST['action']) || $_REQUEST['action'] == 'view') 
    && isset($_REQUEST['title'])) {

    $md5title = md5($_REQUEST['title']);
    $urltitle = urlencode($_REQUEST['title']);
    $fcache_location = "/mnt/fcache/" . $_SERVER['SERVER_NAME'] . '/';
    $path = $fcache_location . $md5title[0] . '/' . $md5title[0] . $md5title[1] . '/' .
            $urltitle;

    if (file_exists("$path.html.gz")) {
	header("Content-Type: text/html");

	if (strstr("gzip", $_SERVER['HTTP_ACCEPT_ENCODING'])) {
		header("Content-Encoding: gzip");
		readfile("$path.html.gz");
		exit;
	}

        readgzfile("$path.html.gz");
        exit;
    }
}

require_once('index.php');
?>
