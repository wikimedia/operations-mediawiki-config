<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

#######################################################################
# ProductionServices.php should include all the service hostnames/ips
# for any service used in production, divided by datacenter.
#
# It is included by CommonSettings.php
#
#######################################################################

global $wmfDatacenter, $wmfMasterDatacenter;

$wmfAllServices = array();

$wmfAllServices['unittest'] = array(
	'udp2log' => 'localhost:8420',
	'statsd' => 'localhost',
	'search' => 'localhost',
	'ocg' => 'localhost',
	'urldownloader' => 'localhost',
	'parsoidcache' => 'localhost',
	'mathoid' => 'localhost',
	'eventlogging' => 'localhost',
	'eventbus' => 'localhost',
);

### Logstash
$wmfAllServices['unittest']['logstash'] = array( '127.0.0.1' );

### Analytics Kafka cluster
$wmfAllServices['unittest']['kafka'] = array( '127.0.0.1:9092' );

### IRC
$wmfAllServices['unittest']['irc'] = '127.0.0.1';

### Restbase
$wmfAllServices['unittest']['restbase'] = "http://127.0.0.1:7231";

### Poolcounter
$wmfAllServices['unittest']['poolcounter'] = array( '127.0.0.1' );

### LockManager Redis
$wmfAllServices['eqiad']['redis_lock'] = array(
	'rdb1' => '127.0.0.1',
	'rdb2' => '127.0.0.1',
	'rdb3' => '127.0.0.1'
);

# Shorthand when we have no master-slave situation to keep into account
$wmfLocalServices = $wmfAllServices[$wmfDatacenter];

$wmfMasterServices = $wmfAllServices[$wmfMasterDatacenter];
