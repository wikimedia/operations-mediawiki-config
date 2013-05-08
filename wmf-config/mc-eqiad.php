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
		'10.64.0.180',
		'10.64.0.181',
		'10.64.0.182',
		'10.64.0.183',
		'10.64.0.184',
		'10.64.0.185',
		'10.64.0.186',
		'10.64.0.187',
		'10.64.0.188',
		'10.64.0.189',
		'10.64.0.190',
		'10.64.0.191',
		'10.64.0.192',
		'10.64.0.193',
		'10.64.0.194',
		'10.64.0.195',
	)
);

# vim: set sts=4 sw=4 et :
