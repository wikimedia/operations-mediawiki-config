<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

#######################################################################
# TestServices.php should include all the service hostnames/ips
# for any service referenced in unittests, divided by datacenter.
#
# It is included by CommonSettings.php
#
#######################################################################

$wgWMFDatacenter = $wgWMFMasterDatacenter = 'unittest';

$wgWMFAllServices = array();

$wgWMFAllServices['unittest'] = array(
	'udp2log' => 'localhost:8420',
	'statsd' => 'localhost',
	'search' => array( 'localhost' ),
	'ocg' => 'localhost',
	'urldownloader' => 'localhost',
	'parsoidcache' => 'localhost',
	'mathoid' => 'localhost',
	'eventlogging' => 'localhost',
	'eventbus' => 'localhost',
);

### Logstash
$wgWMFAllServices['unittest']['logstash'] = array( '127.0.0.1' );

### Analytics Kafka cluster
$wgWMFAllServices['unittest']['kafka'] = array( '127.0.0.1:9092' );

### IRC
$wgWMFAllServices['unittest']['irc'] = '127.0.0.1';

### Restbase
$wgWMFAllServices['unittest']['restbase'] = "http://127.0.0.1:7231";

### Poolcounter
$wgWMFAllServices['unittest']['poolcounter'] = array( '127.0.0.1' );

### LockManager Redis
$wgWMFAllServices['unittest']['redis_lock'] = array(
	'rdb1' => '127.0.0.1',
	'rdb2' => '127.0.0.1',
	'rdb3' => '127.0.0.1'
);

# Make sure direct references to our datacenters work
$wgWMFLocalServices = $wgWMFAllServices['eqiad'] = $wgWMFAllServices['codfw'] = $wmfAllServices['unittest'];
$wgWMFMasterServices = $wgWMFLocalServices;
