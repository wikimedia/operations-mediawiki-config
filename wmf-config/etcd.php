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

/** In production, read the database loadbalancer config from etcd.
 * See https://wikitech.wikimedia.org/wiki/Dbctl
 *
 * This function must be called after db-{eqiad,codfw}.php has been loaded!
 * It overwrites a few sections of $wgLBFactoryConf (passed by reference) and
 * populates the $wmgPCServers global with data from etcd.
 * @param array $localDbConfig Local config loaded from etcd, to be applied to
 * @param array &$lbFactoryConf LBFactoryConf array to be updated using $localDbConfig
 */
function wmfApplyEtcdDBConfig( $localDbConfig, &$lbFactoryConf ) {
	global $wmgRemoteMasterDbConfig, $wmgPCServers;
	$lbFactoryConf['readOnlyBySection'] = $localDbConfig['readOnlyBySection'];
	$lbFactoryConf['groupLoadsBySection'] = $localDbConfig['groupLoadsBySection'];
	$lbFactoryConf['hostsByName'] = $localDbConfig['hostsByName'];
	foreach ( $localDbConfig['sectionLoads'] as $section => $dbctlLoads ) {
		// For each section, MediaWiki treats the first host as the master.
		// Since JSON dictionaries are unordered, dbctl stores an array of two host:load
		// dictionaries, one containing the master and one containing all the replicas.
		// We also need to merge in the cross-DC master entries if that is relevant.
		$crossDCLoads = $wmgRemoteMasterDbConfig['sectionLoads'][$section][0] ?? null;
		if ( $crossDCLoads ) {
			$remoteMaster = array_key_first( $crossDCLoads );
			$loadByHost = array_merge( [ $remoteMaster => 0 ], ...$dbctlLoads );
			$lbFactoryConf['hostsByName'][$remoteMaster] =
				$wmgRemoteMasterDbConfig['hostsByName'][$remoteMaster];
		} else {
			$loadByHost = array_merge( ...$dbctlLoads );
		}
		$lbFactoryConf['sectionLoads'][$section] = $loadByHost;
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
		'es4' => [ 'cluster26', 'cluster28' ],
		'es5' => [ 'cluster27', 'cluster29' ],
		'es6' => [ 'cluster30' ],
		'es7' => [ 'cluster31' ],
		'x1' => [ 'extension1' ],
		'x2' => [ 'extension2' ],
	];
	// x2 uses circular replication so there is no need for cross-DC connections
	$circularReplicationClusters = [
		'x2' => true,
	];
	$wmgPCServers = [];
	foreach ( $localDbConfig['externalLoads'] as $dbctlCluster => $dbctlLoads ) {
		// While parsercache sections are included in externalLoads, they are not
		// accessed through LBFactoryMulti. Instead, populate to wmgPCServers for
		// consumption by SqlBagOStuff.
		if ( substr( $dbctlCluster, 0, 2 ) === 'pc' ) {
			// Expected parsercache $dbctlLoads structure: [ [ 'pcNNNN' => 0 ], [] ]
			if ( is_array( $dbctlLoads ) && isset( $dbctlLoads[0] ) && is_array( $dbctlLoads[0] ) ) {
				$host = array_key_first( $dbctlLoads[0] );
				if ( is_string( $host ) ) {
					$wmgPCServers[$dbctlCluster] = $localDbConfig['hostsByName'][$host] ?? $host;
				}
			}
			continue;
		}
		// Merge the same way as sectionLoads
		if ( !empty( $circularReplicationClusters[$dbctlCluster] ) ) {
			$localMaster = array_key_first( $dbctlLoads[0] );
			// Override the 'ssl' flag set in masterTemplateOverrides via db-production.php
			$lbFactoryConf['templateOverridesByServer'][$localMaster]['ssl'] = false;
			$loadByHost = array_merge( ...$dbctlLoads );
		} else {
			$crossDCLoads = $wmgRemoteMasterDbConfig['externalLoads'][$dbctlCluster][0] ?? null;
			if ( $crossDCLoads ) {
				$remoteMaster = array_key_first( $crossDCLoads );
				$loadByHost = array_merge( [ $remoteMaster => 0 ], ...$dbctlLoads );
				$lbFactoryConf['hostsByName'][$remoteMaster] =
					$wmgRemoteMasterDbConfig['hostsByName'][$remoteMaster];
			} else {
				$loadByHost = array_merge( ...$dbctlLoads );
			}
		}

		foreach ( $externalStoreAliasesByCluster[$dbctlCluster] as $mwLoadName ) {
			$lbFactoryConf['externalLoads'][$mwLoadName] = $loadByHost;
		}
	}
}
