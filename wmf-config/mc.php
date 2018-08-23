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

if ( $wgDBname === 'labswiki' ) {
	$wgMainCacheType = 'memcached-pecl'; // nutcracker only; no mcrouter present
} else {
	$wgMainCacheType = 'mcrouter';
	$wgMainWANCache = 'wancache-main-mcrouter';
	$wgWANObjectCaches['wancache-main-mcrouter'] = [
		'class'   => 'WANObjectCache',
		'cacheId' => $wgMainCacheType,
		// Specify the route alias that mcrouter listens for and broadcasts.
		// This route is configured in Puppet (profile::mediawiki::mcrouter_wancache).
		'cluster' => 'mw-wan',
		'mcrouterAware' => true
	];
}

# vim: set sts=4 sw=4 et :
