<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file holds the MediaWiki CirrusSearch configuration which is specific
# to the 'labs' realm which in most of the cases means the beta cluster.
# It should be loaded AFTER CirrusSearch-common.php

$wgCirrusSearchClusters = array(
	'eqiad' => array_map( function ( $host ) {
		return array(
			'transport' => 'Https',
			'port' => '9243',
			'host' => $host,
			'curl' => array(
				// We might not need to specify the cert explicitly,
				// CURLOPT_CAINFO => '/etc/ssl/certs/wmf-labs.pem',
			),
		);
	}, $wmfAllServices['eqiad']['search'] ),
);

if ( $wgDBname == 'enwiki' ) {
	$wgCirrusSearchInterwikiSources = array(
		'wiktionary' => 'enwiktionary',
		'wikibooks' => 'enwikibooks',
		'wikinews' => 'enwikinews',
		'wikiquote' => 'enwikiquote',
		'wikisource' => 'enwikisource',
		'wikiversity' => 'enwikiversity',
	);
}

# We don't have enough nodes to support these settings in beta so just turn
# them off.
$wgCirrusSearchMaxShardsPerNode = array();

# Use the safer query from the extra extension that is currently only deployed
# in beta.
$wgCirrusSearchWikimediaExtraPlugin[ 'safer' ] = array(
	'phrase' => array(
	)
);

# write to all configured clusters, there should only be one in labs
$wgCirrusSearchWriteClusters = null;

$wgCirrusSearchEnableSearchLogging = true;

$wgCirrusSearchLanguageToWikiMap = array(
    'ar' => 'ar',
    'de' => 'de',
    'en' => 'en',
    'es' => 'es',
    'fa' => 'fa',
    'he' => 'he',
    'hi' => 'hi',
    'ja' => 'ja',
    'ko' => 'ko',
    'ru' => 'ru',
    'sq' => 'sq',
    'uk' => 'uk',
    'zh-cn' => 'zh',
    'zh-tw' => 'zh',
);

$wgCirrusSearchWikiToNameMap = array(
    'ar' => 'arwiki',
    'de' => 'dewiki',
    'en' => 'enwiki',
    'es' => 'eswiki',
    'fa' => 'fawiki',
    'he' => 'hewiki',
    'hi' => 'hiwiki',
    'ja' => 'jawiki',
    'ko' => 'kowiki',
    'ru' => 'ruwiki',
    'sq' => 'sqwiki',
    'uk' => 'ukwiki',
    'zh' => 'zhwiki',
);

