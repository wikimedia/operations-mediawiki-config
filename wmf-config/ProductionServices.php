<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

#######################################################################
# ProductionServices.php should include all the service hostnames/ips
# for any service used in production, divided by datacenter.
#
# It is included by CommonSettings.php
#
#######################################################################

$wmfAllServices = array();

$wmfAllServices['eqiad'] = array(
	'udp2log' => 'fluorine.eqiad.wmnet:8420',
	'statsd' => 'statsd.eqiad.wmnet',
	'search' => array( '10.2.2.30' ), # search.svc.eqiad.wmnet
	'ocg' => 'http://ocg.svc.eqiad.wmnet:8000',
	'urldownloader' => 'http://url-downloader.wikimedia.org:8080',
	'parsoid' => 'http://parsoid.svc.eqiad.wmnet:8000',
	'mathoid' => 'http://mathoid.svc.eqiad.wmnet:10042',
	'eventlogging' => 'udp://10.64.32.167:8421', # eventlog1001.eqiad.wmnet
	'eventbus' => 'http://eventbus.svc.eqiad.wmnet:8085',
	'cxserver' => 'http://cxserver.svc.eqiad.wmnet:8080',
);

$wmfAllServices['codfw'] = array(
	'udp2log' => 'fluorine.eqiad.wmnet:8420',
	'statsd' => 'statsd.eqiad.wmnet',
	'search' => array( '10.2.1.30' ), # search.svc.codfw.wmnet
	'ocg' => 'http://ocg.svc.eqiad.wmnet:8000',
	'urldownloader' => 'http://url-downloader.wikimedia.org:8080',
	'parsoid' => 'http://parsoid.svc.eqiad.wmnet:8000', # Change this once parsoid is up and running in codfw
	'mathoid' => 'http://mathoid.svc.eqiad.wmnet:10042',
	'eventlogging' => 'udp://10.64.32.167:8421',  # eventlog1001.eqiad.wmnet,
	'eventbus' => 'http://eventbus.svc.eqiad.wmnet:8085',
	'cxserver' => 'http://cxserver.svc.codfw.wmnet:8080',
);

### Logstash
$wmfAllServices['eqiad']['logstash'] = array(
	'10.64.0.122', // logstash1001.eqiad.wmnet
	'10.64.32.137', // logstash1002.eqiad.wmnet
	'10.64.48.113', // logstash1003.eqiad.wmnet
);
$wmfAllServices['codfw']['logstash'] = $wmfAllServices['eqiad']['logstash'];

### Analytics Kafka cluster
$wmfAllServices['eqiad']['kafka'] = array(
	'10.64.5.12:9092',   // kafka1012.eqiad.wmnet
	'10.64.5.13:9092',   // kafka1013.eqiad.wmnet
	'10.64.36.114:9092', // kafka1014.eqiad.wmnet
	'10.64.53.10:9092',  // kafka1018.eqiad.wmnet
	'10.64.53.12:9092',  // kafka1020.eqiad.wmnet
	'10.64.36.122:9092', // kafka1022.eqiad.wmnet
);
$wmfAllServices['codfw']['kafka'] = $wmfAllServices['eqiad']['kafka'];

### IRC
$wmfAllServices['eqiad']['irc'] = '208.80.154.160'; // eqiad: argon
$wmfAllServices['codfw']['irc'] = $wmfAllServices['eqiad']['irc'];

### Restbase
$wmfAllServices['eqiad']['restbase'] = 'http://10.2.2.17:7231';
$wmfAllServices['codfw']['restbase'] = 'http://10.2.2.17:7231';

### Poolcounter
$wmfAllServices['eqiad']['poolcounter'] = array(
	'10.64.0.179', # helium.eqiad.wmnet
	'10.64.16.152', # potassium.eqiad.wmnet
);
$wmfAllServices['codfw']['poolcounter'] = array(
	'10.192.16.124', # subra.codfw.wmnet
	'10.192.0.121', # suhail.codfw.wmnet
);

### LockManager Redis
$wmfAllServices['eqiad']['redis_lock'] = array(
	'rdb1' => '10.64.0.180',
	'rdb2' => '10.64.0.181',
	'rdb3' => '10.64.0.182'
);
$wmfAllServices['codfw']['redis_lock'] = array(
	'rdb1' => '10.192.0.34',
	'rdb2' => '10.192.16.37',
	'rdb3' => '10.192.32.20',
);


# Shorthand when we have no master-slave situation to keep into account
$wmfLocalServices = $wmfAllServices[$wmfDatacenter];

$wmfMasterServices = $wmfAllServices[$wmfMasterDatacenter];
