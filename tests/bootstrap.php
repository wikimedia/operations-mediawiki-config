<?php

// Load the shared utilities classes from here!
require_once __DIR__ . "/DBList.php";
require_once __DIR__ . "/Provide.php";
require_once __DIR__ . "/Defines.php";
echo defined( 'HHVM_VERSION' ) ?
	'Using HHVM ' . HHVM_VERSION . ' (' . PHP_VERSION . ")\n" :
	'Using PHP ' . PHP_VERSION . "\n";
