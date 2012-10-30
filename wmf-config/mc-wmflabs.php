<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.
if( $datacenter == 'wmflabs' ) {  # safe guard

/*
 * Before altering the wgMemCachedServers array below, make sure you planned
 * your change. Memcached compute a hash of the data and given the hash
 * assign the value to one of the servers.
 * If you remove / comment / change order of servers, the hash will miss
 * and that can result in bad performance for the cluster !
 *
 * Hashar, based on dammit comments. Nov 28 2005.
 *
 */
$wgMemCachedPersistent = true;
$wgUseMemCached = true;
$wgMainCacheType = CACHE_MEMCACHED;
$wgMemCachedTimeout = 500000; # micro seconds
$wgSessionsInMemcached = true;

$wgMemCachedInstanceSize = 2000;

$wgMemCachedServers = array(
0 => 'deployment-mc:11000',
);


} # end safe guard
