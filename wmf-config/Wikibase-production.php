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
			'Q17559452' => 'wb-badge-recommendedarticle', // Bug T72268
			'Q17506997' => 'wb-badge-featuredlist', // Bug T72332
			'Q17580674' => 'wb-badge-featuredportal', // Bug T75193
		);
	}
}

if ( $wmgUseWikibaseClient ) {
	$wgWBClientSettings['badgeClassNames'] = array(
		'Q17437796' => 'badge-featuredarticle',
		'Q17437798' => 'badge-goodarticle',
		'Q17559452' => 'badge-recommendedarticle', // Bug T72268
		'Q17506997' => 'badge-featuredlist', // Bug T72332
		'Q17580674' => 'badge-featuredportal', // Bug T75193
	);
}
