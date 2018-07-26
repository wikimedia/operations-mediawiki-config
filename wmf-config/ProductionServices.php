<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# ProductionServices.php statically defines all service hostnames/ips
# for any service used by MediaWiki, divided by datacenter.
#
# This for PRODUCTION.
#
# Included from: wmf-config/CommonSettings.php.
#

$wmfAllServices = [];

// Logging is not active-active.
$wmfAllServices['eqiad']['udp2log'] =
$wmfAllServices['codfw']['udp2log'] = 'mwlog1001.eqiad.wmnet:8420';

// Statsd is not active-active.
$wmfAllServices['eqiad']['statsd'] =
$wmfAllServices['codfw']['statsd'] = 'statsd.eqiad.wmnet';

// elasticsearch must be accessed by hostname for SSL certificate verification.
$wmfAllServices['eqiad']['search'] = [
	'search.svc.eqiad.wmnet',
];
$wmfAllServices['codfw']['search'] = [
	'search.svc.codfw.wmnet',
];

$wmfAllServices['eqiad']['urldownloader'] = 'http://url-downloader.eqiad.wikimedia.org:8080';
$wmfAllServices['codfw']['urldownloader'] = 'http://url-downloader.codfw.wikimedia.org:8080';

$wmfAllServices['eqiad']['parsoid'] =
$wmfAllServices['codfw']['parsoid'] = 'http://parsoid.discovery.wmnet:8000';

$wmfAllServices['eqiad']['mathoid'] =
$wmfAllServices['codfw']['mathoid'] = 'http://mathoid.discovery.wmnet:10042';

// EventLogging is not active-active.
$wmfAllServices['eqiad']['eventlogging'] =
$wmfAllServices['codfw']['eventlogging'] = 'udp://10.64.32.167:8421'; # eventlog1001.eqiad.wmnet

$wmfAllServices['eqiad']['eventbus'] =
$wmfAllServices['codfw']['eventbus'] = 'http://eventbus.discovery.wmnet:8085';

$wmfAllServices['eqiad']['cxserver'] =
$wmfAllServices['codfw']['cxserver'] = 'http://cxserver.discovery.wmnet:8080';

$wmfAllServices['eqiad']['upload'] = 'ms-fe.svc.eqiad.wmnet';
$wmfAllServices['codfw']['upload'] = 'ms-fe.svc.codfw.wmnet';

$wmfAllServices['eqiad']['mediaSwiftAuth'] = 'https://ms-fe.svc.eqiad.wmnet/auth';
$wmfAllServices['codfw']['mediaSwiftAuth'] = 'https://ms-fe.svc.codfw.wmnet/auth';

$wmfAllServices['eqiad']['mediaSwiftStore'] = 'https://ms-fe.svc.eqiad.wmnet/v1/AUTH_mw';
$wmfAllServices['codfw']['mediaSwiftStore'] = 'https://ms-fe.svc.codfw.wmnet/v1/AUTH_mw';

$wmfAllServices['eqiad']['etcd'] = '_etcd._tcp.eqiad.wmnet';
$wmfAllServices['codfw']['etcd'] = '_etcd._tcp.codfw.wmnet';

$wmfAllServices['eqiad']['electron'] =
$wmfAllServices['codfw']['electron'] = 'http://pdfrender.discovery.wmnet:5252';

// Logstash is not active-active.
$wmfAllServices['eqiad']['logstash'] =
$wmfAllServices['codfw']['logstash'] = [
	'10.2.2.36', # logstash.svc.eqiad.wmnet
];

// Analytics Kafka (not active-active).
$wmfAllServices['eqiad']['kafka'] =
$wmfAllServices['codfw']['kafka'] = [
	'10.64.5.12:9092', # kafka1012.eqiad.wmnet
	'10.64.5.13:9092', # kafka1013.eqiad.wmnet
	'10.64.36.114:9092', # kafka1014.eqiad.wmnet
	'10.64.53.10:9092', # kafka1018.eqiad.wmnet
	'10.64.53.12:9092', # kafka1020.eqiad.wmnet
	'10.64.36.122:9092', # kafka1022.eqiad.wmnet
];

// IRC (broadcast RCFeed for irc.wikimedia.org)
// Not active-active.
$wmfAllServices['eqiad']['irc'] =
$wmfAllServices['codfw']['irc'] = '208.80.153.44'; # kraz.codfw.wmnet

// Restbase
$wmfAllServices['eqiad']['restbase'] =
$wmfAllServices['codfw']['restbase'] = 'http://restbase.discovery.wmnet:7231';

// Poolcounter
$wmfAllServices['eqiad']['poolcounter'] = [
	'10.64.32.126', # poolcounter1001.eqiad.wmnet
	'10.64.0.19', # poolcounter1003.eqiad.wmnet
];
$wmfAllServices['codfw']['poolcounter'] = [
	'10.192.0.19', # poolcounter2001.codfw.wmnet
	'10.192.16.21', # poolcounter2002.codfw.wmnet
];

// LockManager Redis
$wmfAllServices['eqiad']['redis_lock'] = [
	'rdb1' => '10.64.0.80',
	'rdb2' => '10.64.16.107',
	'rdb3' => '10.64.48.155'
];
$wmfAllServices['codfw']['redis_lock'] = [
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
$wmfAllServices['eqiad']['jobqueue_redis'] = [
	'rdb4-6379' => 'rdb1005.eqiad.wmnet:6379',
	'rdb4-6380' => 'rdb1005.eqiad.wmnet:6380',
	'rdb4-6381' => 'rdb1005.eqiad.wmnet:6381',
];
$wmfAllServices['codfw']['jobqueue_redis'] = [
	'rdb4-6379' => 'rdb2005.codfw.wmnet:6379',
	'rdb4-6380' => 'rdb2005.codfw.wmnet:6380',
	'rdb4-6381' => 'rdb2005.codfw.wmnet:6381',
];
$wmfAllServices['eqiad']['jobqueue_aggregator'] = [
	'rdb1005.eqiad.wmnet:6378', // preferred, no fallback anymore
];
$wmfAllServices['codfw']['jobqueue_aggregator'] = [
	'rdb2005.codfw.wmnet:6378', // preferred
];
