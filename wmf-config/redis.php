<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

foreach ( [ 'eqiad', 'codfw' ] as $dc ) {
	$wgObjectCaches["redis_{$dc}"] = [
		'class'       => 'RedisBagOStuff',
		'servers'     => [ "/var/run/nutcracker/redis_{$dc}.sock" ],
		'password'    => $wmgRedisPassword,
		'loggroup'    => 'redis',
		'reportDupes' => false
	];
}

$wgObjectCaches['redis_master'] = $wgObjectCaches["redis_{$wmfMasterDatacenter}"];
$wgObjectCaches['redis_local'] = $wgObjectCaches["redis_{$wmfDatacenter}"];
