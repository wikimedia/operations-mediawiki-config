<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
$wgMainCacheType = 'memcached-pecl';

$wgMemCachedPersistent = false;
$wgUseMemCached = true;
$wgMemCachedTimeout = 250000; # default is 100000
$wgMemCachedInstanceSize = 2000;

# Newer "mc*" servers (only use the pecl client with these).
# This does not use the "slot" system like the old setup, but
# rather a consistent hash based on key and server addresses,
# so the ordering of servers is not important. Additionally, the
# number of servers can grow/shrink without *too* much disruption.
$wgObjectCaches['memcached-pecl'] = array(
	'class'      => 'MemcachedPeclBagOStuff',
	'serializer' => 'igbinary',
	'servers'    => array(
		'10.0.12.1:11211',
		'10.0.12.2:11211',
		'10.0.12.3:11211',
		'10.0.12.4:11211',
		'10.0.12.5:11211',
		'10.0.12.6:11211',
		'10.0.12.7:11211',
		'10.0.12.8:11211',
		'10.0.12.9:11211',
		'10.0.12.10:11211',
		'10.0.12.11:11211',
		'10.0.12.12:11211',
		'10.0.12.13:11211',
		'10.0.12.14:11211',
		'10.0.12.15:11211',
		'10.0.12.16:11211',
	)
);

# vim: set sts=4 sw=4 et :
