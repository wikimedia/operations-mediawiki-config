<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# etcd.php provides wmfEtcdConfig() which will set certain MediaWiki
# configuration variables based on values from Etcd.
#
# This for PRODUCTION.
#
# This is loaded very early. Only two sets of globals may be
# used here:
# - $wmfRealm, $wmfDatacenter (from multiversion/MWRealm)
# - $wmfLocalServices (from wmf-config/*Services.php)
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/*Services.php
# - wmf-config/etcd.php [THIS FILE]
#
# Included from: wmf-config/CommonSettings.php.
#

function wmfSetupEtcd() {
	global $wmfLocalServices, $wmfEtcdLastModifiedIndex;
		# Create a local cache
	if ( PHP_SAPI === 'cli' ) {
		$localCache = new HashBagOStuff;
	} else {
		if ( function_exists( 'apcu_fetch' ) ) {
			$localCache = new APCUBagOStuff;
		} else {
			$localCache = new APCBagOStuff;
		}
	}

	# Use a single EtcdConfig object for both local and common paths
	$etcdConfig = new EtcdConfig( [
		'host' => $wmfLocalServices['etcd'],
		'protocol' => 'https',
		'directory' => "conftool/v1/mediawiki-config",
		'cache' => $localCache,
	] );
	$wmfEtcdLastModifiedIndex = $etcdConfig->getModifiedIndex();
	return $etcdConfig;
}

function wmfEtcdConfig() {
	global $wmfDatacenter, $wgReadOnly, $wmfMasterDatacenter, $wmfDbconfigFromEtcd;
	$etcdConfig = wmfSetupEtcd();

	# Read only mode
	$wgReadOnly = $etcdConfig->get( "$wmfDatacenter/ReadOnly" );

	# Master datacenter
	# The datacenter from which we serve traffic.
	$wmfMasterDatacenter = $etcdConfig->get( 'common/WMFMasterDatacenter' );

	# Database load balancer config (sectionLoads, groupLoadsBySection, etc) from etcd.
	# See https://wikitech.wikimedia.org/wiki/Dbctl
	$wmfDbconfigFromEtcd = $etcdConfig->get( "$wmfDatacenter/dbconfig" );
}

// Phased rollout of dbctl: database loadbalancer config from etcd.
// Eventually this will be used on all appservers, but to start, just a small set.
// See https://wikitech.wikimedia.org/wiki/Dbctl and https://phabricator.wikimedia.org/T229070
//
// This function must be called after db-{eqiad,codfw}.php has been loaded!
// It overwrites a few sections of $wgLBFactoryConf with data from etcd.
function wmfEtcdApplyDBConfig() {
	global $wgLBFactoryConf, $wmfDbconfigFromEtcd;

	// This is treated as a set; the presence of a key, rather than its value,
	// is what is relevant.
	$dbctl_enabled_hosts = [
		'mwdebug1001' => true,
		'mwdebug1002' => true,
		'mwdebug2001' => true,
		'mwdebug1002' => true,
		'mw1261' => true,
		'mw1276' => true,
	];
	if ( isset( $dbctl_enabled_hosts[ wfHostname() ] ) ) {
		$wgLBFactoryConf['readOnlyBySection'] = $wmfDbconfigFromEtcd['readOnlyBySection'];
		$wgLBFactoryConf['groupLoadsBySection'] = $wmfDbconfigFromEtcd['groupLoadsBySection'];

		// Because JSON dictionaries are unordered, but the order of sectionLoads is
		// rather significant to Mediawiki, dbctl stores the master in a dictionary by itself,
		// and then the remaining replicas in a second dict.
		foreach ( $wmfDbconfigFromEtcd['sectionLoads'] as $section => $sectionLoads ) {
			$wgLBFactoryConf['sectionLoads'][$section] = array_merge( $sectionLoads[0], $sectionLoads[1] );
		}
	}
}
