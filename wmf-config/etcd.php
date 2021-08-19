<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# etcd.php provides wmfSetupEtcd() which CommonSettings.php usees
# to load certain configuration variables from Etcd.
#
# This for PRODUCTION.
#
# This is loaded very early. Only one set of globals should be
# assumed here:
# - $wmfRealm, $wmfDatacenter (from multiversion/MWRealm)
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
 * @param string|array $etcdHost Hostname or [ 'host' => 'hostname', 'protocol' => 'http|https' ]
 * @return EtcdConfig
 */
function wmfSetupEtcd( $etcdHost ) {
	# Create a local cache
	if ( PHP_SAPI === 'cli' || !function_exists( 'apcu_fetch' ) ) {
		$localCache = new HashBagOStuff;
	} else {
		$localCache = new APCUBagOStuff;
	}

	# Type checking during the transition period.
	if ( is_array( $etcdHost ) ) {
		$host = $etcdHost['host'];
		$protocol = $etcdHost['protocol'];
	} else {
		$host = $etcdHost;
		$protocol = 'https';
	}

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
 * It overwrites a few sections of $wgLBFactoryConf with data from etcd.
 */
function wmfEtcdApplyDBConfig() {
	global $wgLBFactoryConf, $wmfDbconfigFromEtcd, $wmfRealm;
	// In labs, the relevant key exists in etcd, but does not contain real data.
	// Only do this in production.
	if ( $wmfRealm === 'production' ) {
		$wgLBFactoryConf['readOnlyBySection'] = $wmfDbconfigFromEtcd['readOnlyBySection'];
		$wgLBFactoryConf['groupLoadsBySection'] = $wmfDbconfigFromEtcd['groupLoadsBySection'];
		$wgLBFactoryConf['hostsByName'] = $wmfDbconfigFromEtcd['hostsByName'];
		foreach ( $wmfDbconfigFromEtcd['sectionLoads'] as $section => $dbctlLoads ) {
			// For each section, MediaWiki treats the first host as the master.
			// Since JSON dictionaries are unordered, dbctl stores an array of two host:load
			// dictionaries, one containing the master and one containing all the replicas.
			$loadByHost = array_merge( $dbctlLoads[0], $dbctlLoads[1] );
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
		foreach ( $wmfDbconfigFromEtcd['externalLoads'] as $dbctlCluster => $dbctlLoads ) {
			// For each external cluster, MediaWiki treats the first host as the master.
			// Since JSON dictionaries are unordered, dbctl stores an array of two host:load
			// dictionaries, one containing the master and one containing all the replicas.
			$loadByHost = array_merge( $dbctlLoads[0], $dbctlLoads[1] );
			foreach ( $externalStoreAliasesByCluster[$dbctlCluster] as $mwLoadName ) {
				$wgLBFactoryConf['externalLoads'][$mwLoadName] = $loadByHost;
			}
		}
	}
}
