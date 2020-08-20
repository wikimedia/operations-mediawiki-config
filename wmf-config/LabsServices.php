<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# LabsServices.php statically defines all service hostnames/ips
# for any service used by MediaWiki in the Beta Cluster.
#
# Included from: ../src/ServiceConfig.php
#

return [
	'eqiad' => [
		'udp2log' => 'deployment-fluorine02.eqiad.wmflabs:8420',
		'xenon' => 'deployment-fluorine02.deployment-prep.eqiad.wmflabs',
		'xhgui' => null,
		'xhgui-pdo' => 'mysql:host=deployment-mdb01.deployment-prep.eqiad.wmflabs;dbname=xhgui',
		'statsd' => 'cloudmetrics1001.eqiad.wmnet',
		'search-chi' => [
			// These MUST match the installed SSL certs
			'deployment-elastic05.deployment-prep.eqiad.wmflabs',
			'deployment-elastic06.deployment-prep.eqiad.wmflabs',
			'deployment-elastic07.deployment-prep.eqiad.wmflabs',
		],
		'search-psi' => [
			// Same as search-chi for now to match production services
			// These MUST match the installed SSL certs
			'deployment-elastic06.deployment-prep.eqiad.wmflabs',
		],
		'search-omega' => [
			// Same as search-chi for now to match production services
			// These MUST match the installed SSL certs
			'deployment-elastic07.deployment-prep.eqiad.wmflabs',
		],
		// cloudelastic is not duplicated in labs, it is a write-only cluster and multi-cluster
		// is sufficiently tested with the clusters above.
		'cloudelastic-chi' => null,
		'cloudelastic-psi' => null,
		'cloudelastic-omega' => null,
		'urldownloader' => 'http://deployment-urldownloader02.deployment-prep.eqiad.wmflabs:8080',
		'parsoid' => 'http://deployment-parsoid11.deployment-prep.eqiad.wmflabs/w/rest.php',
		'mathoid' => 'http://deployment-docker-mathoid01.eqiad.wmflabs:10044',
		'eventlogging' => 'udp://deployment-eventlogging03.eqiad.wmflabs:8421',
		'eventgate-analytics' => 'http://deployment-eventgate-3.deployment-prep.eqiad.wmflabs:8192',
		'eventgate-main' => 'http://deployment-eventgate-3.deployment-prep.eqiad.wmflabs:8292',
		'upload' => 'deployment-ms-fe03.deployment-prep.eqiad.wmflabs',
		'cxserver' => 'http://deployment-docker-cxserver01.eqiad.wmflabs:8080',
		'irc' => 'irc.beta.wmflabs.org', // deployment-ircd
		'redis_lock' => [
			'rdb1' => 'deployment-memc04.deployment-prep.eqiad.wmflabs',
			'rdb2' => 'deployment-memc05.deployment-prep.eqiad.wmflabs',
		],
		'etcd' => 'deployment-etcd-01.deployment-prep.eqiad.wmflabs:2379',
		'mediaSwiftAuth' => 'http://deployment-ms-fe03.deployment-prep.eqiad.wmflabs/auth',
		'mediaSwiftStore' => 'http://deployment-ms-fe03.deployment-prep.eqiad.wmflabs/v1/AUTH_mw',
		'electron' => 'http://deployment-pdfrender02.deployment-prep.eqiad.wmflabs:5252',
		'push-notifications' => 'http://deployment-push-notifications01.deployment-prep.eqiad1.wikimedia.cloud:8900',

		### Logstash
		'logstash' => [
			'localhost',
		],

		### Restbase
		'restbase' => 'http://deployment-restbase03.deployment-prep.eqiad.wmflabs:7231',

		### Poolcounter
		'poolcounter' => [
			'deployment-poolcounter06.deployment-prep.eqiad.wmflabs',
		],

		### Kask
		'sessionstore' => 'http://deployment-sessionstore03.deployment-prep.eqiad.wmflabs:8080',
		'echostore' => 'http://deployment-echostore01.deployment-prep.eqiad.wmflabs:8080',
	],
];
