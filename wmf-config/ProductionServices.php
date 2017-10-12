<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# ######################################################################
# ProductionServices.php should include all the service hostnames/ips
# for any service used in production, divided by datacenter.
#
# It is included by CommonSettings.php
#
# ######################################################################

$wmgAllServices = [];

$wmgAllServices['eqiad'] = [
	'udp2log' => 'mwlog1001.eqiad.wmnet:8420',
	'statsd' => 'statsd.eqiad.wmnet',
	'search' => [ 'search.svc.eqiad.wmnet' ], # elasticsearch must be accessed by hostname for SSL certificate verification to work
	'urldownloader' => 'http://url-downloader.eqiad.wikimedia.org:8080',
	'parsoid' => 'http://parsoid.discovery.wmnet:8000',
	'mathoid' => 'http://mathoid.discovery.wmnet:10042',
	'eventlogging' => 'udp://10.64.32.167:8421', # eventlog1001.eqiad.wmnet
	'eventbus' => 'http://eventbus.discovery.wmnet:8085',
	'upload' => 'upload.svc.eqiad.wmnet',
	'cxserver' => 'http://cxserver.discovery.wmnet:8080',
	'etcd' => '_etcd._tcp.eqiad.wmnet',
	'mediaSwiftAuth' => 'https://ms-fe.svc.eqiad.wmnet/auth',
	'mediaSwiftStore' => 'https://ms-fe.svc.eqiad.wmnet/v1/AUTH_mw',
	'electron' => 'http://pdfrender.discovery.wmnet:5252',
];

$wmgAllServices['codfw'] = [
	'udp2log' => 'mwlog1001.eqiad.wmnet:8420',
	'statsd' => 'statsd.eqiad.wmnet',
	'search' => [ 'search.svc.codfw.wmnet' ], # elasticsearch must be accessed by hostname for SSL certificate verification to work
	'urldownloader' => 'http://url-downloader.codfw.wikimedia.org:8080',
	'parsoid' => 'http://parsoid.discovery.wmnet:8000',
	'mathoid' => 'http://mathoid.discovery.wmnet:10042',
	'eventlogging' => 'udp://10.64.32.167:8421',  # eventlog1001.eqiad.wmnet,
	'eventbus' => 'http://eventbus.discovery.wmnet:8085',
	'upload' => 'upload.svc.codfw.wmnet',
	'cxserver' => 'http://cxserver.discovery.wmnet:8080',
	'etcd' => '_etcd._tcp.codfw.wmnet',
	'mediaSwiftAuth' => 'https://ms-fe.svc.codfw.wmnet/auth',
	'mediaSwiftStore' => 'https://ms-fe.svc.codfw.wmnet/v1/AUTH_mw',
	'electron' => 'http://pdfrender.discovery.wmnet:5252',
];

### Logstash
$wmgAllServices['eqiad']['logstash'] = [
	'10.64.0.122', // logstash1001.eqiad.wmnet
	'10.64.32.137', // logstash1002.eqiad.wmnet
	'10.64.48.113', // logstash1003.eqiad.wmnet
];
$wmgAllServices['codfw']['logstash'] = $wmgAllServices['eqiad']['logstash'];

### Analytics Kafka cluster
$wmgAllServices['eqiad']['kafka'] = [
	'10.64.5.12:9092',   // kafka1012.eqiad.wmnet
	'10.64.5.13:9092',   // kafka1013.eqiad.wmnet
	'10.64.36.114:9092', // kafka1014.eqiad.wmnet
	'10.64.53.10:9092',  // kafka1018.eqiad.wmnet
	'10.64.53.12:9092',  // kafka1020.eqiad.wmnet
	'10.64.36.122:9092', // kafka1022.eqiad.wmnet
];
$wmgAllServices['codfw']['kafka'] = $wmgAllServices['eqiad']['kafka'];

### IRC
$wmgAllServices['eqiad']['irc'] = '208.80.153.44'; // codfw: kraz
$wmgAllServices['codfw']['irc'] = $wmgAllServices['eqiad']['irc'];

### Restbase
$wmgAllServices['eqiad']['restbase'] = 'http://restbase.discovery.wmnet:7231';
$wmgAllServices['codfw']['restbase'] = 'http://restbase.discovery.wmnet:7231';

