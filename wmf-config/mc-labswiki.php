<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.

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

$wgCacheDirectory = "$IP/cache";
$wgMainCacheType    = CACHE_MEMCACHED;
$wgMessageCacheType = CACHE_MEMCACHED;
$wgSessionsInMemcached = true;
