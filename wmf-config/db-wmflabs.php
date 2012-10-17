<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.

if( $cluster == 'wmflabs' ) { # safe guard
# Database configuration files for the beta labs

// Pretend we have a complex database setup...
$wgDBtype           = "mysql";
$wgDBserver         = "deployment-sql";
$wgDBprefix         = "";
$wgDBTableOptions   = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

if ( $wgDBname === 'wikivoyage' ) {
	$wgDBtype           = "mysql";
	$wgDBserver         = "deployment-sql02";
	$wgDBprefix         = "";
	$wgDBTableOptions   = "ENGINE=InnoDB, DEFAULT CHARSET=binary";
}

} # end safe guard
