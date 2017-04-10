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
	$wgReadOnly = $wmgEtcdConfig->has( 'ReadOnly' )
		? $wmgEtcdConfig->get( 'ReadOnly' ) : 'Unable to contact etcd';

	# Master datacenter
	# The datacenter from which we serve traffic.
	if ( $wmgEtcdConfig->has( 'WMFMasterDatacenter' ) ) {
		$wmfMasterDatacenter = $wmgEtcdConfig->get( 'WMFMasterDatacenter' );
	}
}

wmfSetupEtcd();
