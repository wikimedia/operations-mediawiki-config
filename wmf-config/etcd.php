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
		// Debug hosts and canary hosts.
		'mwdebug1001' => true,
		'mwdebug1002' => true,
		'mwdebug2001' => true,
		'mwdebug1002' => true,
		'mw1261' => true,
		'mw1262' => true,
		'mw1263' => true,
		'mw1264' => true,
		'mw1265' => true,
		'mw1276' => true,
		'mw1277' => true,
		'mw1278' => true,
		'mw1279' => true,
		'mw1311' => true,
		// 25 addl non-canary hosts to go to 25% in eqiad
		'mw1221' => true,
		'mw1222' => true,
		'mw1223' => true,
		'mw1224' => true,
		'mw1225' => true,
		'mw1226' => true,
		'mw1227' => true,
		'mw1228' => true,
		'mw1229' => true,
		'mw1230' => true,
		'mw1231' => true,
		'mw1232' => true,
		'mw1233' => true,
		'mw1234' => true,
		'mw1235' => true,
		'mw1238' => true,
		'mw1239' => true,
		'mw1240' => true,
		'mw1241' => true,
		'mw1242' => true,
		'mw1243' => true,
		'mw1244' => true,
		'mw1245' => true,
		'mw1246' => true,
		'mw1247' => true,
		'mw1248' => true,
		'mw1249' => true,
		'mw1250' => true,
		'mw1251' => true,
		'mw1252' => true,
		'mw1253' => true,
		'mw1254' => true,
		'mw1255' => true,
		'mw1256' => true,
		'mw1257' => true,
		// 35 hosts to go to 10% in codfw.
		'mw2135' => true,
		'mw2136' => true,
		'mw2137' => true,
		'mw2138' => true,
		'mw2139' => true,
		'mw2140' => true,
		'mw2141' => true,
		'mw2142' => true,
		'mw2143' => true,
		'mw2144' => true,
		'mw2145' => true,
		'mw2146' => true,
		'mw2147' => true,
		'mw2150' => true,
		'mw2151' => true,
		'mw2152' => true,
		'mw2153' => true,
		'mw2154' => true,
		'mw2155' => true,
		'mw2156' => true,
		'mw2157' => true,
		'mw2158' => true,
		'mw2159' => true,
		'mw2160' => true,
		'mw2161' => true,
		'mw2162' => true,
		'mw2163' => true,
		'mw2164' => true,
		'mw2165' => true,
		'mw2166' => true,
		'mw2167' => true,
		'mw2168' => true,
		'mw2169' => true,
		'mw2170' => true,
		'mw2171' => true,
	];
	// TODO: before enabling on all hosts, conditionalize the below on $wmfRealm!='labs'.
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
