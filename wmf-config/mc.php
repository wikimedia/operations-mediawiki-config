<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

if ( in_array( $wgDBname, [ 'testwiki', 'test2wiki' ], true ) ) {
	$wgMainCacheType = 'memcached-mcrouter';
} else {
	$wgMainCacheType = 'memcached-pecl';
}
$wgMemCachedPersistent = false;
$wgMemCachedTimeout = 0.25 * 1e6;  // 250ms

$wgObjectCaches['memcached-pecl'] = [
	'class'                => 'MemcachedPeclBagOStuff',
	'serializer'           => 'php',
	'persistent'           => false,
	'servers'              => defined( 'HHVM_VERSION' )
		? [ '/var/run/nutcracker/nutcracker.sock:0' ]
		: [ '127.0.0.1:11212' ],
	'server_failure_limit' => 1e9,
	'retry_timeout'        => -1,
	'loggroup'             => 'memcached',
	'timeout'              => $wgMemCachedTimeout
];

$wgObjectCaches['memcached-mcrouter'] = [
	'class' => 'MultiWriteBagOStuff',
	'caches' => [
		// new mcrouter consistent hash scheme (uses host:port)
		0 => [
			'class'                => 'MemcachedPeclBagOStuff',
			'serializer'           => 'php',
			'persistent'           => false,
			'servers'              => [ '127.0.0.1:11213' ],
			'server_failure_limit' => 1e9,
			'retry_timeout'        => -1,
			'loggroup'             => 'memcached',
			'timeout'              => $wgMemCachedTimeout
		],
		// old nutcracker consistent hash scheme (uses shard tag);
		// make sure this cache scheme gets purges and stays warm
		1 => [
			'factory' => [ 'ObjectCache', 'getInstance' ],
			'args' => [ 'memcached-pecl' ]
		],
	],
	'reportDupes' => false
];

# vim: set sts=4 sw=4 et :
