<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# ######################################################################
# TestServices.php should include all the service hostnames/ips
# for any service referenced in unittests, divided by datacenter.
#
# It is included by CommonSettings.php
#
# ######################################################################

# FIXME variables should be set in WgConfTestCase::loadWgConf??

$wmgDatacenter = $wmgMasterDatacenter = 'unittest';
$wmgAllServices = [];

$wmgAllServices['unittest'] = [
	'udp2log' => 'localhost:8420',
	'statsd' => 'localhost',
	'search' => [ 'localhost' ],
	'ocg' => 'localhost',
	'urldownloader' => 'localhost',
	'parsoidcache' => 'localhost',
	'mathoid' => 'localhost',
	'eventlogging' => 'localhost',
	'eventbus' => 'localhost',
];

### Logstash
$wmgAllServices['unittest']['logstash'] = [ '127.0.0.1' ];

### Analytics Kafka cluster
$wmgAllServices['unittest']['kafka'] = [ '127.0.0.1:9092' ];

### IRC
$wmgAllServices['unittest']['irc'] = '127.0.0.1';

### Restbase
$wmgAllServices['unittest']['restbase'] = "http://127.0.0.1:7231";

### Poolcounter
$wmgAllServices['unittest']['poolcounter'] = [ '127.0.0.1' ];

### LockManager Redis
$wmgAllServices['unittest']['redis_lock'] = [
	'rdb1' => '127.0.0.1',
	'rdb2' => '127.0.0.1',
	'rdb3' => '127.0.0.1'
];

# Make sure direct references to our datacenters work
$wmgLocalServices = $wmgAllServices['eqiad'] = $wmgAllServices['codfw'] = $wmgAllServices['unittest'];
$wmgMasterServices = $wmgLocalServices;
