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

# Two replicas let use lose two any two machines before we lose any portion of
# the index.
$wgCirrusSearchReplicaCount = array( 'content' => 2, 'general' => 2 );

# Settings customized per index.
$wgCirrusSearchShardCount = $wmgCirrusSearchShardCount;
$wgCirrusSearchUseAggressiveSplitting = $wmgCirrusSearchUseAggressiveSplitting;
$wgCirrusSearchPreferRecentDefaultDecayPortion = $wmgCirrusSearchPreferRecentDefaultDecayPortion;

// Commons is special
if ( $wgDBname == 'commonswiki' ) {
	$wgCirrusSearchNamespaceMappings[ NS_FILE ] = 'file';

	// Temporarily lower redundancy for commonswiki's file namespace to save some space.
	// Should be $wgCirrusSearchReplicaCount['file'] = 2;
	$wgCirrusSearchReplicaCount['file'] = 1;
// So is everyone else, for using commons
}
// Temporarily disabled until we've deployed the fix for
// https://github.com/elasticsearch/elasticsearch/issues/4645
//else {
//	$wgCirrusSearchExtraIndexes[ NS_FILE ] = array( 'commonswiki_file' );
//}


// Temporarily lower redundancy for enwiki to save some space.
if ( $wgDBname == 'enwiki' ) {
	$wgCirrusSearchReplicaCount = array( 'content' => 1, 'general' => 1 );
}

# Load per realm specific configuration, either:
# - CirrusSearch-labs.php
# - CirrusSearch-production.php
#
require( getRealmSpecificFilename( "$wmfConfigDir/CirrusSearch.php" ) );
