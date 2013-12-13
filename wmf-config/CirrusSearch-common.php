<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file hold the CirrusSearch configuration which is common to all realms,
# ie settings should apply to both the production cluster and the beta
# cluster.
# If you ever want to stick there an IP address, you should use the per realm
# specific files CirrusSearch-labs.php and CirrusSearch-production.php

# See: https://wikitech.wikimedia.org/wiki/Search
#
# Contact Wikimedia operations or platform engineering for more details.

if ( file_exists( "$IP/extensions/Elastica/Elastica.php" ) ){
	require_once( "$IP/extensions/Elastica/Elastica.php" );
}

require_once( "$IP/extensions/CirrusSearch/CirrusSearch.php" );
if ( $wmgUseCirrus ) {
	$wgSearchType = 'CirrusSearch';
	$wgSearchTypeAlternatives = array( 'LuceneSearch' );
	$wgEnableLucenePrefixSearch = false;
	$wgCirrusSearchShowNowUsing = true;
} else {
	$wgSearchTypeAlternatives = array( 'CirrusSearch' );
	if ( !$wmgCirrusIsBuilding ) {
		$wgCirrusSearchEnablePref = true;
	}
}

$wgCirrusSearchShardCount = $wmgCirrusSearchShardCount;
$wgCirrusSearchReplicaCount = $wmgCirrusSearchReplicaCount;
$wgCirrusSearchUseAggressiveSplitting = $wmgCirrusSearchUseAggressiveSplitting;

# Load per realm specific configuration, either:
# - CirrusSearch-labs.php
# - CirrusSearch-production.php
#
require( getRealmSpecificFilename( "$wmfConfigDir/CirrusSearch.php" ) );
