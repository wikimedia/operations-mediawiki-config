<?php

require_once( "$IP/extensions/Wikidata/Wikidata.php" );

// The version number now comes from the Wikidata build,
// included above, so that cache invalidations can be in sync
// extension changes when there is a new extension branch or
// otherwise needed to change the cache key.
$wgWBSharedCacheKey = '-' . $wmgWikibaseCachePrefix;

if ( defined( 'HHVM_VERSION' ) ) {
	// Split the cache up for hhvm. T73461
	$wgWBSharedCacheKey .= '-hhvm';
}

$wgWBSharedSettings = [];

$wgWBSharedSettings['maxSerializedEntitySize'] = 2500;

$wgWBSharedSettings['siteLinkGroups'] = [
	'wikipedia',
	'wikibooks',
	'wikinews',
	'wikiquote',
	'wikisource',
	'wikiversity',
	'wikivoyage',
	'special'
];

$wgWBSharedSettings['specialSiteLinkGroups'] = [
	'commons',
	'mediawiki',
	'meta',
	'species'
];

$baseNs = 120;

// Define the namespace indexes for repo (and client wikis also need to be aware of these,
// thus entityNamespaces need to be a shared setting).
//
// NOTE: do *not* define WB_NS_ITEM and WB_NS_ITEM_TALK when using a core namespace for items!
define( 'WB_NS_PROPERTY', $baseNs );
define( 'WB_NS_PROPERTY_TALK', $baseNs + 1 );
define( 'WB_NS_QUERY', $baseNs + 2 );
define( 'WB_NS_QUERY_TALK', $baseNs + 3 );

// Tell Wikibase which namespace to use for which type of entities
// @note when we enable WikibaseRepo on commons, then having NS_MAIN for items
// will be a problem, though commons should be aware that Wikidata items are in
// the main namespace. (see T137444)
$wgWBSharedSettings['entityNamespaces'] = [
	'item' => NS_MAIN,
	'property' => WB_NS_PROPERTY
];

if ( in_array( $wgDBname, [ 'test2wiki', 'testwiki', 'testwikidatawiki' ] ) ) {
	$wgWBSharedSettings['specialSiteLinkGroups'][] = 'testwikidata';
} else {
	$wgWBSharedSettings['specialSiteLinkGroups'][] = 'wikidata';
}

