<?php

if ( $wmgUseWikibaseRepo ) {
	$wgWBRepoSettings['canonicalUriProperty'] = 'P174944';

	$wgWBRepoSettings['useTermsTableSearchFields'] = false;

	$wgWBRepoSettings['dispatchLagToMaxLagFactor'] = 60;

	$wgWBRepoSettings['tmpMaxItemIdForNewItemIdHtmlFormatter'] = $wmgWikibaseMaxItemIdForNewItemIdHtmlFormatter;

	$wgWBQualityConstraintsSuggestionsBetaFeature[ 'value' ] = true;
}

if ( $wmgUseWikibaseClient ) {
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
}
