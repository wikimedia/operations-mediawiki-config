<?php

global $wmfCluster, $wmfDatacenter, $wmfRealm, $wmgRealm;

$wmfCluster = trim( file_get_contents( '/etc/wikimedia-cluster' ) );
if ( $wmfCluster === 'labs' ) {
	$wmfRealm = $wmgRealm = 'labs';
	$wmfDatacenter = 'eqiad';
} else {
	$wmfRealm = $wmgRealm = 'production';
	$wmfDatacenter = $wmfCluster;
}
