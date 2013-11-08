<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.
if( $wmgRealm == 'labs' ) {  # safe guard

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
$wgMainCacheType = CACHE_MEMCACHED;
$wgMemCachedTimeout = 500000; # micro seconds
$wgSessionsInMemcached = true;

$wgMemCachedServers = array(
	0 => '10.4.1.86:11211',   # deployment-memc0
	1 => '10.4.1.106:11211',  # deployment-memc1
);


} # end safe guard
