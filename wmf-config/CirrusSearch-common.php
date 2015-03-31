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
$wgSearchType = 'CirrusSearch';

if ( $wmgUseClusterJobqueue ) {
	# The secondary update job has a delay of a few seconds to make sure that Elasticsearch
	# has completed a refresh cycle between when the data that the job needs is added and
	# when the job is run.
	$wgJobTypeConf['cirrusSearchIncomingLinkCount'] = array( 'checkDelay' => true ) +
		$wgJobTypeConf['default'];
}

# Turn off the more accurate but slower search mode.  It is most helpful when you
# have many small shards.  We don't do that in production and we could use the speed.
$wgCirrusSearchMoreAccurateScoringMode = false;

# Raise the refresh interval to save some CPU at the cost of being slightly less realtime.
$wgCirrusSearchRefreshInterval = 30;

# Lower the regex timeouts - the defaults are too high in an environment with reverse proxies.
$wgCirrusSearchSearchShardTimeout[ 'regex' ] = '40s';
$wgCirrusSearchClientSideSearchTimeout[ 'regex' ] = 80;

# Set the backoff for Cirrus' job that reacts to template changes - slow and steady
# will help prevent spikes in Elasticsearch load.
$wgJobBackoffThrottling['cirrusSearchLinksUpdate'] = 5;
# Also engage a delay for the Cirrus job that counts incoming links to pages when
# pages are newly linked or unlinked.  Too many link count queries at once could flood
# Elasticsearch.
$wgJobBackoffThrottling['cirrusSearchIncomingLinkCount'] = 1;

# Ban the hebrew plugin, it is unstable
$wgCirrusSearchBannedPlugins[] = 'elasticsearch-analysis-hebrew';

# Build and use an ngram index for faster regex matching
$wgCirrusSearchWikimediaExtraPlugin = array(
	'regex' => array(
		'build',
		'use',
	),
);

# Enable the "experimental" highlighter on all wikis
$wgCirrusSearchUseExperimentalHighlighter = true;
$wgCirrusSearchOptimizeIndexForExperimentalHighlighter = true;

# Settings customized per index.
$wgCirrusSearchShardCount = $wmgCirrusSearchShardCount;
$wgCirrusSearchReplicas = $wmgCirrusSearchReplicas;
$wgCirrusSearchMaxShardsPerNode = $wmgCirrusSearchMaxShardsPerNode;
$wgCirrusSearchPreferRecentDefaultDecayPortion = $wmgCirrusSearchPreferRecentDefaultDecayPortion;
$wgCirrusSearchBoostLinks = $wmgCirrusSearchBoostLinks;
$wgCirrusSearchWeights = array_merge( $wgCirrusSearchWeights, $wmgCirrusSearchWeightsOverrides );
$wgCirrusSearchPowerSpecialRandom = $wmgCirrusSearchPowerSpecialRandom;
$wgCirrusSearchAllFields = $wmgCirrusSearchAllFields;
$wgCirrusSearchNamespaceWeights = array_merge( $wgCirrusSearchNamespaceWeights,
	$wmgCirrusSearchNamespaceWeightOverrides );

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
