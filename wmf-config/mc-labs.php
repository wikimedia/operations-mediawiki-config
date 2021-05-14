<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

if ( $wmfRealm == 'labs' ) {  # safe guard

$wgMemCachedPersistent = false;
// Beta Cluster: Increase timeout to 500ms (in microseconds)
$wgMemCachedTimeout = 0.5 * 1e6;

$wgObjectCaches['mcrouter'] = [
	'class'                 => 'MemcachedPeclBagOStuff',
	'serializer'            => 'php',
	'persistent'            => false,
	'servers'               => [ '127.0.0.1:11213' ],
	'retry_timeout'         => -1,
	'loggroup'              => 'memcached',
	'timeout'               => $wgMemCachedTimeout,
	'allow_tcp_nagle_delay' => false,
];
$wgObjectCaches['mcrouter-with-onhost-tier'] = array_merge(
	$wgObjectCaches['mcrouter'],
	[ 'routingPrefix' => "/$wmfDatacenter/mw-with-onhost-tier/" ]
);

$wgMainCacheType = 'mcrouter';
$wgMainWANCache = 'wancache-main-mcrouter';
$wgWANObjectCaches['wancache-main-mcrouter'] = [
	'class'   => 'WANObjectCache',
	'cacheId' => $wgMainCacheType,
	// Specify the route prefix that mcrouter listens for and broadcasts.
	// The route prefix is configured in Puppet (profile::mediawiki::mcrouter_wancache).
	'broadcastRoutingPrefix' => '/*/mw-wan/',
];

// Beta Cluster: Experimentally turn on CacheReaper.
// This ensures page-related cache purges are performed,
// even if they got lost somehow, by scanning the recent changes
// table from a job.
$wgEnableWANCacheReaper = true;

} # end safe guard
