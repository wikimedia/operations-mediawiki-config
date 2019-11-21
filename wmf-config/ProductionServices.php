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
	'xenon' => 'mwlog1001.eqiad.wmnet',

	// XHGui MongoDB
	'xhgui' => 'mongodb://tungsten.eqiad.wmnet:27017',

	// Statsd is not active-active.
	'statsd' => 'statsd.eqiad.wmnet',

	// EventLogging is not active-active.
	'eventlogging' => 'udp://10.64.32.167:8421', # eventlog1001.eqiad.wmnet

	// Logstash is not active-active.
	'logstash' => [
		'10.2.2.36', # logstash.svc.eqiad.wmnet
	],

	// IRC (broadcast RCFeed for irc.wikimedia.org)
	// Not active-active.
	'irc' => '208.80.153.44', # kraz.codfw.wmnet

	// Automatic dc-local discovery
	'parsoid' => 'http://parsoid.discovery.wmnet:8000',
	'parsoidphp' => 'https://parsoid-php.discovery.wmnet/w/rest.php',
	'mathoid' => 'http://mathoid.discovery.wmnet:10042',
	'eventgate-analytics' => 'http://eventgate-analytics.discovery.wmnet:31192',
	'eventgate-main' => 'http://eventgate-main.discovery.wmnet:32192',
	'cxserver' => 'http://cxserver.discovery.wmnet:8080',
	'electron' => 'http://pdfrender.discovery.wmnet:5252',
	'restbase' => 'http://restbase.discovery.wmnet:7231',
	'sessionstore' => 'https://sessionstore.discovery.wmnet:8081',
	'echostore' => 'https://echostore.discovery.wmnet:8082',

	// cloudelastic only exists in eqiad. No load balancer is available due to
	// the part of the network that they live in so each host is enumerated
	'cloudelastic-chi' => [
		[
			'host' => 'cloudelastic.wikimedia.org',
			'transport' => 'Https',
			'port' => 9243,
		],
	],
	'cloudelastic-psi' => [
		[
			'host' => 'cloudelastic.wikimedia.org',
			'transport' => 'Https',
			'port' => 9443,
		],
	],
	'cloudelastic-omega' => [
		[
			'host' => 'cloudelastic.wikimedia.org',
			'transport' => 'Https',
			'port' => 9643,
		],
	]
];

$services = [
	'eqiad' => $common + [

		// each DC has its own urldownloader for latency reasons
		'urldownloader' => 'http://url-downloader.eqiad.wikimedia.org:8080',

		'upload' => 'ms-fe.svc.eqiad.wmnet',
		'mediaSwiftAuth' => 'https://ms-fe.svc.eqiad.wmnet/auth',
		'mediaSwiftStore' => 'https://ms-fe.svc.eqiad.wmnet/v1/AUTH_mw',

		'etcd' => '_etcd._tcp.eqiad.wmnet',

		'poolcounter' => [
			'10.64.0.151', # poolcounter1004.eqiad.wmnet
			'10.64.32.236', # poolcounter1005.eqiad.wmnet
		],

		// LockManager Redis
		'redis_lock' => [
			'rdb1' => '10.64.0.80',
			'rdb2' => '10.64.16.107',
			'rdb3' => '10.64.48.155',
		],
		'search-chi' => [
			[ // forwarded to https://search.svc.eqiad.wmnet:9243/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 19243,
			]
		],
		'search-psi' => [
			[ // forwarded to https://search.svc.eqiad.wmnet:9643/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 19643,
			]
		],
		'search-omega' => [
			[ // forwarded to https://search.svc.eqiad.wmnet:9443/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 19443,
			]
		],
	],
	'codfw' => $common + [

		'urldownloader' => 'http://url-downloader.codfw.wikimedia.org:8080',

		'upload' => 'ms-fe.svc.codfw.wmnet',
		'mediaSwiftAuth' => 'https://ms-fe.svc.codfw.wmnet/auth',
		'mediaSwiftStore' => 'https://ms-fe.svc.codfw.wmnet/v1/AUTH_mw',

		'etcd' => '_etcd._tcp.codfw.wmnet',

		'poolcounter' => [
			'10.192.0.132', # poolcounter2003.codfw.wmnet
			'10.192.16.129', # poolcounter2004.codfw.wmnet
		],

		'redis_lock' => [
			'rdb1' => '10.192.0.83',
			'rdb2' => '10.192.0.84',
			'rdb3' => '10.192.0.85',
		],
		'search-chi' => [
			[ // forwarded to https://search.svc.codfw.wmnet:9243/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 14243,
			]
		],
		'search-psi' => [
			[ // forwarded to https://search.svc.codfw.wmnet:9643/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 14643,
			]
		],
		'search-omega' => [
			[ // forwarded to https://search.svc.codfw.wmnet:9443/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 14443,
			]
		],
	],
];
unset( $common );
return $services;
