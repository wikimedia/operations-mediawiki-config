<?php

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
}
