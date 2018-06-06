<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

if ( $wmfRealm == 'labs' ) {  # safe guard

$wgMainCacheType = "memcached-pecl";
$wgMemCachedPersistent = false;

$wgMemCachedTimeout = 500000; # micro seconds

$wgObjectCaches['memcached-pecl'] = [
	'class'      => 'MemcachedPeclBagOStuff',
	'serializer' => 'php',
	'persistent' => false,
	'servers'    => [ '127.0.0.1:11213' ], // mcrouter
	'retry_timeout' => 1,
	'loggroup' => 'memcached',
];

$wgMainWANCache = 'wancache-main-mcrouter';
$wgWANObjectCaches['wancache-main-mcrouter'] = [
	'class'         => 'WANObjectCache',
	'cacheId'       => 'memcached-pecl',
	'mcrouterAware' => true
];

# Confirm page related key purges via scanning recent changes
$wgEnableWANCacheReaper = true;

} # end safe guard
