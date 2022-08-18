<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# etcd.php provides wmfSetupEtcd() which CommonSettings.php usees
# to load certain configuration variables from Etcd.
#
# This for PRODUCTION.
#
# This is loaded very early. Only one set of globals should be
# assumed here:
# - $wmgRealm, $wmgDatacenter (from multiversion/MWRealm)
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/etcd.php [THIS FILE]
#
# Included from: wmf-config/CommonSettings.php.
#

/**
 * Setup etcd!
 *
 * @param array $etcdHost [ 'host' => 'hostname', 'protocol' => 'http|https' ]
 * @return EtcdConfig
 */
function wmfSetupEtcd( $etcdHost ) {
	# Create a local cache
	if ( PHP_SAPI === 'cli' || !function_exists( 'apcu_fetch' ) ) {
		$localCache = new HashBagOStuff;
	} else {
		$localCache = new APCUBagOStuff;
	}

	$host = $etcdHost['host'];
	$protocol = $etcdHost['protocol'];

	# Use a single EtcdConfig object for both local and common paths
	$etcdConfig = new EtcdConfig( [
		'host' => $host,
		'protocol' => $protocol,
		'directory' => "conftool/v1/mediawiki-config",
		'cache' => $localCache,
	] );
	return $etcdConfig;
}

/**
 * @param array $array
 * @return mixed
 */
function wmfArrayKeyFirst( array $array ) {
	if ( function_exists( 'array_key_first' ) ) {
		return array_key_first( $array );
	} else {
		// PHP 7.2
		foreach ( $array as $key => $unused ) {
			return $key;
		}
		return null;
	}
}

/** In production, read the database loadbalancer config from etcd.
 * See https://wikitech.wikimedia.org/wiki/Dbctl
 *
 * This function must be called after db-{eqiad,codfw}.php has been loaded!
 * It overwrites a few sections of $wgLBFactoryConf with data from etcd.
 */
function wmfEtcdApplyDBConfig() {
	global $wgLBFactoryConf, $wmgLocalDbConfig, $wmgRemoteMasterDbConfig;
	$wgLBFactoryConf['readOnlyBySection'] = $wmgLocalDbConfig['readOnlyBySection'];
	$wgLBFactoryConf['groupLoadsBySection'] = $wmgLocalDbConfig['groupLoadsBySection'];
	$wgLBFactoryConf['hostsByName'] = $wmgLocalDbConfig['hostsByName'];
	foreach ( $wmgLocalDbConfig['sectionLoads'] as $section => $dbctlLoads ) {
		// For each section, MediaWiki treats the first host as the master.
		// Since JSON dictionaries are unordered, dbctl stores an array of two host:load
		// dictionaries, one containing the master and one containing all the replicas.
		// We also need to merge in the cross-DC master entries if that is relevant.
		$crossDCLoads = $wmgRemoteMasterDbConfig['sectionLoads'][$section][0] ?? null;
		if ( $crossDCLoads ) {
			$remoteMaster = wmfArrayKeyFirst( $crossDCLoads );
			$loadByHost = array_merge( [ $remoteMaster => 0 ], ...$dbctlLoads );
			$wgLBFactoryConf['hostsByName'][$remoteMaster] =
				$wmgRemoteMasterDbConfig['hostsByName'][$remoteMaster];
		} else {
			$loadByHost = array_merge( ...$dbctlLoads );
		}
		$wgLBFactoryConf['sectionLoads'][$section] = $loadByHost;
	}
	// Since MediaWiki components that use ExternalStore includes cluster names in the rows
	// of blob tracking tables, the periodic consolidation of clusters by DBAs requires the
	// preservation of cluster aliases in order to handle all the old cluster references.
	$externalStoreAliasesByCluster = [
		# es1, previously known as $wmgOldExtTemplate
		'es1' => [ 'rc1', 'cluster3', 'cluster4', 'cluster5', 'cluster6', 'cluster7',
					'cluster8', 'cluster9', 'cluster10', 'cluster20', 'cluster21',
					'cluster1', 'cluster2', 'cluster22', 'cluster23' ],
		'es2' => [ 'cluster24' ],
		'es3' => [ 'cluster25' ],
		'es4' => [ 'cluster26' ],
		'es5' => [ 'cluster27' ],
		'x1' => [ 'extension1' ],
		'x2' => [ 'extension2' ],
	];
	// x2 uses circular replication so there is no need for cross-DC connections
	$circularReplicationClusters = [
		'x2' => true,
	];
	foreach ( $wmgLocalDbConfig['externalLoads'] as $dbctlCluster => $dbctlLoads ) {
		// Merge the same way as sectionLoads
		if ( !empty( $circularReplicationClusters[$dbctlCluster] ) ) {
			$localMaster = wmfArrayKeyFirst( $dbctlLoads[0] );
			// Override the 'ssl' flag set in masterTemplateOverrides via db-production.php
			$wgLBFactoryConf['templateOverridesByServer'][$localMaster]['ssl'] = false;
			$loadByHost = array_merge( ...$dbctlLoads );
		} else {
			$crossDCLoads = $wmgRemoteMasterDbConfig['externalLoads'][$dbctlCluster][0] ?? null;
			if ( $crossDCLoads ) {
				$remoteMaster = wmfArrayKeyFirst( $crossDCLoads );
				$loadByHost = array_merge( [ $remoteMaster => 0 ], ...$dbctlLoads );
				$wgLBFactoryConf['hostsByName'][$remoteMaster] =
					$wmgRemoteMasterDbConfig['hostsByName'][$remoteMaster];
			} else {
				$loadByHost = array_merge( ...$dbctlLoads );
			}
		}

		foreach ( $externalStoreAliasesByCluster[$dbctlCluster] as $mwLoadName ) {
			$wgLBFactoryConf['externalLoads'][$mwLoadName] = $loadByHost;
		}
	}
}
