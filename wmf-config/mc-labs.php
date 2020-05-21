<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

if ( $wmfRealm == 'labs' ) {  # safe guard

$wgMainCacheType = 'mcrouter';
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
	'allow_tcp_nagle_delay' => false
];

// Beta Cluster: Make WANObjectCache mcrouter-aware
$wgMainWANCache = 'wancache-main-mcrouter';
$wgWANObjectCaches['wancache-main-mcrouter'] = [
	'class'   => 'WANObjectCache',
	'cacheId' => $wgMainCacheType,
	// Specify the route prefix that mcrouter listens for and broadcasts.
	// The route prefix is configured in Puppet (profile::mediawiki::mcrouter_wancache).
	'cluster' => 'mw-wan',
	'mcrouterAware' => true,
	// Reduce connection use by co-locating related keys
	'coalesceKeys' => true
];

// Beta Cluster: Experimentally turn on CacheReaper.
// This ensures page-related cache purges are performed,
// even if they got lost somehow, by scanning the recent changes
// table from a job.
$wgEnableWANCacheReaper = true;

} # end safe guard
