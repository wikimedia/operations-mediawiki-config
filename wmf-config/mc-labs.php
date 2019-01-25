<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

if ( $wmfRealm == 'labs' ) {  # safe guard

$wgMainCacheType = 'mcrouter';
$wgMemCachedPersistent = false;
// Beta Cluster: Increase timeout to 500ms (in microseconds)
$wgMemCachedTimeout = 0.5 * 1e6;

$wgObjectCaches['mcrouter'] = [
	'class'                => 'MemcachedPeclBagOStuff',
	'serializer'           => 'php',
	'persistent'           => false,
	'servers'              => [ '127.0.0.1:11213' ],
	'retry_timeout'        => -1,
	'loggroup'             => 'memcached',
	'timeout'              => $wgMemCachedTimeout,
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
