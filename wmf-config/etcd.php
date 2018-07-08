<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# etcd.php provides wmfEtcdConfig() which will set certain MediaWiki
# configuration variables based on values from Etcd.
#
# This for PRODUCTION.
#
# This is loaded very early. Only two sets of globals may be
# used here:
# - $wmgRealm, $wmfDatacenter (from multiversion/MWRealm)
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
