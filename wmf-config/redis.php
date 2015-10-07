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
