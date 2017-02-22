<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
$wgMainCacheType = 'memcached-pecl';
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

if ( $wgDBname === 'testwiki' || $wgDBname === 'mediawikiwiki' ) {
	# Confirm page related key purges via scanning recent changes
	$wgEnableWANCacheReaper = true;
}

# vim: set sts=4 sw=4 et :
