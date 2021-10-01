<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

#
# This for PRODUCTION.
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/*Services.php
# - wmf-config/etcd.php
# - wmf-config/InitialiseSettings.php
# - wmf-config/redis.php [THIS FILE]
#
# Included from: wmf-config/CommonSettings.php.
#

// If the installation is on kubernetes, use tcp to connect to nutcracker
if ( $wmfUsingKubernetes ) {
	$nutcrackerServersByDc = [ 'eqiad' => '127.0.0.1:12000', 'codfw' => '127.0.0.1:12001' ];
} else {
	$nutcrackerServersByDc = array_map(
		$wmfDatacenters,
		function ( $dc ) {
			return "/var/run/nutcracker/redis_{$dc}.sock";
		}
	);
}
foreach ( $wmfDatacenters as $dc ) {
	$wgObjectCaches["redis_{$dc}"] = [
		'class'       => 'RedisBagOStuff',
		'servers'     => [ $nutcrackerServersByDc[$dc] ],
		'password'    => $wmgRedisPassword,
		'loggroup'    => 'redis',
		'reportDupes' => false
	];
}

$wgObjectCaches['redis_master'] = $wgObjectCaches["redis_{$wmfMasterDatacenter}"];
$wgObjectCaches['redis_local'] = $wgObjectCaches["redis_{$wmfDatacenter}"];
