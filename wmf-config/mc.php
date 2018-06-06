<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

$wgMainCacheType = 'memcached-pecl';
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
	// Basically disable failure limit (0 is invalid)
	'server_failure_limit' => 1e9,
	// Basically disable retry timeout
	'retry_timeout'        => -1,
	'loggroup'             => 'memcached',
	'timeout'              => $wgMemCachedTimeout
];

# vim: set sts=4 sw=4 et :
