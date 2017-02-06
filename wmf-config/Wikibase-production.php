<?php

if ( $wmgUseWikibaseRepo ) {
	if ( $wgDBname === 'testwikidatawiki' ) {
		$wgPropertySuggesterClassifyingPropertyIds = [ 7 ];
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
	} else {
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
	}

	if ( $wgDBname === 'wikidatawiki' ) {
		$wgWBRepoSettings['unitStorage'] = [
			'class' => '\\Wikibase\\Lib\\JsonUnitStorage',
			'args' => [__DIR__ . '/unitConversionConfig.json']
		];
	}
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
}
