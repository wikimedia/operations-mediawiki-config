<?php

$remoteCaches = array();
foreach ( array( 'eqiad', 'codfw' ) as $dc ) {
	$wgObjectCaches["redis_{$dc}"] = array(
		'class'    => 'RedisBagOStuff',
		'servers'  => array( "/var/run/nutcracker/redis_{$dc}.sock" ),
		'password' => $wmgRedisPassword,
		'loggroup' => 'redis',
	);
	if ( $dc !== $wmfDatacenter ) {
		$remoteCaches[] = $wgObjectCaches["redis_{$dc}"];
	}
}
unset( $dc );

$wgObjectCaches['redis_master'] = $wgObjectCaches['redis_eqiad'];
$wgObjectCaches['redis_local'] = $wgObjectCaches["redis_{$wmfDatacenter}"];

// Stash that writes to the master DC store first and asynchronously to the other DCs
$wgObjectCaches['redis-multiwrite'] = array(
	'class' => 'MultiWriteBagOStuff',
	'caches' => array_merge(
		array( $wgObjectCaches['redis_master'] ),
		$remoteCaches
	),
	'replication' => 'async'
);

unset( $remoteCaches );
