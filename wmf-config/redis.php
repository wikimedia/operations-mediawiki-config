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

$wgObjectCaches['redis-replicated'] = array(
	'class' => 'ReplicatedBagOStuff',
	// Reads go to the local DC store (except when READ_LATEST is requested)
	'readFactory' => array(
		'factory' => array( 'ObjectCache', 'getInstance' ),
		'args' => array( 'redis_local' )
	),
	// Writes go to the master DC store first and asynchronously to the other DCs
	'writeFactory' => array(
		'class' => 'MultiWriteBagOStuff',
		'args' => array( array(
			'caches' => array_merge(
				array( $wgObjectCaches['redis_master'] ),
				$remoteCaches
			),
			'replication' => 'async'
		) )
	),
	'loggroup' => 'redis'
);

unset( $remoteCaches );
