<?php

// Configure CirrusSearch settings for wikibase

// Cirrus usage for wbsearchentities is on
$wgWBRepoSettings['entitySearch']['useCirrus'] = true;
if ( !empty( $wmgNewWikibaseCirrusSearch ) ) {
	$wgWBCSUseCirrus = true;
}
if ( !empty( $wmgNewWikibaseLexemeCirrusSearch ) ) {
	// Enable new code
	$wgLexemeUseCirrus = true;
}

// T176903, T180169
$wgWBRepoSettings['entitySearch']['useStemming'] = [
	'ar' => [ 'index' => true, 'query' => true ],
	'bg' => [ 'index' => true, 'query' => true ],
	'ca' => [ 'index' => true, 'query' => true ],
	'ckb' => [ 'index' => true, 'query' => true ],
	'cs' => [ 'index' => true, 'query' => true ],
	'da' => [ 'index' => true, 'query' => true ],
	'de' => [ 'index' => true, 'query' => true ],
	'el' => [ 'index' => true, 'query' => true ],
	'en' => [ 'index' => true, 'query' => true ],
	'en-ca' => [ 'index' => true, 'query' => true ],
	'en-gb' => [ 'index' => true, 'query' => true ],
	'es' => [ 'index' => true, 'query' => true ],
	'eu' => [ 'index' => true, 'query' => true ],
	'fa' => [ 'index' => true, 'query' => true ],
	'fi' => [ 'index' => true, 'query' => true ],
	'fr' => [ 'index' => true, 'query' => true ],
	'ga' => [ 'index' => true, 'query' => true ],
	'gl' => [ 'index' => true, 'query' => true ],
	'he' => [ 'index' => true, 'query' => true ],
	'hi' => [ 'index' => true, 'query' => true ],
	'hu' => [ 'index' => true, 'query' => true ],
	'hy' => [ 'index' => true, 'query' => true ],
	'id' => [ 'index' => true, 'query' => true ],
	'it' => [ 'index' => true, 'query' => true ],
	'ja' => [ 'index' => true, 'query' => true ],
	'ko' => [ 'index' => true, 'query' => true ],
	'lt' => [ 'index' => true, 'query' => true ],
	'lv' => [ 'index' => true, 'query' => true ],
	'nb' => [ 'index' => true, 'query' => true ],
	'nl' => [ 'index' => true, 'query' => true ],
	'nn' => [ 'index' => true, 'query' => true ],
	'pl' => [ 'index' => true, 'query' => true ],
	'pt' => [ 'index' => true, 'query' => true ],
	'pt-br' => [ 'index' => true, 'query' => true ],
	'ro' => [ 'index' => true, 'query' => true ],
	'ru' => [ 'index' => true, 'query' => true ],
	'simple' => [ 'index' => true, 'query' => true ],
	'sv' => [ 'index' => true, 'query' => true ],
	'th' => [ 'index' => true, 'query' => true ],
	'tr' => [ 'index' => true, 'query' => true ],
	'uk' => [ 'index' => true, 'query' => true ],
	'zh' => [ 'index' => true, 'query' => true ],
];

// Properties to index
$wgWBRepoSettings['searchIndexProperties'] = $wmgWikibaseSearchIndexProperties;
// Statement boosting
$wgWBRepoSettings['entitySearch']['statementBoost'] = $wmgWikibaseSearchStatementBoosts;
// T163642, T99899
$wgWBRepoSettings['searchIndexTypes'] = [
	'string', 'external-id', 'wikibase-item', 'wikibase-property',
	'wikibase-lexeme', 'wikibase-form', 'wikibase-sense'
];
$wgWBRepoSettings['searchIndexPropertiesExclude'] = $wmgWikibaseSearchIndexPropertiesExclude;

if ( $wgDBname === 'wikidatawiki' || $wgDBname === 'testwikidatawiki' ) {
	// Load wikidata specific search config
	require_once "{$wmfConfigDir}/SearchSettingsForWikidata.php";
} elseif ( $wgDBname === 'commonswiki' || $wgDBname === 'testcommonswiki' ) {
	// Load SDoC specific search config
	require_once "{$wmfConfigDir}/SearchSettingsForSDC.php";
}
