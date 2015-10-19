<?php
foreach ( array( 'eqiad', 'codfw' ) as $dc ) {
	$wgObjectCaches["redis_{$dc}"] = array(
		'class'    => 'RedisBagOStuff',
		'servers'  => array( "/var/run/nutcracker/redis_{$dc}.sock" ),
		'password' => $wmgRedisPassword,
		'loggroup' => 'redis',
	);
}

$wgObjectCaches['redis_master'] = $wgObjectCaches['redis_eqiad'];
$wgObjectCaches['redis_local'] = $wgObjectCaches["redis_{$wmfDatacenter}"];

// Stash that writes to the master DC store first and asynchronously to the other DCs
$wgObjectCaches['redis-multiwrite'] = array(
	'class' => 'MultiWriteBagOStuff',
	'caches' => array(
		0 => $wgObjectCaches['redis_master'],
		1 => $wgObjectCaches['redis_local']
	),
	'replication' => 'async'
);