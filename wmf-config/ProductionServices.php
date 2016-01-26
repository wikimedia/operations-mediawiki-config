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
	'search' => '10.2.2.30', # search.svc.eqiad.wmnet
	'ocg' => 'http://ocg.svc.eqiad.wmnet:8000',
	'urldownloader' => 'http://url-downloader.wikimedia.org:8080',
	'parsoidcache' => 'http://10.2.2.29',
	'mathoid' => 'http://mathoid.svc.eqiad.wmnet:10042',
	'eventlogging' => 'udp://10.64.32.167:8421', # eventlog1001.eqiad.wmnet
	'eventbus' => 'http://eventbus.svc.eqiad.wmnet:8085',
);

$wmfAllServices['codfw'] = array(
	'udp2log' => 'fluorine.eqiad.wmnet:8420',
	'statsd' => 'statsd.eqiad.wmnet',
	'search' => '10.2.1.30', # search.svc.codfw.wmnet
	'ocg' => 'http://ocg.svc.eqiad.wmnet:8000',
	'urldownloader' => 'http://url-downloader.wikimedia.org:8080',
	'parsoidcache' => 'http://10.2.2.29', # Change this once parsoidcache is up and running in codfw
	'mathoid' => 'http://mathoid.svc.eqiad.wmnet:10042',
	'eventlogging' => 'udp://10.64.32.167:8421',  # eventlog1001.eqiad.wmnet,
	'eventbus' => 'http://eventbus.svc.eqiad.wmnet:8085',
);

# Shorthand when we have no master-slave situation to keep into account
$wmfLocalServices = $wmfAllServices[$wmfDatacenter];

$wmfMasterServices = $wmfAllServices[$wmfMasterDatacenter];
