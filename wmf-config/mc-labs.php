<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.
if ( $wmgRealm == 'labs' ) {  # safe guard
/*
 * Before altering the wgMemCachedServers array below, make sure you planned
 * your change. Memcached compute a hash of the data and given the hash
 * assign the value to one of the servers.
 * If you remove / comment / change order of servers, the hash will miss
 * and that can result in bad performance for the cluster !
 *
 * Hashar, based on dammit comments. Nov 28 2005.
 *
 */$wgMemCachedPersistent = true;

$wgMainCacheType = "memcached-pecl";

$wgMemCachedTimeout = 500000; # micro seconds

$wgObjectCaches['memcached-pecl'] = [
	'class'      => 'MemcachedPeclBagOStuff',
	'serializer' => 'php',
	'persistent' => false,
	'servers'    => [ '127.0.0.1:11212' ],
	'retry_timeout' => 1,
	'loggroup' => 'memcached',
];

# Confirm page related key purges via scanning recent changes
$wgEnableWANCacheReaper = true;

} # end safe guard
