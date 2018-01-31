<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# ######################################################################
# LabsServices.php should include all the service hostnames/ips
# for any service used in labs
#
# It is included by CommonSettings.php
#
# ######################################################################

$wmfAllServices = [];

$wmfAllServices['eqiad'] = [
	'udp2log' => 'deployment-fluorine02.eqiad.wmflabs:8420',
	'statsd' => 'labmon1001.eqiad.wmnet',
	'search' => [
		// These MUST match the installed SSL certs
		'deployment-elastic05.deployment-prep.eqiad.wmflabs',
		'deployment-elastic06.deployment-prep.eqiad.wmflabs',
		'deployment-elastic07.deployment-prep.eqiad.wmflabs',
	],
	'urldownloader' => 'http://deployment-urldownloader.deployment-prep.eqiad.wmflabs:8080',
	'parsoid' => 'http://deployment-parsoid09.deployment-prep.eqiad.wmflabs:8000',
	'mathoid' => 'http://deployment-mathoid.eqiad.wmflabs:10042',
	'eventlogging' => 'udp://deployment-eventlogging03.eqiad.wmflabs:8421',
	'eventbus' => 'http://deployment-eventlogging04.deployment-prep.eqiad.wmflabs:8085',
	'upload' => false,
	'cxserver' => 'http://deployment-sca01.eqiad.wmflabs:8080',
	'irc' => 'irc.beta.wmflabs.org', // deployment-ircd
	'redis_lock' => [
		'rdb1' => 'deployment-redis01.deployment-prep.eqiad.wmflabs',
		'rdb2' => 'deployment-redis02.deployment-prep.eqiad.wmflabs',
	],
	'etcd' => 'deployment-etcd-01.deployment-prep.eqiad.wmflabs:2379',
	'mediaSwiftAuth' => 'http://deployment-ms-fe02.deployment-prep.eqiad.wmflabs/auth',
	'mediaSwiftStore' => 'http://deployment-ms-fe02.deployment-prep.eqiad.wmflabs/v1/AUTH_mw',
	'electron' => 'http://deployment-pdfrender02.deployment-prep.eqiad.wmflabs:5252',
];

### Logstash
$wmfAllServices['eqiad']['logstash'] = [
	'deployment-logstash2.deployment-prep.eqiad.wmflabs',
];

### Analytics Kafka cluster (analytics-deployment-prep)
$wmfAllServices['eqiad']['kafka'] = [
	'deployment-kafka01.deployment-prep.eqiad.wmflabs:9092',
	'deployment-kafka03.deployment-prep.eqiad.wmflabs:9092',
];

### Restbase
$wmfAllServices['eqiad']['restbase'] = 'http://deployment-restbase02.deployment-prep.eqiad.wmflabs:7231';

### Poolcounter
$wmfAllServices['eqiad']['poolcounter'] = [
	'deployment-poolcounter04.deployment-prep.eqiad.wmflabs',
];
