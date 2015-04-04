<?php

if ( $wmgUseWikibaseRepo ) {
	$wgWBRepoSettings['badgeItems'] = array(
		'Q49444' => 'wb-badge-goodarticle',
		'Q49447' => 'wb-badge-featuredarticle',
		'Q49448' => 'wb-badge-recommendedarticle', // Bug T72268
		'Q49449' => 'wb-badge-featuredlist', // Bug T72332
		'Q49450' => 'wb-badge-featuredportal', // Bug T75193
	);
}

if ( $wmgUseWikibaseClient ) {
	$wgWBClientSettings['badgeClassNames'] = array(
		'Q49444' => 'badge-goodarticle',
		'Q49447' => 'badge-featuredarticle',
		'Q49448' => 'badge-recommendedarticle', // Bug T72268
		'Q49449' => 'badge-featuredlist', // Bug T72332
		'Q49450' => 'badge-featuredportal', // Bug T75193
	);
}
