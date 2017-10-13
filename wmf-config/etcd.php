<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
#
# Included very early by CommonSettings.php
# Only two sets of globals available here:
# - $wmgRealm, $wmgDatacenter (from multiversion/MWRealm)
# - $wmgLocalServices (from wmf-config/*Services.php)

function wmfSetupEtcd() {
	global $wmgRealm, $wmgDatacenter, $wmgLocalServices, $wgReadOnly, $wmgMasterDatacenter;

	# Create a local cache
	if ( PHP_SAPI === 'cli' ) {
		$localCache = new HashBagOStuff;
	} else {
		$localCache = new APCBagOStuff;
	}

	# Use a single EtcdConfig object for both local and common paths
	$etcdConfig = new EtcdConfig( [
		'host' => $wmgLocalServices['etcd'],
		'protocol' => 'https',
		'directory' => "conftool/v1/mediawiki-config",
		'cache' => $localCache,
	] );

	# Read only mode
	$wgReadOnly = $etcdConfig->get( "$wmgDatacenter/ReadOnly" );

	# Master datacenter
	# The datacenter from which we serve traffic.
	$wmgMasterDatacenter = $etcdConfig->get( 'common/WMFMasterDatacenter' );
}

wmfSetupEtcd();
