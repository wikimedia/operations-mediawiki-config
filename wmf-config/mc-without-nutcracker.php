<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
$wgMainCacheType = 'memcached-pecl';
$wgMemCachedPersistent = false;
$wgMemCachedTimeout = 0.25 * 1e6;  // 250ms

$wgObjectCaches['memcached-pecl'] = array(
	'class'                => 'MemcachedPeclBagOStuff',
	'serializer'           => 'php',
	'persistent'           => false,
	'servers'              => array( '127.0.0.1:11000' ),
	'server_failure_limit' => 1e9,
	'retry_timeout'        => -1,
	'loggroup'             => 'memcached',
);

# vim: set sts=4 sw=4 et :
