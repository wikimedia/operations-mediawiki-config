<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file holds the MediaWiki CirrusSearch configuration which is specific
# to the 'production' realm.
# It should be loaded AFTER CirrusSearch-common.php

if ( $wgDBname == 'test2wiki' ) {
	$wgCirrusSearchServers = array(
		'10.2.2.30', # search.svc.eqiad.wmnet
	);
	$wgCirrusSearchConnectionAttempts = 3;
} else {
	$wgCirrusSearchServers = array(
		'10.64.32.138', # testsearch1001
		'10.64.32.137', # testsearch1002
		'10.64.32.136', # testsearch1003
	);
}
