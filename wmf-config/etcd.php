<?php
function wmfSetupEtcd() {
	global $wmfDatacenter, $wmfRealm, $wmfLocalServices, $wgReadOnly, $wmfMasterDatacenter;

	# Create a local cache
	if ( PHP_SAPI === 'cli' ) {
		$localCache = new HashBagOStuff;
	} else {
		$localCache = new APCBagOStuff;
	}

	# Set up local EtcdConfig with fallback to common EtcdConfig, this allows
	# us to set $wgReadOnly differently in each datacenter
	$etcdConfig = new MultiConfig( [
		new EtcdConfig( [
			'host' => $wmfLocalServices['etcd'],
			'protocol' => 'https',
			'directory' => "conftool/v1/mediawiki-config/$wmfDatacenter",
			'cache' => $localCache,
		] ),
		new EtcdConfig( [
			'host' => $wmfLocalServices['etcd'],
			'protocol' => 'https',
			'directory' => "conftool/v1/mediawiki-config/common",
			'cache' => $localCache,
		] ),
	] );

	# Read only mode
	$wgReadOnly = $etcdConfig->get( 'ReadOnly' );

	# Master datacenter
	# The datacenter from which we serve traffic.
	$wmfMasterDatacenter = $etcdConfig->get( 'WMFMasterDatacenter' );
}

wmfSetupEtcd();
