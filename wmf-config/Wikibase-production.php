<?php

if ( $wmgUseWikibaseRepo ) {
	if ( $wgDBname === 'testwikidatawiki' ) {
		$wgWBQualityConstraintsSparqlEndpoint = 'http://wdqs-internal.discovery.wmnet/sparql';
		$wgWBQualityConstraintsPropertyConstraintId = 'P51064';
		$wgWBQualityConstraintsFormatConstraintId = 'Q100086';
		$wgWBQualityConstraintsFormatAsARegularExpressionId = 'P51065';
	} elseif ( $wgDBname === 'wikidatawiki' ) {
		$wgWBRepoSettings['sparqlEndpoint'] = 'https://query.wikidata.org/sparql';

		$wgWBQualityConstraintsSparqlEndpoint = 'http://wdqs-internal.discovery.wmnet/sparql';
		$wgWBQualityConstraintsSparqlMaxMillis = 5000; // limit SPARQL queries to just 5 seconds for now
		$wgWBQualityConstraintsTypeCheckMaxEntities = 10; // only check few entities in PHP => fall back to SPARQL very quickly
		$wgWBQualityConstraintsCacheCheckConstraintsResults = true;
		$wgWBQualityConstraintsPropertiesWithViolatingQualifiers = [ 'P1855', 'P2271', 'P5192', 'P5193' ]; // T183267
		$wgSpecialPages['ItemDisambiguation'] = 'SpecialBlankpage';
		$wgWBRepoSettings['dispatchLagToMaxLagFactor'] = 60;

		$wgWBRepoSettings['unitStorage'] = [
			'class' => '\\Wikibase\\Lib\\Units\\JsonUnitStorage',
			'args' => [ __DIR__ . '/unitConversionConfig.json' ]
		];
	}

	// T207019
	$wgWBQualityConstraintsSuggestionsBetaFeature = true;

	// T189776, T189777
	$wgWBRepoSettings['useTermsTableSearchFields'] = false;
}

if ( $wmgUseWikibaseClient ) {
	if ( in_array( $wgDBname, [ 'test2wiki', 'testwiki', 'testwikidatawiki' ] ) ) {
		$wgWBClientSettings['changesDatabase'] = 'testwikidatawiki';
		$wgWBClientSettings['repoDatabase'] = 'testwikidatawiki';
		$wgWBClientSettings['repoUrl'] = "https://test.wikidata.org";
		$wgWBClientSettings['repoConceptBaseUri'] = 'http://test.wikidata.org/entity/';

		$wgArticlePlaceholderImageProperty = 'P47';
	} else {
		$wgWBClientSettings['repoUrl'] = 'https://www.wikidata.org';
		$wgWBClientSettings['repoConceptBaseUri'] = 'http://www.wikidata.org/entity/';
		$wgArticlePlaceholderImageProperty = 'P18';
		$wgWBClientSettings['wikiPageUpdaterDbBatchSize'] = 20;
	}

	$wgArticlePlaceholderSearchEngineIndexed = $wmgArticlePlaceholderSearchEngineIndexed;
	$wgWBClientSettings['propertyOrderUrl'] = 'https://www.wikidata.org/w/index.php?title=MediaWiki:Wikibase-SortedProperties&action=raw&sp_ver=1';

	// T142103
	$wgWBClientSettings['sendEchoNotification'] = true;
	$wgWBClientSettings['echoIcon'] = [ 'url' => '/static/images/wikibase/echoIcon.svg' ];

	$wgWBClientSettings['disabledUsageAspects'] = $wmgWikibaseDisabledUsageAspects;
	$wgWBClientSettings['fineGrainedLuaTracking'] = $wmgWikibaseFineGrainedLuaTracking;

	$wgWBClientSettings['useTermsTableSearchFields'] = false;

}
