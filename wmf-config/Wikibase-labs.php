<?php

if ( $wmgUseWikibaseRepo ) {
	$wgWBRepoSettings['badgeItems'] = array(
		'Q49444' => 'wb-badge-goodarticle',
		'Q49447' => 'wb-badge-featuredarticle',
		'Q49448' => 'wb-badge-recommendedarticle', // bug 70268
		'Q49449' => 'wb-badge-featuredlist', // bug 70332
		'Q49450' => 'wb-badge-featuredportal', // bug 73193
	);
}

if ( $wmgUseWikibaseClient ) {
	$wgWBClientSettings['badgeClassNames'] = array(
		'Q49444' => 'badge-goodarticle',
		'Q49447' => 'badge-featuredarticle',
		'Q49448' => 'badge-recommendedarticle', // bug 70268
		'Q49449' => 'badge-featuredlist', // bug 70332
		'Q49450' => 'badge-featuredportal', // bug 73193
	);
}
