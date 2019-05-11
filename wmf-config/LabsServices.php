<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# LabsServices.php statically defines all service hostnames/ips
# for any service used by MediaWiki in the Beta Cluster.
#
# Included from: wmf-config/CommonSettings.php.
#

return [
	'eqiad' => [
		'udp2log' => 'deployment-fluorine02.eqiad.wmflabs:8420',
		'xenon' => 'deployment-fluorine02.deployment-prep.eqiad.wmflabs',
		'xhgui' => false, // Not yet available in Beta Cluster, T180761
		'statsd' => 'labmon1001.eqiad.wmnet',
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
		'parsoid' => 'http://deployment-parsoid09.deployment-prep.eqiad.wmflabs:8000',
		'mathoid' => 'http://deployment-docker-mathoid01.eqiad.wmflabs:10044',
		'eventlogging' => 'udp://deployment-eventlogging03.eqiad.wmflabs:8421',
		'eventbus' => 'http://deployment-kafka-main-1.deployment-prep.eqiad.wmflabs:8085',
		'eventgate-analytics' => 'http://deployment-eventgate-analytics-1.deployment-prep.eqiad.wmflabs:8192',
		'upload' => 'deployment-ms-fe03.deployment-prep.eqiad.wmflabs',
		'cxserver' => 'http://deployment-sca01.eqiad.wmflabs:8080',
		'irc' => 'irc.beta.wmflabs.org', // deployment-ircd
		'redis_lock' => [
			'rdb1' => 'deployment-memc04.deployment-prep.eqiad.wmflabs',
			'rdb2' => 'deployment-memc05.deployment-prep.eqiad.wmflabs',
		],
		'etcd' => 'deployment-etcd-01.deployment-prep.eqiad.wmflabs:2379',
		'mediaSwiftAuth' => 'http://deployment-ms-fe03.deployment-prep.eqiad.wmflabs/auth',
		'mediaSwiftStore' => 'http://deployment-ms-fe03.deployment-prep.eqiad.wmflabs/v1/AUTH_mw',
		'electron' => 'http://deployment-pdfrender02.deployment-prep.eqiad.wmflabs:5252',

		### Logstash
		'logstash' => [
			'localhost',
		],

		### Jumbo Kafka cluster (jumbo-deployment-prep)
		'kafka' => [
			'deployment-kafka-jumbo-1.deployment-prep.eqiad.wmflabs:9092',
			'deployment-kafka-jumbo-2.deployment-prep.eqiad.wmflabs:9092',
		],

		### Restbase
		'restbase' => 'http://deployment-restbase02.deployment-prep.eqiad.wmflabs:7231',

		### Poolcounter
		'poolcounter' => [
			'deployment-poolcounter05.deployment-prep.eqiad.wmflabs',
		],
	],
];
