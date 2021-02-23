#!/usr/bin/env php
<?php
require_once dirname( __DIR__ ) . '/MWWikiversions.php';

$localsettings = <<<EOF
<?php
require __DIR__ . "/../wmf-config/CommonSettings.php";

EOF;

foreach ( MWWikiversions::getAvailableBranchDirs() as $dir ) {
	$path = $dir . "/LocalSettings.php";
	if ( !file_put_contents( $path, $localsettings, LOCK_EX ) ) {
		print "Unable to write to $path.\n";
		exit( 1 );
	}
}