if ( $wmgUseWikibaseRepo ) {
	$wgNamespaceAliases['Item'] = NS_MAIN;
	$wgNamespaceAliases['Item_talk'] = NS_TALK;

	// Define the namespaces
	$wgExtraNamespaces[WB_NS_PROPERTY] = 'Property';
	$wgExtraNamespaces[WB_NS_PROPERTY_TALK] = 'Property_talk';
	$wgExtraNamespaces[WB_NS_QUERY] = 'Query';
	$wgExtraNamespaces[WB_NS_QUERY_TALK] = 'Query_talk';

	$wgWBRepoSettings = $wgWBSharedSettings + $wgWBRepoSettings;

	$wgWBRepoSettings['statementSections'] = [
		'item' => [
			'statements' => null,
			'identifiers' => [
				'type' => 'dataType',
				'dataTypes' => [ 'external-id' ],
			],
		],
	];

	$wgWBRepoSettings['normalizeItemByTitlePageNames'] = true;

	$wgWBRepoSettings['dataRightsText'] = 'Creative Commons CC0 License';
	$wgWBRepoSettings['dataRightsUrl'] = 'https://creativecommons.org/publicdomain/zero/1.0/';

	if ( $wgDBname === 'testwikidatawiki' ) {
		// there is no cronjob dispatcher yet, this will do nothing
		$wgWBRepoSettings['clientDbList'] = [ 'testwiki', 'test2wiki', 'testwikidatawiki' ];
	} else {
		$wgWBRepoSettings['clientDbList'] = array_diff(
			MWWikiversions::readDbListFile( 'wikidataclient' ),
			[ 'testwikidatawiki', 'testwiki', 'test2wiki' ]
		);
		// Exclude closed wikis
		$wgWBRepoSettings['clientDbList'] = array_diff(
			$wgWBRepoSettings['clientDbList'],
			MWWikiversions::readDbListFile( $wmfRealm === 'labs' ? 'closed-labs' : 'closed' )
		);
	}

	$wgWBRepoSettings['localClientDatabases'] = array_combine(
		$wgWBRepoSettings['clientDbList'],
		$wgWBRepoSettings['clientDbList']
	);

	// T53637 and T48953
	$wgGroupPermissions['*']['property-create'] = ( $wgDBname === 'testwikidatawiki' );

	$wgCacheEpoch = '20160817192300';

	$wgWBRepoSettings['dataSquidMaxage'] = 1 * 60 * 60;
	$wgWBRepoSettings['sharedCacheDuration'] = 60 * 60 * 24;
	$wgWBRepoSettings['sharedCacheKeyPrefix'] .= $wgWBSharedCacheKey;

	$wgPropertySuggesterMinProbability = 0.069;

	// T72346
	$wgPropertySuggesterDeprecatedIds = [
		143, // imported from
		/**
		 * Deprecated properties
		 * @see https://www.wikidata.org/wiki/Special:WhatLinksHere/Q18644427
		 */
		357, // (OBSOLETE) title (use P1476, "title")
		513, // (OBSOLETE) birth name (use P1477)
		/**
		 * @see https://www.wikidata.org/w/index.php?oldid=335040857
		 */
		646, // Freebase ID
		/**
		 * Sandbox properties
		 * @see https://www.wikidata.org/wiki/Special:WhatLinksHere/Q18720640
		 */
		368,  // commonsMedia
		369,  // wikibase-item
		370,  // string
		578,  // time
		626,  // globe-coordinate
		855,  // url
		1106, // quantity
		1450, // monolingualtext
		2368, // wikibase-property
		2535, // math
		2536, // external-id
	];

	// Don't try to let users answer captchas if they try to add links
	// on either Item or Property pages. T86453
	$wgCaptchaTriggersOnNamespace[NS_MAIN]['addurl'] = false;
	$wgCaptchaTriggersOnNamespace[WB_NS_PROPERTY]['addurl'] = false;
}