### Poolcounter
$wmgAllServices['eqiad']['poolcounter'] = [
	'10.64.32.126', # poolcounter1001.eqiad.wmnet
	'10.64.16.152', # poolcounter1002.eqiad.wmnet
];
$wmgAllServices['codfw']['poolcounter'] = [
	'10.192.0.19', # poolcounter2001.codfw.wmnet
	'10.192.16.21', # poolcounter2002.codfw.wmnet
];

### LockManager Redis
$wmgAllServices['eqiad']['redis_lock'] = [
	'rdb1' => '10.64.0.80',
	'rdb2' => '10.64.16.107',
	'rdb3' => '10.64.48.155'
];
$wmgAllServices['codfw']['redis_lock'] = [
	'rdb1' => '10.192.0.83',
	'rdb2' => '10.192.0.84',
	'rdb3' => '10.192.0.85',
];

### Jobqueue redis
// Note: for each host:port combination, the local fallback slave is
// the next server, so rdb1001:6379 => rdb1002:6379 and so on.
// Note: on server failure, partition masters should be switched to the slave
// Note: MediaWiki will fail-over to other shards when one is down. On master
// failure, it is best to either do nothing or just disable the whole shard
// until the master is fixed or full fail-over is done. Proper fail over
// requires changing the slave to stop slaving the old master before updating
// the MediaWiki config to direct traffic there.
// Do NOT remove entries from here if they are still present in 'partitionsBySection'
// in wmf-config/jobqueue.php
$wmgAllServices['eqiad']['jobqueue_redis'] = [
	'rdb1-6379' => 'rdb1001.eqiad.wmnet:6379',
	'rdb1-6380' => 'rdb1001.eqiad.wmnet:6380',
	'rdb1-6381' => 'rdb1001.eqiad.wmnet:6381',
	'rdb2-6379' => 'rdb1003.eqiad.wmnet:6379',
	'rdb2-6380' => 'rdb1003.eqiad.wmnet:6380',
	'rdb2-6381' => 'rdb1003.eqiad.wmnet:6381',
	'rdb3-6379' => 'rdb1007.eqiad.wmnet:6379',
	'rdb3-6380' => 'rdb1007.eqiad.wmnet:6380',
	'rdb3-6381' => 'rdb1007.eqiad.wmnet:6381',
	'rdb4-6379' => 'rdb1005.eqiad.wmnet:6379',
	'rdb4-6380' => 'rdb1005.eqiad.wmnet:6380',
	'rdb4-6381' => 'rdb1005.eqiad.wmnet:6381',
];
$wmgAllServices['codfw']['jobqueue_redis'] = [
	'rdb1-6379' => 'rdb2001.codfw.wmnet:6379',
	'rdb1-6380' => 'rdb2001.codfw.wmnet:6380',
	'rdb1-6381' => 'rdb2001.codfw.wmnet:6381',
	'rdb2-6379' => 'rdb2003.codfw.wmnet:6379',
	'rdb2-6380' => 'rdb2003.codfw.wmnet:6380',
	'rdb2-6381' => 'rdb2003.codfw.wmnet:6381',
	'rdb3-6379' => 'rdb2005.codfw.wmnet:6479',
	'rdb3-6380' => 'rdb2005.codfw.wmnet:6480',
	'rdb3-6381' => 'rdb2005.codfw.wmnet:6481',
	'rdb4-6379' => 'rdb2005.codfw.wmnet:6379',
	'rdb4-6380' => 'rdb2005.codfw.wmnet:6380',
	'rdb4-6381' => 'rdb2005.codfw.wmnet:6381',
];
$wmgAllServices['eqiad']['jobqueue_aggregator'] = [
	'rdb1001.eqiad.wmnet:6378', // preferred
	'rdb1003.eqiad.wmnet:6378', // fallback
	'rdb1005.eqiad.wmnet:6378', // fallback
	'rdb1007.eqiad.wmnet:6378', // fallback
];
$wmgAllServices['codfw']['jobqueue_aggregator'] = [
	'rdb2001.codfw.wmnet:6378', // preferred
	'rdb2003.codfw.wmnet:6378', // fallback
	'rdb2005.codfw.wmnet:6378', // fallback
];
