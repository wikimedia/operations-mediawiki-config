<?php

include 'inc/config.php';
include 'inc/sanity.php';

set_error_handler(

    function($errno, $message, $file, $line, $context)
    {
        if ($errno === E_WARNING) {
            backtrace();
        }
        return false;
    }
);

function db()
{
    static $db = null;
    global $db_host, $db_user, $db_pass, $db_name;
    if (is_null($db) || !is_resource($db))
    {
        e("db connect: host");
        $db = @mysql_connect($db_host, $db_user, $db_pass)
            or die('database'); mysql_select_db($db_name);
    }
    return $db;
}

include 'inc/tree.php';

$tree = new Tree();
list ($clusters) = $tree->generate();

include 'inc/template.php';