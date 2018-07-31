<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

// Disabled here for sanity (although matches MediaWiki default,
// and isn't used given we set 'persistent' explicitly).
$wgMemCachedPersistent = false;
// Set timeout to 250ms (in microseconds)
$wgMemCachedTimeout = 0.25 * 1e6;

$wgObjectCaches['memcached-pecl'] = [
	'class'                => 'MemcachedPeclBagOStuff',
	'serializer'           => 'php',
	'persistent'           => false,
	'servers'              => defined( 'HHVM_VERSION' )
		? [ '/var/run/nutcracker/nutcracker.sock:0' ]
		: [ '127.0.0.1:11212' ],
	// Effectively disable the failure limit (0 is invalid)
	'server_failure_limit' => 1e9,
	// Effectively disable the retry timeout
	'retry_timeout'        => -1,
	'loggroup'             => 'memcached',
	'timeout'              => $wgMemCachedTimeout,
];

$wgObjectCaches['mcrouter'] = [
	'class'                => 'MemcachedPeclBagOStuff',
	'serializer'           => 'php',
	'persistent'           => false,
	'servers'              => [ '127.0.0.1:11213' ],
	'server_failure_limit' => 1e9,
	'retry_timeout'        => -1,
	'loggroup'             => 'memcached',
	'timeout'              => $wgMemCachedTimeout,
];

$wgObjectCaches['mcrouter+memcached'] = [
	'class' => 'MultiWriteBagOStuff',
	'caches' => [
		// new mcrouter consistent hash scheme (uses host:port)
		0 => [
			'factory' => [ 'ObjectCache', 'getInstance' ],
			'args' => [ 'mcrouter' ],
		],
		// old nutcracker consistent hash scheme (uses shard tag);
		// make sure this cache scheme gets purges and stays warm
		1 => [
			'factory' => [ 'ObjectCache', 'getInstance' ],
			'args' => [ 'memcached-pecl' ],
		],
	],
	'reportDupes' => false,
];

$wgObjectCaches['memcached+mcrouter'] = [
	'class'       => 'ReplicatedBagOStuff',
	'readFactory' => [
		'factory' => [ 'ObjectCache', 'getInstance' ],
		'args'    => in_array( $wgDBname, [ 'testwiki', 'test2wiki' ] )
			? [ 'mcrouter' ]
			: [ 'memcached-pecl' ]
	],
	'writeFactory' => [
		'factory' => [ 'ObjectCache', 'getInstance' ],
		'args'    => [ 'mcrouter+memcached' ]
	],
	'reportDupes' => false
];

if ( $wgDBname === 'labswiki' ) {
	$wgMainCacheType = 'memcached-pecl'; // nutcracker only; no mcrouter present
} else {
	$wgMainCacheType = 'memcached+mcrouter'; // nutcracker for reads; write to both
	$wgMainWANCache = 'wancache-main-mcrouter';
	$wgWANObjectCaches['wancache-main-mcrouter'] = [
		'class'   => 'WANObjectCache',
		'cacheId' => $wgMainCacheType,
		'channels' => [ 'purge' => 'wancache-main-default-purge' ],
		// 'mcrouterAware' => true, # wait until *only* mcrouter is used
	];
}

# vim: set sts=4 sw=4 et :
