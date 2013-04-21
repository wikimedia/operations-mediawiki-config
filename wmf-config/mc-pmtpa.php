<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
$wgMainCacheType = 'memcached-pecl';

$wgMemCachedPersistent = false;
$wgMemCachedTimeout = 250000; # default is 100000

# Newer "mc*" servers (only use the pecl client with these).
# This does not use the "slot" system like the old setup, but
# rather a consistent hash based on key and server addresses,
# so the ordering of servers is not important. Additionally, the
# number of servers can grow/shrink without *too* much disruption.
$wgObjectCaches['memcached-pecl'] = array(
	'class'      => 'MemcachedPeclBagOStuff',
	'serializer' => 'igbinary',
	'servers'    => array(
		'10.0.12.1',
		'10.0.12.2',
		'10.0.12.3',
		'10.0.12.4',
		'10.0.12.5',
		'10.0.12.6',
		'10.0.12.7',
		'10.0.12.8',
		'10.0.12.9',
		'10.0.12.10',
		'10.0.12.11',
		'10.0.12.12',
		'10.0.12.13',
		'10.0.12.14',
		'10.0.12.15',
		'10.0.12.16',
	)
);

# vim: set sts=4 sw=4 et :
