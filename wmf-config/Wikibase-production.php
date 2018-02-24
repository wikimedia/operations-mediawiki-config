<?php

if ( $wmgUseWikibaseRepo ) {
	if ( $wgDBname === 'testwikidatawiki' ) {
		$wgPropertySuggesterClassifyingPropertyIds = [ 7 ];
		$wgPropertySuggesterInitialSuggestions = [ 7 ];
		$wgPropertySuggesterDeprecatedIds = [ 107 ];

		$wgWBRepoSettings['formatterUrlProperty'] = 'P368';

		$wgWBRepoSettings['badgeItems'] = [
			'Q608' => 'wb-badge-goodarticle',
			'Q609' => 'wb-badge-featuredarticle'
		];
		$wgWBRepoSettings['preferredGeoDataProperties'] = [
			'P125',
			'P10',
		];
		$wgWBRepoSettings['preferredPageImagesProperties'] = [
			'P6',
			'P47',
			'P50',
			'P98',
			'P106',
			'P118',
			'P152',
			'P153',
			'P185',
		];
		$wgWBRepoSettings['readFullEntityIdColumn'] = true;

		$wgWBRepoSettings['statementSections']['property'] = [
			'statements' => null,
			'constraints' => [
				'type' => 'propertySet',
				'propertyIds' => [ 'P400' ],
			],
		];

		$wgWBQualityConstraintsSparqlEndpoint = 'https://query.wikidata.org/sparql';
		$wgWBQualityConstraintsPropertyConstraintId = 'P51064';
		$wgWBQualityConstraintsFormatConstraintId = 'Q100086';
		$wgWBQualityConstraintsFormatAsARegularExpressionId = 'P51065';
	} else {
		$wgPropertySuggesterClassifyingPropertyIds = [ 31, 279 ]; // T169060
		$wgPropertySuggesterInitialSuggestions = [ 31, 279 ];

		// T72346
		$wgPropertySuggesterDeprecatedIds = [
			143, // imported from
			/**
			 * Deprecated properties
			 * @see https://www.wikidata.org/wiki/Special:WhatLinksHere/Q18644427
			 */
			1222, // (deprecated) NARA person ID
			2315, // comment (DEPRECATED)
			/**
			 * @see https://www.wikidata.org/w/index.php?oldid=335040857
			 */
			646, // Freebase ID
			/**
			 * Sandbox properties
			 * @see https://www.wikidata.org/wiki/Special:WhatLinksHere/Q18720640
			 */
			368,  // commonsMedia
			369,  // wikibase-item
			370,  // string
			578,  // time
			626,  // globe-coordinate
			855,  // url
			1106, // quantity
			1450, // monolingualtext
			2368, // wikibase-property
			2535, // math
			2536, // external-id
			4045, // tabular-data
			4047, // geo-shape
		];

		$wgWBRepoSettings['sparqlEndpoint'] = 'https://query.wikidata.org/sparql';

		$wgWBRepoSettings['formatterUrlProperty'] = 'P1630';

		$wgWBRepoSettings['badgeItems'] = [
			'Q17437798' => 'wb-badge-goodarticle',
			'Q17437796' => 'wb-badge-featuredarticle',
			'Q17559452' => 'wb-badge-recommendedarticle', // T72268
			'Q17506997' => 'wb-badge-featuredlist', // T72332
			'Q17580674' => 'wb-badge-featuredportal', // T75193
			'Q20748091' => 'wb-badge-notproofread', // T97014 - Wikisource badges
			'Q20748094' => 'wb-badge-problematic',
			'Q20748092' => 'wb-badge-proofread',
			'Q20748093' => 'wb-badge-validated',
			'Q28064618' => 'wb-badge-digitaldocument', // T153186
		];

		$wgWBRepoSettings['preferredGeoDataProperties'] = [
			'P625',
		];
		$wgWBRepoSettings['preferredPageImagesProperties'] = [
			// Photos
			'P18',
			// Complex graphics
			'P41',
			'P94',
			'P154',
			'P1766',
			// Simple graphics
			'P14',
			'P158',
			'P1543',
			'P109',
			'P367',
			// Multi page content
			'P996',
			// Maps
			'P1621',
			'P15',
			'P1846',
			'P181',
			'P242',
			'P1944',
			'P1943',
			// Diagrams
			'P207',
			'P117',
			'P692',
			'P491',
		];
		$wgWBRepoSettings['readFullEntityIdColumn'] = true;

		$wgWBRepoSettings['statementSections']['property'] = [
			'statements' => null,
			'constraints' => [
				'type' => 'propertySet',
				'propertyIds' => [ 'P2302' ],
			],
		];

		$wgWBQualityConstraintsSparqlEndpoint = $wgWBRepoSettings['sparqlEndpoint'];
		$wgWBQualityConstraintsSparqlMaxMillis = 5000; // limit SPARQL queries to just 5 seconds for now
		$wgWBQualityConstraintsTypeCheckMaxEntities = 10; // only check few entities in PHP => fall back to SPARQL very quickly
		$wgWBQualityConstraintsPropertiesWithViolatingQualifiers = [ 'P1855', 'P2271' ]; // T183267
		// T148411: Use profile that uses statement boosting by default to boost/unboost specific types
		$wgWBRepoSettings['entitySearch']['defaultPrefixRescoreProfile'] = 'wikibase_prefix_boost';
		$wgWBRepoSettings['entitySearch']['statementBoost'] = [
			// Q4167410=Wikimedia disambiguation page
			'P31=Q4167410' => '-10',
			// T183510:
			// Q13442814=scientific article
			'P31=Q13442814' => '-5',
			// Q18918145=academic journal article
			'P31=Q18918145' => '-5',
		];
	}

	if ( $wgDBname === 'wikidatawiki' ) {
		$wgWBRepoSettings['unitStorage'] = [
			'class' => '\\Wikibase\\Lib\\Units\\JsonUnitStorage',
			'args' => [ __DIR__ . '/unitConversionConfig.json' ]
		];
	}

	$wgWBRepoSettings['writeFullEntityIdColumn'] = true;

	// T112606
	$wgRightsPage = 'Wikidata:Copyright';
	$wgRightsText = 'All structured data from the main and property namespace is available under ' .
		'the Creative Commons CC0 License; text in the other namespaces is available under ' .
		'the Creative Commons Attribution-ShareAlike License; additional terms may apply.';
	$wgRightsUrl = 'creativecommons.org/licenses/by-sa/3.0';
	// T183053 - make Cirrus instantly index new items
	$wgCirrusSearchInstantIndexNew = [ NS_MAIN, WB_NS_PROPERTY ];
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

	$wgWBClientSettings['badgeClassNames'] = [
		'Q17437796' => 'badge-featuredarticle',
		'Q17437798' => 'badge-goodarticle',
		'Q17559452' => 'badge-recommendedarticle', // T72268
		'Q17506997' => 'badge-featuredlist', // T72332
		'Q17580674' => 'badge-featuredportal', // T75193
		'Q20748091' => 'badge-notproofread', // T97014 - Wikisource badges
		'Q20748094' => 'badge-problematic',
		'Q20748092' => 'badge-proofread',
		'Q20748093' => 'badge-validated',
		'Q28064618' => 'badge-digitaldocument', // T153186
	];

	// Overwrite or add commons links in the "other projects sidebar" with the "commons category" (P373), per T126960
	$wgWikimediaBadgesCommonsCategoryProperty = $wgDBname === 'commonswiki' ? null : 'P373';

	$wgArticlePlaceholderSearchEngineIndexed = $wmgArticlePlaceholderSearchEngineIndexed;
	$wgWBClientSettings['propertyOrderUrl'] = 'https://www.wikidata.org/w/index.php?title=MediaWiki:Wikibase-SortedProperties&action=raw&sp_ver=1';
	$wgWBClientSettings['hasFullEntityIdColumn'] = false;

	$wgWBClientSettings['readFullEntityIdColumn'] = true;

	// T142103
	$wgWBClientSettings['sendEchoNotification'] = true;
	$wgWBClientSettings['echoIcon'] = [ 'url' => '/static/images/wikibase/echoIcon.svg' ];

	$wgWBClientSettings['disabledUsageAspects'] = $wmgWikibaseDisabledUsageAspects;
	$wgWBClientSettings['fineGrainedLuaTracking'] = $wmgWikibaseFineGrainedLuaTracking;

	// T171027
	if ( in_array( $wgDBname, [ 'commonswiki' ] ) ) {
		$wgWBClientSettings['injectRecentChanges'] = false;
	}

}
