<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.

if( $datacenter == 'wmflabs' ) { # safe guard
# Database configuration files for the beta labs

// Pretend we have a complex database setup...
$wgDBtype           = "mysql";
$wgDBserver         = "deployment-sql";
$wgDBprefix         = "";
$wgDBTableOptions   = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

if ( $wgDBname === 'enwikivoyage' ) {
	$wgDBserver         = "deployment-sql02";
}

} # end safe guard
