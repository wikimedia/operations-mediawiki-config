<?php

if ( $wmgUseWikibaseRepo ) {
	$wgWBRepoSettings['badgeItems'] = array(
		'Q49444' => 'wb-badge-goodarticle',
		'Q49447' => 'wb-badge-featuredarticle',
		'Q49448' => 'wb-badge-recommendedarticle', // T72268
		'Q49449' => 'wb-badge-featuredlist', // T72332
		'Q49450' => 'wb-badge-featuredportal', // T75193
		'Q98649' => 'wb-badge-notproofread', // T97014 - Wikisource badges
		'Q98650' => 'wb-badge-problematic',
		'Q98658' => 'wb-badge-proofread',
		'Q98651' => 'wb-badge-validated'
	);
	$wgWBRepoSettings['pageImagesPropertyIds'] = array(
		'P448',
		'P715',
		'P723',
		'P733',
		'P964',
	);

	$wgWBRepoSettings['subscriptionLookupMode'] = 'subscriptions+sitelinks';
	$wgWBRepoSettings['useLegacyChangesSubscription'] = false;
}

if ( $wmgUseWikibaseClient ) {
	$wgWBClientSettings['badgeClassNames'] = array(
		'Q49444' => 'badge-goodarticle',
		'Q49447' => 'badge-featuredarticle',
		'Q49448' => 'badge-recommendedarticle', // T72268
		'Q49449' => 'badge-featuredlist', // T72332
		'Q49450' => 'badge-featuredportal', // T75193
		'Q98649' => 'badge-notproofread', // T97014 - Wikisource badges
		'Q98650' => 'badge-problematic',
		'Q98658' => 'badge-proofread',
		'Q98651' => 'badge-validated'
	);

	$wgWBClientSettings['useLegacyChangesSubscription'] = false;
	$wgWBClientSettings['repoConceptBaseUri'] = 'http://wikidata.beta.wmflabs.org/entity/';
}
