<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

if ( $wmfRealm == 'labs' ) {  # safe guard

$wgMainCacheType = 'memcached-pecl';
// Beta Cluster: Enable persistent connections (or do we? still disabled below)
$wgMemCachedPersistent = true;
// Set timeout to 500ms (in microseconds)
$wgMemCachedTimeout = 0.5 * 1e6;

$wgObjectCaches['memcached-pecl'] = [
	'class'                => 'MemcachedPeclBagOStuff',
	'serializer'           => 'php',
	'persistent'           => false,
	// Beta Cluter: Use mcrouter
	'servers'              => [ '127.0.0.1:11213' ],
	// Beta Cluster: Disable server_failure_limit
	# 'server_failure_limit' => 1e9,
	// Beta Cluster: Enable retry timeout (disabled in prod)
	'retry_timeout'        => 1,
	'loggroup'             => 'memcached',
	// Beta Cluster: Disable timeout
	# 'timeout'              => $wgMemCachedTimeout,
];

$wgMainWANCache = 'wancache-main-mcrouter';
$wgWANObjectCaches['wancache-main-mcrouter'] = [
	'class'         => 'WANObjectCache',
	'cacheId'       => 'memcached-pecl',
	'mcrouterAware' => true
];

// Confirm page related key purges via scanning recent changes
$wgEnableWANCacheReaper = true;

} # end safe guard
