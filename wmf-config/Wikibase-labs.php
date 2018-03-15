<?php

if ( $wmgUseWikibaseRepo ) {
	$wgPropertySuggesterClassifyingPropertyIds = [ 694 ];
	$wgPropertySuggesterInitialSuggestions = [ 694 ];
	$wgPropertySuggesterDeprecatedIds = [ 107 ];
	$wgWBRepoSettings['formatterUrlProperty'] = 'P9094';

	$wgWBRepoSettings['badgeItems'] = [
		'Q49444' => 'wb-badge-goodarticle',
		'Q49447' => 'wb-badge-featuredarticle',
		'Q49448' => 'wb-badge-recommendedarticle', // T72268
		'Q49449' => 'wb-badge-featuredlist', // T72332
		'Q49450' => 'wb-badge-featuredportal', // T75193
		'Q98649' => 'wb-badge-notproofread', // T97014 - Wikisource badges
		'Q98650' => 'wb-badge-problematic',
		'Q98658' => 'wb-badge-proofread',
		'Q98651' => 'wb-badge-validated'
	];
	$wgWBRepoSettings['preferredGeoDataProperties'] = [
		'P740',
		'P477',
	];
	$wgWBRepoSettings['preferredPageImagesProperties'] = [
		'P448',
		'P715',
		'P723',
		'P733',
		'P964',
	];

	$wgWBRepoSettings['siteLinkGroups'] = [
		'wikipedia',
		'wikibooks',
		'wikinews',
		'wikiquote',
		'wikisource',
		'wikiversity',
		'wikivoyage',
		'wiktionary',
		'special'
	];
	// T112606
	$wgRightsPage = 'Wikidata:Copyright';
	$wgRightsText = 'All structured data from the main and property namespace is available under ' .
		'the Creative Commons CC0 License; text in the other namespaces is available under ' .
		'the Creative Commons Attribution-ShareAlike License; additional terms may apply.';
	$wgRightsUrl = 'creativecommons.org/licenses/by-sa/3.0';

	$wgWBRepoSettings['canonicalUriProperty'] = 'P174944';

	// Cirrus is not ready for this on beta, yet
	$wgWBRepoSettings['entitySearch']['useCirrus'] = false;

	$wgWBRepoSettings['useTermsTableSearchFields'] = false;
}

if ( $wmgUseWikibaseClient ) {
	$wgWBClientSettings['badgeClassNames'] = [
		'Q49444' => 'badge-goodarticle',
		'Q49447' => 'badge-featuredarticle',
		'Q49448' => 'badge-recommendedarticle', // T72268
		'Q49449' => 'badge-featuredlist', // T72332
		'Q49450' => 'badge-featuredportal', // T75193
		'Q98649' => 'badge-notproofread', // T97014 - Wikisource badges
		'Q98650' => 'badge-problematic',
		'Q98658' => 'badge-proofread',
		'Q98651' => 'badge-validated'
	];

	$wgWBClientSettings['repositories'] = [
		'' => [
			'repoDatabase' => 'wikidatawiki',
			'entityNamespaces' => [
				'item' => NS_MAIN,
				'property' => WB_NS_PROPERTY
			],
			'baseUri' => 'https://wikidata.beta.wmflabs.org/entity/',
			'prefixMapping' => [ '' => '' ],
		],
	];

	$wgWBClientSettings['repoUrl'] = 'https://wikidata.beta.wmflabs.org';
	$wgWBClientSettings['sendEchoNotification'] = true;
	$wgWBClientSettings['echoIcon'] = [ 'path' => '/static/images/wikibase/echoIcon.svg' ];

	$wgWikimediaBadgesCommonsCategoryProperty = 'P725';

	$wgArticlePlaceholderImageProperty = 'P964';
}
