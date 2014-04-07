<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.
if( $wmfRealm == 'labs' ) {  # safe guard

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

$wgMainCacheType = "beta-memcached-{$wmfDatacenter}";

$wgMemCachedTimeout = 500000; # micro seconds

# On labs, use one memcached cluster per datacenter

# pmtpa
$wgObjectCaches['beta-memcached-pmtpa'] = array(
	'class'      => 'MemcachedPeclBagOStuff',
	'serializer' => 'php',
	'persistent' => false,
	'servers'    => array(
		0 => '10.4.1.86:11211',   # deployment-memc0.pmtpa
		1 => '10.4.1.106:11211',  # deployment-memc1.pmtpa
	),
	'retry_timeout' => 1,
);

# eqiad
$wgObjectCaches['beta-memcached-eqiad'] = array(
	'class'      => 'MemcachedPeclBagOStuff',
	'serializer' => 'php',
	'persistent' => false,
	'servers'    => (
		defined( 'HHVM_VERSION' ) ? array(
			0 => '10.68.17.69:11211',  # deployment-memc04.eqiad
			1 => '10.68.17.71:11211',  # deployment-memc05.eqiad
		) : array(
			0 => '10.68.16.14:11211',  # deployment-memc02.eqiad
			1 => '10.68.16.15:11211',  # deployment-memc03.eqiad
		)
	),
	'retry_timeout' => 1,
);

} # end safe guard
