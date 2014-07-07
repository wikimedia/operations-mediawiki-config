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

# Use twemproxy for memcached access - see twemproxy-eqiad.yaml
# NOTE: after deploying a new twemproxy.yaml config, run restart-twemproxy
# from the deploy host to make it live.
$wgObjectCaches['memcached-pecl'] = array(
	'class'      => 'MemcachedPeclBagOStuff',
	'serializer' => 'php',
	'persistent' => false,
	'servers'    => array( '127.0.0.1:11212' ),
	'server_failure_limit' => 1e9,
	'retry_timeout' => -1
);

/*** No Twemproxy
	$wgObjectCaches['memcached-pecl'] = array(
		'class'      => 'MemcachedPeclBagOStuff',
		'serializer' => 'php',
		'servers'    => array(
			'10.64.0.180', # mc1001
			'10.64.0.181', # mc1002
			'10.64.0.182', # mc1003
			'10.64.0.183', # mc1004
			'10.64.0.184', # mc1005
			'10.64.0.185', # mc1006
			'10.64.0.186', # mc1007
			'10.64.0.187', # mc1008
			'10.64.0.188', # mc1009
			'10.64.0.189', # mc1010
			'10.64.0.190', # mc1011
			'10.64.0.191', # mc1012
			'10.64.0.192', # mc1013
			'10.64.0.193', # mc1014
			'10.64.0.194', # mc1015
			'10.64.0.195', # mc1016
		)
	);
***/

# vim: set sts=4 sw=4 et :