if ( $wmgUseWikibaseClient ) {
	$wgWBClientSettings = $wgWBSharedSettings + $wgWBClientSettings;

	// to be safe, keeping this here although $wgDBname is default setting
	$wgWBClientSettings['siteGlobalID'] = $wgDBname;

	// Note: Wikibase-production.php overrides this for the test wikis
	$wgWBClientSettings['changesDatabase'] = 'wikidatawiki';
	$wgWBClientSettings['repoDatabase'] = 'wikidatawiki';

	$wgWBClientSettings['repoNamespaces'] = [
		'item' => '',
		'property' => 'Property'
	];

	$wgWBClientSettings['languageLinkSiteGroup'] = $wmgWikibaseSiteGroup;

	if ( in_array( $wgDBname, [ 'commonswiki', 'mediawikiwiki', 'metawiki', 'specieswiki' ] ) ) {
		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
	}

	$wgWBClientSettings['siteGroup'] = $wmgWikibaseSiteGroup;
	$wgWBClientSettings['otherProjectsLinksByDefault'] = true;

	$wgWBClientSettings['excludeNamespaces'] = function() {
		// @fixme 102 is LiquidThread comments on wikinews and elsewhere?
		// but is the Extension: namespace on mediawiki.org, so we need
		// to allow wiki-specific settings here.
		return array_merge(
			MWNamespace::getTalkNamespaces(),
			// 90 => LiquidThread threads
			// 92 => LiquidThread summary
			// 118 => Draft
			// 1198 => NS_TRANSLATE
			// 2600 => Flow topic
			[ NS_USER, NS_FILE, NS_MEDIAWIKI, 90, 92, 118, 1198, 2600 ]
		);
	};

	$wgWBClientSettings['interwikiSortOrders'] = [
		'alphabetic' => [
			'ace', 'kbd', 'ady', 'af', 'ak', 'als', 'am', 'ang', 'ab', 'ar', 'an', 'arc',
			'roa-rup', 'frp', 'as', 'ast', 'gn', 'av', 'ay', 'az', 'azb', 'bm', 'bn', 'bjn',
			'zh-min-nan', 'nan', 'map-bms', 'ba', 'be', 'be-x-old', 'bh', 'bcl', 'bi',
			'bg', 'bar', 'bo', 'bs', 'br', 'bxr', 'ca', 'cv', 'ceb', 'cs', 'ch',
			'cbk-zam', 'ny', 'sn', 'tum', 'cho', 'co', 'cy', 'da', 'dk', 'pdc', 'de',
			'dv', 'nv', 'dsb', 'dz', 'mh', 'et', 'el', 'eml', 'en', 'myv', 'es', 'eo',
			'ext', 'eu', 'ee', 'fa', 'hif', 'fo', 'fr', 'fy', 'ff', 'fur', 'ga', 'gv',
			'gag', 'gd', 'gl', 'gan', 'ki', 'glk', 'gu', 'got', 'gom', 'hak', 'xal', 'ko',
			'ha', 'haw', 'hy', 'hi', 'ho', 'hsb', 'hr', 'io', 'ig', 'ilo', 'bpy', 'id', 'ia',
			'ie', 'iu', 'ik', 'os', 'xh', 'zu', 'is', 'it', 'he', 'jv', 'kl', 'kn', 'kr',
			'pam', 'krc', 'ka', 'ks', 'csb', 'kk', 'kw', 'rw', 'rn', 'sw', 'kv', 'kg',
			'ht', 'ku', 'kj', 'ky', 'mrj', 'lad', 'lbe', 'lez', 'lo', 'lrc', 'ltg', 'la',
			'lv', 'lb', 'lt', 'lij', 'li', 'ln', 'olo', 'jbo', 'lg', 'lmo', 'hu', 'mai', 'mk',
			'mg', 'ml', 'mt', 'mi', 'mr', 'xmf', 'arz', 'mzn', 'ms', 'min', 'cdo', 'mwl',
			'mdf', 'mo', 'mn', 'mus', 'my', 'nah', 'na', 'fj', 'nl', 'nds-nl', 'cr', 'ne',
			'new', 'ja', 'nap', 'ce', 'frr', 'pih', 'no', 'nb', 'nn', 'nrm', 'nov', 'ii', 'oc',
			'mhr', 'or', 'om', 'ng', 'hz', 'uz', 'pa', 'pi', 'pfl', 'pag', 'pnb', 'pap', 'ps',
			'jam', 'koi', 'km', 'pcd', 'pms', 'tpi', 'nds', 'pl', 'tokipona', 'tp', 'pnt', 'pt',
			'aa', 'kaa', 'crh', 'ty', 'ksh', 'ro', 'rmy', 'rm', 'qu', 'rue', 'ru', 'sah',
			'se', 'sm', 'sa', 'sg', 'sc', 'sco', 'stq', 'st', 'nso', 'tn', 'sq', 'scn',
			'si', 'simple', 'sd', 'ss', 'sk', 'sl', 'cu', 'szl', 'so', 'ckb', 'srn', 'sr',
			'sh', 'su', 'fi', 'sv', 'tl', 'ta', 'shi', 'kab', 'roa-tara', 'tt', 'te', 'tet',
			'th', 'ti', 'tg', 'to', 'chr', 'chy', 've', 'tcy', 'tr', 'tk', 'tw', 'tyv', 'udm', 'bug',
			'uk', 'ur', 'ug', 'za', 'vec', 'vep', 'vi', 'vo', 'fiu-vro', 'wa', 'zh-classical',
			'vls', 'war', 'wo', 'wuu', 'ts', 'yi', 'yo', 'zh-yue', 'diq', 'zea', 'bat-smg',
			'zh', 'zh-tw', 'zh-cn'
		],
		'alphabetic_revised' => [
			'ace', 'ady', 'kbd', 'af', 'ak', 'als', 'am', 'ang', 'ab', 'ar', 'an', 'arc', 'roa-rup',
			'frp', 'as', 'ast', 'gn', 'av', 'ay', 'az', 'azb', 'bjn', 'id', 'ms', 'bm', 'bn',
			'zh-min-nan', 'nan', 'map-bms', 'jv', 'su', 'ba', 'min', 'be', 'be-x-old', 'bh',
			'bcl', 'bi', 'bar', 'bo', 'bs', 'br', 'bug', 'bg', 'bxr', 'ca', 'ceb', 'cv', 'cs',
			'ch', 'cbk-zam', 'ny', 'sn', 'tum', 'cho', 'co', 'cy', 'da', 'dk', 'pdc', 'de',
			'dv', 'nv', 'dsb', 'na', 'dz', 'mh', 'et', 'el', 'eml', 'en', 'myv', 'es', 'eo',
			'ext', 'eu', 'ee', 'fa', 'hif', 'fo', 'fr', 'fy', 'ff', 'fur', 'ga', 'gv', 'sm',
			'gag', 'gd', 'gl', 'gan', 'ki', 'glk', 'gu', 'got', 'gom', 'hak', 'xal', 'ko',
			'ha', 'haw', 'hy', 'hi', 'ho', 'hsb', 'hr', 'io', 'ig', 'ilo', 'bpy', 'ia', 'ie',
			'iu', 'ik', 'os', 'xh', 'zu', 'is', 'it', 'he', 'kl', 'kn', 'kr', 'pam', 'ka',
			'ks', 'csb', 'kk', 'kw', 'rw', 'ky', 'rn', 'mrj', 'sw', 'kv', 'kg', 'ht', 'ku',
			'kj', 'lad', 'lbe', 'lez', 'lo', 'la', 'lrc', 'ltg', 'lv', 'to', 'lb', 'lt', 'lij',
			'li', 'ln', 'olo', 'jbo', 'lg', 'lmo', 'hu', 'mai', 'mk', 'mg', 'ml', 'krc', 'mt',
			'mi', 'mr', 'xmf', 'arz', 'mzn', 'cdo', 'mwl', 'koi', 'mdf', 'mo', 'mn', 'mus', 'my',
			'nah', 'fj', 'nl',  'nds-nl', 'cr', 'ne', 'new', 'ja', 'nap', 'ce', 'frr', 'pih', 'no',
			'nb', 'nn', 'nrm', 'nov', 'ii', 'oc', 'mhr', 'or', 'om', 'ng', 'hz', 'uz', 'pa',
			'pi', 'pfl', 'pag', 'pnb', 'pap', 'ps', 'jam', 'km', 'pcd', 'pms', 'nds', 'pl', 'pnt',
			'pt', 'aa', 'kaa', 'crh', 'ty', 'ksh', 'ro', 'rmy', 'rm', 'qu', 'ru', 'rue', 'sah',
			'se', 'sa', 'sg', 'sc', 'sco', 'stq', 'st', 'nso', 'tn', 'sq', 'scn', 'si',
			'simple', 'sd', 'ss', 'sk', 'sl', 'cu', 'szl', 'so', 'ckb', 'srn', 'sr', 'sh',
			'fi', 'sv', 'tl', 'ta', 'shi', 'kab', 'roa-tara', 'tt', 'te', 'tet', 'th', 'vi',
			'ti', 'tg', 'tpi', 'tokipona', 'tp', 'chr', 'chy', 've', 'tcy', 'tr', 'tk', 'tw',
			'tyv', 'udm', 'uk', 'ur', 'ug', 'za', 'vec', 'vep', 'vo', 'fiu-vro', 'wa',
			'zh-classical', 'vls', 'war', 'wo', 'wuu', 'ts', 'yi', 'yo', 'zh-yue', 'diq',
			'zea', 'bat-smg', 'zh', 'zh-tw', 'zh-cn'
		],
		'alphabetic_sr' => [
			'ace', 'ady', 'kbd', 'af', 'ak', 'als', 'am', 'ang', 'ab', 'ar', 'an', 'arc',
			'roa-rup', 'frp', 'arz', 'as', 'ast', 'gn', 'av', 'ay', 'az', 'azb', 'bjn', 'id',
			'ms', 'bg', 'bm', 'zh-min-nan', 'nan', 'map-bms', 'jv', 'su', 'ba', 'be',
			'be-x-old', 'bh', 'bcl', 'bi', 'bn', 'bo', 'bar', 'bs', 'bpy', 'br', 'bug',
			'bxr', 'ca', 'ceb', 'ch', 'cbk-zam', 'sn', 'tum', 'ny', 'cho', 'chr', 'co',
			'cy', 'cv', 'cs', 'da', 'dk', 'pdc', 'de', 'nv', 'dsb', 'na', 'dv', 'dz',
			'mh', 'et', 'el', 'eml', 'en', 'myv', 'es', 'eo', 'ext', 'eu', 'ee', 'fa',
			'hif', 'fo', 'fr', 'fy', 'ff', 'fur', 'ga', 'gv', 'sm', 'gag', 'gd', 'gl',
			'gan', 'ki', 'glk', 'got', 'gom', 'gu', 'ha', 'hak', 'xal', 'haw', 'he',
			'hi', 'ho', 'hsb', 'hr', 'hy', 'io', 'ig', 'ii', 'ilo', 'ia', 'ie', 'iu',
			'ik', 'os', 'xh', 'zu', 'is', 'it', 'ja', 'ka', 'kl', 'kr', 'pam', 'krc',
			'csb', 'kk', 'kw', 'rw', 'ky', 'mrj', 'rn', 'sw', 'km', 'kn', 'ko', 'kv',
			'kg', 'ht', 'ks', 'ku', 'kj', 'lad', 'lbe', 'la', 'lrc', 'ltg', 'lv', 'to',
			'lb', 'lez', 'lt', 'lij', 'li', 'ln', 'olo', 'lo', 'jbo', 'lg', 'lmo', 'hu', 'mai',
			'mk', 'mg', 'mt', 'mi', 'min', 'cdo', 'mwl', 'ml', 'mdf', 'mo', 'mn', 'mr', 'mus',
			'my', 'mzn', 'nah', 'fj', 'ne', 'nl', 'nds-nl', 'cr', 'new', 'nap', 'ce',
			'frr', 'pih', 'no', 'nb', 'nn', 'nrm', 'nov', 'oc', 'mhr', 'or', 'om', 'ng',
			'hz', 'uz', 'pa', 'pfl', 'pag', 'pap', 'koi', 'pi', 'pcd', 'pms', 'nds',
			'pnb', 'pl', 'pt', 'pnt', 'ps', 'jam', 'aa', 'kaa', 'crh', 'ty', 'ksh', 'ro', 'rmy',
			'rm', 'qu', 'ru', 'rue', 'sa', 'sah', 'se', 'sg', 'sc', 'sco', 'sd', 'stq',
			'st', 'nso', 'tn', 'sq', 'si', 'scn', 'simple', 'ss', 'sk', 'sl', 'cu', 'szl',
			'so', 'ckb', 'srn', 'sr', 'sh', 'fi', 'sv', 'ta', 'shi', 'tl', 'kab',
			'roa-tara', 'tt', 'te', 'tet', 'th', 'ti', 'vi', 'tg', 'tokipona', 'tp',
			'tpi', 'chy', 've', 'tcy', 'tr', 'tk', 'tw', 'tyv', 'udm', 'uk', 'ur', 'ug', 'za', 'vec',
			'vep', 'vo', 'fiu-vro', 'wa', 'vls', 'war', 'wo', 'wuu', 'ts', 'xmf', 'yi',
			'yo', 'diq', 'zea', 'zh', 'zh-tw', 'zh-cn', 'zh-classical', 'zh-yue', 'bat-smg'
		],
		'alphabetic_fy' => [
			'aa', 'ab', 'ace', 'ady', 'af', 'ay', 'ak', 'als', 'am', 'an', 'ang', 'ar', 'arc',
			'arz', 'as', 'ast', 'av', 'az', 'azb', 'ba', 'bar', 'bat-smg', 'bcl', 'be', 'be-x-old',
			'bg', 'bh', 'bi', 'bjn', 'bm', 'bn', 'bo', 'bpy', 'br', 'bs', 'bug', 'bxr',
			'ca', 'cbk-zam', 'cdo', 'ce', 'ceb', 'ch', 'chy', 'cho', 'chr', 'cy', 'ckb',
			'co', 'cr', 'crh', 'cs', 'csb', 'cu', 'cv', 'da', 'de', 'diq', 'dk', 'dsb', 'dv',
			'dz', 'ee', 'el', 'eml', 'en', 'eo', 'es', 'et', 'eu', 'ext', 'fa', 'ff', 'fi',
			'fy', 'fiu-vro', 'fj', 'fo', 'fr', 'frp', 'frr', 'fur', 'ga', 'gag', 'gan', 'gd',
			'gl', 'glk', 'gn', 'got', 'gom', 'gu', 'gv', 'ha', 'hak', 'haw', 'he', 'hi', 'hy',
			'hif', 'ho', 'hr', 'hsb', 'ht', 'hu', 'hz', 'ia', 'id', 'ie', 'ig', 'ii', 'yi',
			'ik', 'ilo', 'io', 'yo', 'is', 'it', 'iu', 'ja', 'jam', 'jbo', 'jv', 'ka', 'kaa', 'kab',
			'kbd', 'kg', 'ki', 'ky', 'kj', 'kk', 'kl', 'km', 'kn', 'ko', 'koi', 'kr', 'krc',
			'ks', 'ksh', 'ku', 'kv', 'kw', 'la', 'lad', 'lb', 'lbe', 'lez', 'lg', 'li', 'lij',
			'lmo', 'ln', 'lo', 'lrc', 'lt', 'ltg', 'lv', 'mai', 'map-bms', 'mdf', 'mg', 'mh',
			'mhr', 'mi', 'my', 'min', 'myv', 'mk', 'ml', 'mn', 'mo', 'mr', 'mrj', 'ms', 'mt',
			'mus', 'mwl', 'mzn', 'na', 'nah', 'nan', 'nap', 'nds', 'nds-nl', 'ne', 'new', 'ng',
			'ny', 'nl', 'nn', 'no', 'nov', 'nrm', 'nso', 'nv', 'oc', 'olo', 'om', 'or', 'os', 'pa',
			'pag', 'pam', 'pap', 'pcd', 'pdc', 'pfl', 'pi', 'pih', 'pl', 'pms', 'pnb', 'pnt',
			'ps', 'pt', 'qu', 'rm', 'rmy', 'rn', 'ro', 'roa-rup', 'roa-tara', 'ru', 'rue',
			'rw', 'sa', 'sah', 'sc', 'scn', 'sco', 'sd', 'se', 'sg', 'sh', 'shi', 'si', 'simple',
			'sk', 'sl', 'sm', 'sn', 'so', 'sq', 'sr', 'srn', 'ss', 'st', 'stq', 'su', 'sv',
			'sw', 'szl', 'ta', 'tcy', 'te', 'tet', 'tg', 'th', 'ti', 'ty', 'tk', 'tl', 'tn', 'to',
			'tokipona', 'tp', 'tpi', 'tr', 'ts', 'tt', 'tum', 'tw', 'tyv', 'udm', 'ug', 'uk', 'ur',
			'uz', 've', 'vec', 'vep', 'vi', 'vls', 'vo', 'wa', 'war', 'wo', 'wuu', 'xal',
			'xh', 'xmf', 'za', 'zea', 'zh', 'zh-classical', 'zh-cn', 'zh-yue', 'zh-min-nan',
			'zh-tw', 'zu'
		],
	];

	if ( $wgDBname === 'wikidatawiki' || $wgDBname === 'testwikidatawiki' ) {
		$wgWBClientSettings['namespaces'] = [
			NS_CATEGORY,
			NS_PROJECT,
			NS_TEMPLATE,
			NS_HELP,
			828 // NS_MODULE
		];

		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
		$wgWBClientSettings['injectRecentChanges'] = false;
		$wgWBClientSettings['showExternalRecentChanges'] = false;
	}

	foreach( $wmgWikibaseClientSettings as $setting => $value ) {
		$wgWBClientSettings[$setting] = $value;
	}

	$wgWBClientSettings['allowDataTransclusion'] = $wmgWikibaseEnableData;
	$wgWBClientSettings['allowDataAccessInUserLanguage'] = $wmgWikibaseAllowDataAccessInUserLanguage;
	$wgWBClientSettings['entityAccessLimit'] = $wmgWikibaseEntityAccessLimit;

	$wgWBClientSettings['sharedCacheKeyPrefix'] .= $wgWBSharedCacheKey;
	$wgWBClientSettings['sharedCacheDuration'] = 60 * 60 * 24;
}

require_once "{$wmfConfigDir}/Wikibase-{$wmfRealm}.php";
