<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

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
	'class'                => 'MemcachedPeclBagOStuff',
	'mcrouterAware'        => true, // use routing prefix wildcards
	'serializer'           => 'php',
	'persistent'           => false,
	'servers'              => [ '127.0.0.1:11213' ],
	'server_failure_limit' => 1e9,
	'retry_timeout'        => -1,
	'loggroup'             => 'memcached',
	'timeout'              => $wgMemCachedTimeout
];

$wgMainCacheType = 'memcached-mcrouter'; // mcrouter for reads; write to both

# vim: set sts=4 sw=4 et :
