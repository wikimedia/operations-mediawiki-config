<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file holds the MediaWiki CirrusSearch configuration which is specific
# to the 'production' realm.
# It should be loaded AFTER CirrusSearch-common.php

$wgCirrusSearchServers = array(
	'10.2.2.30', # search.svc.eqiad.wmnet
);

$wgCirrusSearchConnectionAttempts = 3;

$wgCirrusSearchBackup['backups'] = array(
	'type' => 'swift',
	'swift_url' => $wmfSwiftEqiadConfig['authUrl'],
	'swift_container' => 'global-data-elastic-backups',
	'swift_username' => $wmfSwiftEqiadConfig['cirrus-backup-user'],
	'swift_password' => $wmfSwiftEqiadConfig['cirrus-backup-key'],
	'max_snapshot_bytes_per_sec' => '10mb',
);

$projectsOkForInterwiki = array(
	'itwiki' => 'w',
	'itwiktionary' => 'wikt',
	'itwikibooks' => 'b',
	'itwikinews' => 'n',
	'itwikiquote' => 'q',
	'itwikisource' => 's',
	'itwikivoyage' => 'voy',
	'itwikiversity' => 'v',
);

if ( isset( $projectsOkForInterwiki[ $wgDBname ] ) ) {
	unset( $projectsOkForInterwiki[$wgDBname] );
	$interwikiSearchConf = array_flip( $projectsOkForInterwiki );
	$wgCirrusSearchInterwikiSources = $interwikiSearchConf;
	$wgCirrusSearchInterwikiCacheTime = 60;
}
