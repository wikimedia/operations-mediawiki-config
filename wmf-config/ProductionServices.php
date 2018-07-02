<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# ProductionServices.php statically defines all service hostnames/ips
# for any service used by MediaWiki, divided by datacenter.
#
# This for PRODUCTION.
#
# Included from: wmf-config/CommonSettings.php.
#

$services = [];

// Logging is not active-active.
$services['eqiad']['udp2log'] =
$services['codfw']['udp2log'] = 'mwlog1001.eqiad.wmnet:8420';

// Statsd is not active-active.
$services['eqiad']['statsd'] =
$services['codfw']['statsd'] = 'statsd.eqiad.wmnet';

// elasticsearch must be accessed by hostname for SSL certificate verification.
$services['eqiad']['search'] = [
	'search.svc.eqiad.wmnet',
];
$services['codfw']['search'] = [
	'search.svc.codfw.wmnet',
];

# urldownloader configuration. 1 per DC for latency reasons
# eqiad urldownloader
$services['eqiad']['urldownloader'] = 'http://url-downloader.eqiad.wikimedia.org:8080';
# codfw urldownloader
$services['codfw']['urldownloader'] = 'http://url-downloader.codfw.wikimedia.org:8080';

$services['eqiad']['parsoid'] =
$services['codfw']['parsoid'] = 'http://parsoid.discovery.wmnet:8000';

$services['eqiad']['mathoid'] =
$services['codfw']['mathoid'] = 'http://mathoid.discovery.wmnet:10042';

// EventLogging is not active-active.
$services['eqiad']['eventlogging'] =
$services['codfw']['eventlogging'] = 'udp://10.64.32.167:8421'; # eventlog1001.eqiad.wmnet

$services['eqiad']['eventbus'] =
$services['codfw']['eventbus'] = 'http://eventbus.discovery.wmnet:8085';

$services['eqiad']['cxserver'] =
$services['codfw']['cxserver'] = 'http://cxserver.discovery.wmnet:8080';

$services['eqiad']['upload'] = 'ms-fe.svc.eqiad.wmnet';
$services['codfw']['upload'] = 'ms-fe.svc.codfw.wmnet';

$services['eqiad']['mediaSwiftAuth'] = 'https://ms-fe.svc.eqiad.wmnet/auth';
$services['codfw']['mediaSwiftAuth'] = 'https://ms-fe.svc.codfw.wmnet/auth';

$services['eqiad']['mediaSwiftStore'] = 'https://ms-fe.svc.eqiad.wmnet/v1/AUTH_mw';
$services['codfw']['mediaSwiftStore'] = 'https://ms-fe.svc.codfw.wmnet/v1/AUTH_mw';

$services['eqiad']['etcd'] = '_etcd._tcp.eqiad.wmnet';
$services['codfw']['etcd'] = '_etcd._tcp.codfw.wmnet';

$services['eqiad']['electron'] =
$services['codfw']['electron'] = 'http://pdfrender.discovery.wmnet:5252';

// Logstash is not active-active.
$services['eqiad']['logstash'] =
$services['codfw']['logstash'] = [
	'10.2.2.36', # logstash.svc.eqiad.wmnet
];

// Analytics Kafka (not active-active).
$services['eqiad']['kafka'] =
$services['codfw']['kafka'] = [
	'10.64.5.12:9092', # kafka1012.eqiad.wmnet
	'10.64.5.13:9092', # kafka1013.eqiad.wmnet
	'10.64.36.114:9092', # kafka1014.eqiad.wmnet
	'10.64.53.10:9092', # kafka1018.eqiad.wmnet
	'10.64.53.12:9092', # kafka1020.eqiad.wmnet
	'10.64.36.122:9092', # kafka1022.eqiad.wmnet
];

// IRC (broadcast RCFeed for irc.wikimedia.org)
// Not active-active.
$services['eqiad']['irc'] =
$services['codfw']['irc'] = '208.80.153.44'; # kraz.codfw.wmnet

// Restbase
$services['eqiad']['restbase'] =
$services['codfw']['restbase'] = 'http://restbase.discovery.wmnet:7231';

// Poolcounter
$services['eqiad']['poolcounter'] = [
	'10.64.32.126', # poolcounter1001.eqiad.wmnet
	'10.64.0.19', # poolcounter1003.eqiad.wmnet
];
$services['codfw']['poolcounter'] = [
	'10.192.0.19', # poolcounter2001.codfw.wmnet
	'10.192.16.21', # poolcounter2002.codfw.wmnet
];

// LockManager Redis
$services['eqiad']['redis_lock'] = [
	'rdb1' => '10.64.0.80',
	'rdb2' => '10.64.16.107',
	'rdb3' => '10.64.48.155'
];
$services['codfw']['redis_lock'] = [
	'rdb1' => '10.192.0.83',
	'rdb2' => '10.192.0.84',
	'rdb3' => '10.192.0.85',
];

return $services;
