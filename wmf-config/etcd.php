<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# Included very early by CommonSettings.php
# Only two sets of globals available here:
# - $wmfRealm, $wmfDatacenter (from multiversion/MWRealm)
# - $wmfLocalServices (from wmf-config/*Services.php)

function wmfSetupEtcd() {
	global $wmfLocalServices, $wmfEtcdLastModifiedIndex;
		# Create a local cache
	if ( PHP_SAPI === 'cli' ) {
		$localCache = new HashBagOStuff;
	} else {
		$localCache = new APCBagOStuff;
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
	global $wmfDatacenter, $wgReadOnly, $wmfMasterDatacenter;
	$etcdConfig = wmfSetupEtcd();

	# Read only mode
	$wgReadOnly = $etcdConfig->get( "$wmfDatacenter/ReadOnly" );

	# Master datacenter
	# The datacenter from which we serve traffic.
	$wmfMasterDatacenter = $etcdConfig->get( 'common/WMFMasterDatacenter' );
}

wmfEtcdConfig();
