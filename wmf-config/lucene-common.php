<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file hold the Lucene configuration which is common to all realm,
# ie settings should apply to both the production cluster and the beta
# cluster.
# If you ever want to stick there an IP address, you should use the per realm
# specific files lucene-labs.php and lucene-production.php

# See: https://wikitech.wikimedia.org/wiki/Search
#
# Contact Wikimedia operations or platform engineering for more details.

$wgDisableTextSearch = false;

// Allow nagios configuration queries without requiring MediaWiki environment
if ( defined( 'MEDIAWIKI' ) ) {
	$wgSearchType = 'LuceneSearch';
	require( $IP . '/extensions/MWSearch/MWSearch.php' );
}

$wgLuceneCacheExpiry = 12 * 3600; // 12 hours
$wgLuceneSearchVersion = 2.1;
$wgLuceneSearchTimeout = 10;

# Load per realm specific configuration, either:
# - lucene-labs.php
# - lucene-production.php
#
require( getRealmSpecificFilename( "$wmgConfigDir/lucene.php" ) );
