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
	'search' => '10.2.2.30',
	'ocg' => 'ocg.svc.eqiad.wmnet',
	'urldownloader' => 'url-downloader.wikimedia.org',
	'parsoidcache' => '10.2.2.29',
	'mathoid' => 'mathoid.svc.eqiad.wmnet',
	'eventlogging' => '10.64.32.167',
	'eventbus' => 'eventbus.svc.eqiad.wmnet',
);

$wmfAllServices['codfw'] = array(
	'udp2log' => 'fluorine.eqiad.wmnet:8420',
	'statsd' => 'statsd.eqiad.wmnet',
	'search' => '10.2.1.30',
	'ocg' => 'ocg.svc.eqiad.wmnet',
	'urldownloader' => 'url-downloader.wikimedia.org',
	'parsoidcache' => '10.2.2.29', # Change this once parsoidcache is up and running in codfw
	'mathoid' => 'mathoid.svc.eqiad.wmnet',
	'eventlogging' => '10.64.32.167',
	'eventbus' => 'eventbus.svc.eqiad.wmnet',
);

# Shorthand when we have no master-slave situation to keep into account
$wmfLocalServices = $wmfAllServices[$wmfDatacenter];

$wmfMasterServices = $wmfAllServices[$wmfMasterDatacenter];
