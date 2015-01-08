<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
$wgMainCacheType = 'memcached-pecl';

$wgMemCachedPersistent = false;
$wgMemCachedTimeout = 0.25 * 1e6;  // 250kus (a quarter of a second).

$wgObjectCaches['memcached-pecl'] = array(
	'class'      => 'MemcachedPeclBagOStuff',
	'serializer' => 'php',
	'persistent' => false,
	'servers'    => in_array( gethostname(), array( 'mw1230', 'mw1231' ) )
		? array( '/var/run/nutcracker/nutcracker.sock:0' )
		: array( '127.0.0.1:11212' ),
	'server_failure_limit' => 1e9,
	'retry_timeout' => -1
);

$wgBloomFilterStores['main'] = array(
	'cacheId'      => 'main-v1',
	'class'        => 'BloomCacheRedis',
	'redisServers' => array(
		'10.64.0.162:6379', // master; rbf1001
		'10.64.0.163:6379' // slave; rbf1002
	),
	'redisConfig'  => array(
		'password' => $wmgRedisPassword,
		'connectTimeout' => .25
	)
);

# vim: set sts=4 sw=4 et :
