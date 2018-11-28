<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# ProductionServices.php statically defines all service hostnames/ips
# for any service used by MediaWiki, divided by datacenter.
#
# May be included on app servers in contexts where MediaWiki is not (yet)
# initialised (for example, fatal-error.php and profiler.php).
#
# MUST NOT use any predefined state, only primitive values in plain PHP.
#
# This for PRODUCTION.
#
# For MediaWiki, this is included from wmf-config/CommonSettings.php.
#

$common = [
	// Logging is not active-active.
	'udp2log' => 'mwlog1001.eqiad.wmnet:8420',

	// Statsd is not active-active.
	'statsd' => 'statsd.eqiad.wmnet',

	// EventLogging is not active-active.
	'eventlogging' => 'udp://10.64.32.167:8421', # eventlog1001.eqiad.wmnet

	// Logstash is not active-active.
	'logstash' => [
		'10.2.2.36', # logstash.svc.eqiad.wmnet
	],

	// Analytics Kafka (not active-active).
	'kafka' => [
		'10.64.5.12:9092', # kafka1012.eqiad.wmnet
		'10.64.5.13:9092', # kafka1013.eqiad.wmnet
		'10.64.36.114:9092', # kafka1014.eqiad.wmnet
		'10.64.53.10:9092', # kafka1018.eqiad.wmnet
		'10.64.53.12:9092', # kafka1020.eqiad.wmnet
		'10.64.36.122:9092', # kafka1022.eqiad.wmnet
	],

	// IRC (broadcast RCFeed for irc.wikimedia.org)
	// Not active-active.
	'irc' => '208.80.153.44', # kraz.codfw.wmnet

	// Automatic dc-local discovery
	'parsoid' => 'http://parsoid.discovery.wmnet:8000',
	'mathoid' => 'http://mathoid.discovery.wmnet:10042',
	'eventbus' => 'http://eventbus.discovery.wmnet:8085',
	'cxserver' => 'http://cxserver.discovery.wmnet:8080',
	'electron' => 'http://pdfrender.discovery.wmnet:5252',
	'restbase' => 'http://restbase.discovery.wmnet:7231',
];

$services = [
	'eqiad' => $common + [
		// elasticsearch must be accessed by hostname,
		// for SSL certificate verification.
		'search-khi' => [
			[
				'host' => 'search.svc.eqiad.wmnet',
				'transport' => 'Https',
				'port' => 9243,
			]
		],

		'search-psi' => [
			[
				'host' => 'search.svc.eqiad.wmnet',
				'transport' => 'Https',
				'port' => 9643,
			]
		],

		'search-omega' => [
			[
				'host' => 'search.svc.eqiad.wmnet',
				'transport' => 'Https',
				'port' => 9443,
			]
		],

		// each DC has its own urldownloader for latency reasons
		'urldownloader' => 'http://url-downloader.eqiad.wikimedia.org:8080',

		'upload' => 'ms-fe.svc.eqiad.wmnet',
		'mediaSwiftAuth' => 'https://ms-fe.svc.eqiad.wmnet/auth',
		'mediaSwiftStore' => 'https://ms-fe.svc.eqiad.wmnet/v1/AUTH_mw',

		'etcd' => '_etcd._tcp.eqiad.wmnet',

		'poolcounter' => [
			'10.64.32.126', # poolcounter1001.eqiad.wmnet
			'10.64.0.19', # poolcounter1003.eqiad.wmnet
		],

		// LockManager Redis
		'redis_lock' => [
			'rdb1' => '10.64.0.80',
			'rdb2' => '10.64.16.107',
			'rdb3' => '10.64.48.155',
		],

	],
	'codfw' => $common + [
		'search-khi' => [
			[
				'host' => 'search.svc.codfw.wmnet',
				'transport' => 'Https',
				'port' => 9243,
			]
		],

		'search-psi' => [
			[
				'host' => 'search.svc.codfw.wmnet',
				'transport' => 'Https',
				'port' => 9643,
			]
		],
		'search-omega' => [
			[
				'host' => 'search.svc.codfw.wmnet',
				'transport' => 'Https',
				'port' => 9443,
			]
		],

		'urldownloader' => 'http://url-downloader.codfw.wikimedia.org:8080',

		'upload' => 'ms-fe.svc.codfw.wmnet',
		'mediaSwiftAuth' => 'https://ms-fe.svc.codfw.wmnet/auth',
		'mediaSwiftStore' => 'https://ms-fe.svc.codfw.wmnet/v1/AUTH_mw',

		'etcd' => '_etcd._tcp.codfw.wmnet',

		'poolcounter' => [
			'10.192.0.19', # poolcounter2001.codfw.wmnet
			'10.192.16.21', # poolcounter2002.codfw.wmnet
		],

		'redis_lock' => [
			'rdb1' => '10.192.0.83',
			'rdb2' => '10.192.0.84',
			'rdb3' => '10.192.0.85',
		],

	],
];
unset( $common );
return $services;
