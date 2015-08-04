<?php

if ( $wmgUseWikibaseRepo ) {
	if ( $wgDBname === 'testwikidatawiki' ) {
		$wgWBRepoSettings['badgeItems'] = array(
			'Q608' => 'wb-badge-goodarticle',
			'Q609' => 'wb-badge-featuredarticle'
		);
	} else {
		$wgWBRepoSettings['badgeItems'] = array(
			'Q17437798' => 'wb-badge-goodarticle',
			'Q17437796' => 'wb-badge-featuredarticle',
			'Q17559452' => 'wb-badge-recommendedarticle', // T72268
			'Q17506997' => 'wb-badge-featuredlist', // T72332
			'Q17580674' => 'wb-badge-featuredportal', // T75193
			'Q20748091' => 'wb-badge-notproofread', // T97014 - Wikisource badges
			'Q20748094' => 'wb-badge-problematic',
			'Q20748092' => 'wb-badge-proofread',
			'Q20748093' => 'wb-badge-validated'
		);
	}
}

if ( $wmgUseWikibaseClient ) {

	if ( in_array( $wgDBname, array( 'test2wiki', 'testwiki', 'testwikidatawiki' ) ) ) {
		$wgWBClientSettings['changesDatabase'] = 'testwikidatawiki';
		$wgWBClientSettings['repoDatabase'] = 'testwikidatawiki';
		$wgWBClientSettings['repoUrl'] = "//test.wikidata.org";
	}

	$wgWBClientSettings['badgeClassNames'] = array(
		'Q17437796' => 'badge-featuredarticle',
		'Q17437798' => 'badge-goodarticle',
		'Q17559452' => 'badge-recommendedarticle', // T72268
		'Q17506997' => 'badge-featuredlist', // T72332
		'Q17580674' => 'badge-featuredportal', // T75193
		'Q20748091' => 'badge-notproofread', // T97014 - Wikisource badges
		'Q20748094' => 'badge-problematic',
		'Q20748092' => 'badge-proofread',
		'Q20748093' => 'badge-validated'
	);
}
