<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

if ( $wmgRealm == 'labs' ) {  # safe guard

$wgMainCacheType = 'nutcracker+mcrouter';
$wgMemCachedPersistent = false;
// Beta Cluster: Increase timeout to 500ms (in microseconds)
$wgMemCachedTimeout = 0.5 * 1e6;

$wgObjectCaches['nutcracker-memcached'] = [
	'class'                => 'MemcachedPeclBagOStuff',
	'serializer'           => 'php',
	'persistent'           => false,
	'servers'              => [ '127.0.0.1:11212' ],
	'retry_timeout'        => -1,
	'loggroup'             => 'memcached',
	'timeout'              => $wgMemCachedTimeout,
];

$wgObjectCaches['mcrouter'] = [
	'class'                => 'MemcachedPeclBagOStuff',
	'serializer'           => 'php',
	'persistent'           => false,
	'servers'              => [ '127.0.0.1:11213' ],
	'retry_timeout'        => -1,
	'loggroup'             => 'memcached',
	'timeout'              => $wgMemCachedTimeout,
];

$wgObjectCaches['mcrouter+nutcracker'] = [
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
			'args' => [ 'nutcracker-memcached' ],
		],
	],
	'reportDupes' => false,
];

$wgObjectCaches['nutcracker+mcrouter'] = [
	'class'       => 'ReplicatedBagOStuff',
	'readFactory' => [
		'factory' => [ 'ObjectCache', 'getInstance' ],
		'args'    => [ 'nutcracker-memcached' ]
	],
	'writeFactory' => [
		'factory' => [ 'ObjectCache', 'getInstance' ],
		'args'    => [ 'mcrouter+nutcracker' ]
	],
	'reportDupes' => false
];

// Beta Cluster: Make WANObjectCache mcrouter-aware
$wgMainWANCache = 'wancache-main-mcrouter';
$wgWANObjectCaches['wancache-main-mcrouter'] = [
	'class'   => 'WANObjectCache',
	'cacheId' => $wgMainCacheType,
	'channels' => [ 'purge' => 'wancache-main-default-purge' ],
	// 'mcrouterAware' => true, # wait until *only* mcrouter is used
];

// Beta Cluster: Experimentally turn on CacheReaper.
// This ensures page-related cache purges are performed,
// even if they got lost somehow, by scanning the recent changes
// table from a job.
$wgEnableWANCacheReaper = true;

} # end safe guard
