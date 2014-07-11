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

require_once( "$IP/extensions/Elastica/Elastica.php" );
require_once( "$IP/extensions/CirrusSearch/CirrusSearch.php" );
if ( $wmgUseCirrus ) {
	$wgSearchType = 'CirrusSearch';
	$wgSearchTypeAlternatives = array( 'LuceneSearch' );
	$wgEnableLucenePrefixSearch = false;
	$wgCirrusSearchShowNowUsing = true;
} else {
	$wgSearchTypeAlternatives = array( 'CirrusSearch' );
	$wgCirrusSearchEnablePref = true;
}

# Two replicas let use lose two any two machines before we lose any portion of
# the index.
$wgCirrusSearchReplicaCount = array( 'content' => 2, 'general' => 2 );

# The secondary update job has a delay of a few seconds to make sure that Elasticsearch
# has completed a refresh cycle between when the data that the job needs is added and
# when the job is run.
$wgJobTypeConf['cirrusSearchLinksUpdateSecondary'] = array( 'checkDelay' => true ) +
	$wgJobTypeConf['default'];

# Turn off the more accurate but slower search mode.  It is most helpful when you
# have many small shards.  We don't do that in production and we could use the speed.
$wgCirrusSearchMoreAccurateScoringMode = false;

# Raise the refresh interval to save some CPU at the cost of being slightly less realtime.
$wgCirrusSearchRefreshInterval = 30;

# Ban the hebrew plugin, it is unstable
$wgCirrusSearchBannedPlugins[] = 'elasticsearch-analysis-hebrew';

# Enable the "experimental" highlighter on all wikis
$wgCirrusSearchUseExperimentalHighlighter = true;
$wgCirrusSearchOptimizeIndexForExperimentalHighlighter = true;

# Settings customized per index.
$wgCirrusSearchShardCount = $wmgCirrusSearchShardCount;
$wgCirrusSearchUseAggressiveSplitting = $wmgCirrusSearchUseAggressiveSplitting;
$wgCirrusSearchPreferRecentDefaultDecayPortion = $wmgCirrusSearchPreferRecentDefaultDecayPortion;
$wgCirrusSearchBoostLinks = $wmgCirrusSearchBoostLinks;
$wgCirrusSearchWeights = array_merge( $wgCirrusSearchWeights, $wmgCirrusSearchWeights );

// Enable cache warming for wikis with more than one shard.  Cache warming is good
// for smoothing out I/O spikes caused by merges at the cost of potentially polluting
// the cache by adding things that won't be used.

// Wikis with more then one shard is a decent way of saying "wikis we expect will get
// some search traffic every few seconds".  In this commonet the term "cache" refers
// to all kinds of caches: the linux disk cache, Elasticsearch's filter cache, whatever.
$wgCirrusSearchMainPageCacheWarmer = ( $wgCirrusSearchShardCount['content'] > 1 );

// Commons is special
if ( $wgDBname == 'commonswiki' ) {
	$wgCirrusSearchNamespaceMappings[ NS_FILE ] = 'file';
	$wgCirrusSearchReplicaCount['file'] = 2;
// So is everyone else, for using commons
} else {
	$wgCirrusSearchExtraIndexes[ NS_FILE ] = array( 'commonswiki_file' );
}

# Load per realm specific configuration, either:
# - CirrusSearch-labs.php
# - CirrusSearch-production.php
#
require( getRealmSpecificFilename( "$wmfConfigDir/CirrusSearch.php" ) );
