<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file holds common overrides specific to Beta Cluster.
# For per-wiki overrides, see InitialiseSettings-labs.php.
#
# This for BETA and MUST NOT be loaded for production.
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/*Services.php
# - wmf-config/InitialiseSettings.php
# - wmf-config/InitialiseSettings-labs.php
# - wmf-config/CommonSettings.php
# - wmf-config/CommonSettings-labs.php [THIS FILE]
#
# Included from: wmf-config/CommonSettings.php.
#

if ( $wmfRealm == 'labs' ) { # safe guard

$wmgAddWikiNotify = false;

# Use a different address from the production one - T192686
$wgPasswordSender = 'wiki@wikimedia.beta.wmflabs.org';

# Enable for all Beta wikis, depends on $wmfAllServices.
$wgDebugLogFile = "udp://{$wmfUdp2logDest}/wfDebug";

// Password policies; see https://meta.wikimedia.org/wiki/Password_policy
$wmgPrivilegedPolicy = [
	'MinimalPasswordLength' => [ 'value' => 10, 'suggestChangeOnLogin' => true ],
	'MinimumPasswordLengthToLogin' => [ 'value' => 1, 'suggestChangeOnLogin' => true ],
	'PasswordNotInLargeBlacklist' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
];
foreach ( $wmgPrivilegedGroups as $group ) {
	// On non-SUL wikis this is the effective password policy. On SUL wikis, it will be overridden
	// in the PasswordPoliciesForUser hook, but still needed for Special:PasswordPolicies
	if ( $group === 'user' ) {
		$group = 'default'; // For e.g. private and fishbowl wikis; covers 'user' in password policies
	}
	$wgPasswordPolicy['policies'][$group] = array_merge( $wgPasswordPolicy['policies'][$group] ?? [],
		$wmgPrivilegedPolicy );

}

$wgPasswordPolicy['policies']['default']['MinimalPasswordLength'] = [
	'value' => 8,
	'suggestChangeOnLogin' => false,
];

$wgPasswordPolicy['policies']['default']['PasswordCannotBePopular'] = [
	'value' => 100,
	'suggestChangeOnLogin' => true,
];

// Enforce password policy when users login on other wikis; also for sensitive global groups
// FIXME does this just duplicate the the global policy checks down in the main $wmgUseCentralAuth block?
if ( $wmgUseCentralAuth ) {
	$wgHooks['PasswordPoliciesForUser'][] = function ( User $user, array &$effectivePolicy ) use ( $wmgPrivilegedPolicy ) {
		$privilegedGroups = wmfGetPrivilegedGroups( $user->getName(), $user );
		if ( $privilegedGroups ) {
			$effectivePolicy = UserPasswordPolicy::maxOfPolicies( $effectivePolicy, $wmgPrivilegedPolicy );
			// hack; PasswordNotInLargeBlacklist obsoletes PasswordCannotBePopular but maxOfPolicies can't handle that
			if ( $effectivePolicy['PasswordNotInLargeBlacklist'] ?? false ) {
				$effectivePolicy['PasswordCannotBePopular'] = 0;
			}

			if ( in_array( 'staff', $privilegedGroups, true ) ) {
				$effectivePolicy['MinimumPasswordLengthToLogin'] = [
					'value' => 10,
					'suggestChangeOnLogin' => true,
				];
			}
		}
		return true;
	};
}

$wgLocalVirtualHosts = [
	'wikipedia.beta.wmflabs.org',
	'wiktionary.beta.wmflabs.org',
	'wikibooks.beta.wmflabs.org',
	'wikiquote.beta.wmflabs.org',
	'wikinews.beta.wmflabs.org',
	'wikisource.beta.wmflabs.org',
	'wikiversity.beta.wmflabs.org',
	'wikivoyage.beta.wmflabs.org',
	'meta.wikimedia.beta.wmflabs.org',
	'commons.wikimedia.beta.wmflabs.org',
	'api.wikimedia.beta.wmflabs.org',
];

// T49647
$wgHooks['EnterMobileMode'][] = function () {
	global $wgCentralAuthCookieDomain, $wgHooks;
	$domainRegexp = '/(?<!\.m)\.wikimedia\.beta\.wmflabs\.org$/';
	$mobileDomain = '.m.wikimedia.beta.wmflabs.org';

	if ( preg_match( $domainRegexp, $wgCentralAuthCookieDomain ) ) {
		$wgCentralAuthCookieDomain = preg_replace( $domainRegexp, $mobileDomain, $wgCentralAuthCookieDomain );
	}
	$wgHooks['WebResponseSetCookie'][] = function ( &$name, &$value, &$expire, &$options ) use ( $domainRegexp, $mobileDomain ) {
		if ( isset( $options['domain'] ) && preg_match( $domainRegexp, $options['domain'] ) ) {
			$options['domain'] = preg_replace( $domainRegexp, $mobileDomain, $options['domain'] );
		}
	};
};

# Attempt to auto block users using faulty servers
# See also http://www.us.sorbs.net/general/using.shtml
$wgEnableDnsBlacklist = true;
$wgDnsBlacklistUrls   = [
	'proxies.dnsbl.sorbs.net.',
];

if ( $wmgUseGlobalPreferences ) {
	// Allow global preferences for email-blacklist to be auto-set where it is overridden
	// T231577
	$wgGlobalPreferencesAutoPrefs = [
		'email-blacklist',
		'echo-notifications-blacklist'
	];
}

if ( $wmgUseFlow ) {
	// Override CommonSettings.php, which has:
	// $wgFlowExternalStore = $wgDefaultExternalStore;
	$wgFlowExternalStore = [
		'DB://flow_cluster1',
	];

	$wgExtraNamespaces += [
		190 => 'Flow_test',
		191 => 'Flow_test_talk',
	];

	$wgNamespacesWithSubpages += [
		190 => true,
		191 => true,
	];

	$wgNamespaceContentModels[ 191 ] = 'flow-board'; // CONTENT_MODEL_FLOW_BOARD
}

if ( $wmgUseFileExporter ) {
	// On Beta don't bother enabling beta feature mode.
	$wgFileExporterBetaFeature = false;
	$wgFileExporterTarget = 'https://commons.wikimedia.beta.wmflabs.org/wiki/Special:ImportFile';
}

if ( $wmgUseContentTranslation ) {
	$wgContentTranslationSiteTemplates['cx'] = 'https://cxserver-beta.wmflabs.org/v1';
	$wgContentTranslationSiteTemplates['cookieDomain'] = false;
	$wgContentTranslationTranslateInTarget = false;
}

if ( $wmgUseCentralAuth ) {
	$wgCentralAuthUseSlaves = true;
}

if ( $wmgUseCentralNotice ) {
	// Emit CSP headers on banner previews. This can go away when full CSP
	// support (T135963) is deployed.
	// www.pages04.net is used by Wikimedia Fundraising to enable 'remind me later' banner functionality, which submits email addresses to our email campaign vendor
	$wgCentralNoticeContentSecurityPolicy = "script-src 'unsafe-eval' 'self' meta.wikimedia.beta.wmflabs.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikivoyage.org *.mediawiki.org 'unsafe-inline'; default-src 'self' data: blob: https://upload.beta.wmflabs.org upload.beta.wmflabs.org https://commons.wikimedia.beta.wmflabs.org https://upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.beta.wmflabs.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikivoyage.org *.mediawiki.org wikimedia.org www.pages04.net; style-src 'self' data: blob: https://upload.beta.wmflabs.org upload.beta.wmflabs.org https://commons.wikimedia.beta.wmflabs.org https://upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.beta.wmflabs.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikivoyage.org *.mediawiki.org wikimedia.org 'unsafe-inline';";
}

if ( $wmgUseCite ) {
	// Temporary until we deploy to production, T236894
	$wgCiteBookReferencing = true;
}

// Labs override for GlobalCssJs
if ( $wmgUseGlobalCssJs && $wmgUseCentralAuth ) {
	// Load from betalabs metawiki
	$wgResourceLoaderSources['metawiki'] = [
		'apiScript' => '//meta.wikimedia.beta.wmflabs.org/w/api.php',
		'loadScript' => '//meta.wikimedia.beta.wmflabs.org/w/load.php',
	];
}

if ( $wmgUseGlobalUserPage && $wmgUseCentralAuth ) {
	// Labs override
	$wgGlobalUserPageAPIUrl = 'https://meta.wikimedia.beta.wmflabs.org/w/api.php';
	$wgGlobalUserPageDBname = 'metawiki';
}

if ( $wmgUseUrlShortener ) {
	// Labs overrides
	$wgUrlShortenerReadOnly = false;
	$wgUrlShortenerServer = 'w-beta.wmflabs.org';
	$wgUrlShortenerDBCluster = false;
	$wgUrlShortenerDBName = 'wikishared';
	$wgUrlShortenerDomainsWhitelist = [
		'(.*\.)?wikipedia\.beta\.wmflabs\.org',
		'(.*\.)?wiktionary\.beta\.wmflabs\.org',
		'(.*\.)?wikibooks\.beta\.wmflabs\.org',
		'(.*\.)?wikinews\.beta\.wmflabs\.org',
		'(.*\.)?wikiquote\.beta\.wmflabs\.org',
		'(.*\.)?wikisource\.beta\.wmflabs\.org',
		'(.*\.)?wikiversity\.beta\.wmflabs\.org',
		'(.*\.)?wikivoyage\.beta\.wmflabs\.org',
		'(.*\.)?wikimedia\.beta\.wmflabs\.org',
		'(.*\.)?wikidata\.beta\.wmflabs\.org',
	];
	$wgUrlShortenerApprovedDomains = [
		'*.wikipedia.beta.wmflabs.org',
		'*.wiktionary.beta.wmflabs.org',
		'*.wikibooks.beta.wmflabs.org',
		'*.wikinews.beta.wmflabs.org',
		'*.wikiquote.beta.wmflabs.org',
		'*.wikisource.beta.wmflabs.org',
		'*.wikiversity.beta.wmflabs.org',
		'*.wikivoyage.beta.wmflabs.org',
		'*.wikimedia.beta.wmflabs.org',
		'*.wikidata.beta.wmflabs.org',
	];
}

// Labs override for BounceHandler
if ( $wmgUseBounceHandler ) {
	// $wgVERPsecret = ''; // This was set in PrivateSettings.php by Legoktm
	$wgBounceHandlerCluster = false;
	$wgBounceHandlerSharedDB = false;
	$wgBounceHandlerInternalIPs = [ '127.0.0.1', '::1', '172.16.4.120' ]; // deployment-mx02.deployment-prep.eqiad.wmflabs
	$wgBounceHandlerUnconfirmUsers = true;
	$wgBounceRecordLimit = 5;
	$wgVERPdomainPart = 'beta.wmflabs.org';
}

if ( $wmgUseTimedMediaHandler ) {
	$wgMwEmbedModuleConfig[ 'MediaWiki.ApiProviders' ] = [
	"commons" => [
		'url' => '//commons.wikimedia.beta.wmflabs.org/w/api.php'
	] ];
	$wgEnableTranscode = true; // enable transcoding on labs
	$wgFFmpegLocation = '/usr/bin/ffmpeg'; // use new ffmpeg build w/ VP9 & Opus support
}

// Enable Flickr uploads on commons beta T86120
if ( $wgDBname == 'commonswiki' ) {
	$wgGroupPermissions['user']['upload'] = true;
	$wgGroupPermissions['user']['upload_by_url'] = true;
} else { // Use InstantCommons on all betawikis except commonswiki
	$wgUseInstantCommons = true;
}

if ( $wmgEnableJsonConfigDataMode && $wgDBname !== 'commonswiki' ) {

	// Enable Tabular data namespace on Commons - T148745
	$wgJsonConfigs['Tabular.JsonConfig']['remote']['url'] = 'https://commons.wikimedia.beta.wmflabs.org/w/api.php';

	// Enable Map (GeoJSON) data namespace on Commons - T149548
	$wgJsonConfigs['Map.JsonConfig']['remote']['url'] = 'https://commons.wikimedia.beta.wmflabs.org/w/api.php';
}

if ( $wmgUseMath ) {
	$wgDefaultUserOptions[ 'math' ] = 'mathml';
	$wgMathEnableFormulaLinks = true;
	$wgMathWikibasePropertyIdHasPart = 'P253104';
	$wgMathWikibasePropertyIdDefiningFormula = 'P253105';
	$wgMathWikibasePropertyIdQuantitySymbol = 'P253106';
}

// CORS (cross-domain AJAX, T22814)
// This lists the domains that are accepted as *origins* of CORS requests
// DO NOT add domains here that aren't WMF wikis unless you really know what you're doing
if ( $wmgUseCORS ) {
	$wgCrossSiteAJAXdomains = [
		'*.beta.wmflabs.org',
	];
}

// Temporary override to test T68699 on Beta Cluster.  Remove when in production.
$wgExtendedLoginCookieExpiration = 365 * 86400;

if ( $wmgUseCollection ) {
	$wgCollectionPortletFormats[] = 'rdf2text';
	// Don't use production proxy to reach PediaPress
	$wgCollectionCommandToServeURL[ 'zip_post' ] = 'https://pediapress.com/wmfup/';
}

if ( $wmgUsePageImages ) {
	$wgPageImagesBlacklist[] = [
		'type' => 'db',
		'page' => 'MediaWiki:Pageimages-blacklist',
		'db' => 'commonswiki',
	];
}

if ( $wmgUseQuickSurveys ) {
	$wgQuickSurveysRequireHttps = false;
}

if ( $wmgUseEcho && $wmgUseCentralAuth ) {
	$wgEchoSharedTrackingDB = 'wikishared';
	// Set cluster back to false, to override CommonSettings.php setting it to 'extension1'
	$wgEchoSharedTrackingCluster = false;
}

// Enabling thank-you-edit on beta for testing T128249. Still disabled in prod.
$wgEchoNotifications['thank-you-edit']['notify-type-availability']['web'] = true;

// Enabling article-reminder on beta for testing T166973. Still disabled in prod.
$wgAllowArticleReminderNotification = true;

if ( $wmgUseGraph ) {
	// **** THIS LIST MUST MATCH puppet/blob/production/hieradata/cloud/eqiad1/deployment-prep/common.yaml ****
	// See https://www.mediawiki.org/wiki/Extension:Graph#External_data
	$wgGraphAllowedDomains = [
		'http' => [
			'wmflabs.org',
		],
		'https' => [
			'beta.wmflabs.org',
		],
		'wikirawupload' => [
			'upload.beta.wmflabs.org',
			'upload.wikimedia.org',
		],
		'wikidatasparql' => [
			'wdqs-test.wmflabs.org',
			'query.wikidata.org',
		],
		'geoshape' => [
			'maps.wikimedia.org',
		],
	];
}

if ( $wmgUseORES ) {
	$wgOresBaseUrl = 'https://ores-beta.wmflabs.org/';
}

if ( $wmgUseUniversalLanguageSelector ) {
	$wgDefaultUserOptions['compact-language-links'] = 0;
}

$wgLoginNotifyAttemptsKnownIP = 10;
$wgLoginNotifyAttemptsNewIP = 1;

if ( $wmgUseKartographer ) {
	$wgKartographerMapServer = 'https://maps-beta.wmflabs.org';
}

if ( $wmgUseTwoColConflict ) {
	$wgTwoColConflictBetaFeature = false;
	$wgTwoColConflictSuggestResolution = true;
}

if ( $wmgUseFileImporter ) {
	// Beta commons references configuration files hosted locally.
	// Note that beta testwiki will continue to fetch its configuration from production mw.org .
	if ( $wgDBname == 'commonswiki' ) {
		$wgFileImporterCommonsHelperServer = 'https://commons.wikimedia.beta.wmflabs.org';
		$wgFileImporterCommonsHelperBasePageName = 'Extension:FileImporter/Data/';
		$wgFileImporterCommonsHelperHelpPage = 'https://commons.wikimedia.beta.wmflabs.org/wiki/Extension:FileImporter/Data';
		$wgFileImporterWikidataEntityEndpoint = 'https://wikidata.beta.wmflabs.org/wiki/Special:EntityData/';
		$wgFileImporterWikidataNowCommonsEntity = 'Q531650';
	}
}

if ( $wmgUseEventBus ) {
	$wgEventBusEnableRunJobAPI = true;
}

$wgMessageCacheType = CACHE_ACCEL;

// This will work for most wikis, which is considered good enough.
$wgPageViewInfoWikimediaDomain = "$lang.$site.org";
$wgGERestbaseUrl = $wgGERestbaseUrl ?: "https://$lang.$site.org/api/rest_v1";

// Let Beta Cluster Commons do upload-from-URL from production Commons.
if ( $wgDBname == 'commonswiki' ) {
	$wgCopyUploadsDomains[] = 'upload.wikimedia.org';
}

// Turn off exact search match redirects on beta commons
if ( $wgDBname == 'commonswiki' ) {
	$wgDefaultUserOptions['search-match-redirect'] = false;
}

// Test of new import source configuration on labs cluster
$wgImportSources = false;
include "$wmfConfigDir/import.php";
$wgHooks['ImportSources'][] = 'wmfImportSources';

$wgAuthManagerAutoConfig['preauth'][GuanacoProvider::class] = [
	'class' => GuanacoProvider::class,
	'sort' => 0,
];
class GuanacoProvider extends \MediaWiki\Auth\AbstractPreAuthenticationProvider {
	private const EVILUA = 'Bawolff test';

	/**
	 * @param User $user
	 * @param array $autocreate
	 * @param array $options
	 * @return Status
	 */
	public function testUserForCreation( $user, $autocreate, array $options = [] ) {
		return $this->testUser( $user );
	}

	/**
	 * @param User $user
	 * @param User $creator
	 * @param array $reqs
	 * @return Status
	 */
	public function testForAccountCreation( $user, $creator, array $reqs ) {
		return $this->testUser( $user );
	}

	/**
	 * @param User $user
	 * @return Status
	 */
	public function testUser( $user ) {
		$ua = $this->manager->getRequest()->getHeader( 'User-agent' );
		$logger = \MediaWiki\Logger\LoggerFactory::getInstance( 'badpass' );
		if ( $ua === self::EVILUA ) {
			$logger->info( 'Account creation prevented due to UA {name}', [
				'successful' => false,
				'name' => $user->getName(),
				'ua' => $ua,
			] );
			// To be misleading, claim its a throttle hit.
			// hopefully this will confuse attacker.
			$msg = wfMessage( 'acct_creation_throttle_hit' )->params( 6 )
				->durationParams( 86400 );
			return \StatusValue::newFatal( $msg );
		}

		$logger->info( 'Account creation allowed due to UA {name}', [
			'successful' => true,
			'name' => $user->getName(),
			'ua' => $ua,
		] );
		return \StatusValue::newGood();
	}
}

if ( $wgDBname == 'apiportalwiki' ) {
	// Needs integrating properly, but this works for now as
	// IS-labs doesn't have groupOverrides etc

	$wgGroupPermissions['user']['move'] = false;
	$wgGroupPermissions['user']['move-subpages'] = false;
	$wgGroupPermissions['user']['edit'] = false;
	$wgGroupPermissions['user']['createpage'] = false;

	$wgGroupPermissions['docseditor']['docseditor'] = true;
	$wgGroupPermissions['docseditor']['move'] = true;
	$wgGroupPermissions['docseditor']['move-subpages'] = true;
	$wgGroupPermissions['docseditor']['edit'] = true;
	$wgGroupPermissions['docseditor']['createpage'] = true;

	$wgGroupPermissions['sysop']['docseditor'] = true;
	$wgGroupPermissions['sysop']['move'] = true;
	$wgGroupPermissions['sysop']['move-subpages'] = true;
	$wgGroupPermissions['sysop']['edit'] = true;
	$wgGroupPermissions['sysop']['createpage'] = true;

	$wgGroupPermissions['bureaucrat']['docseditor'] = true;
	$wgGroupPermissions['bureaucrat']['move'] = true;
	$wgGroupPermissions['bureaucrat']['move-subpages'] = true;
	$wgGroupPermissions['bureaucrat']['edit'] = true;
	$wgGroupPermissions['bureaucrat']['createpage'] = true;

	$wgNamespaceProtection[NS_MAIN] = [ 'docseditor' ];
	$wgNamespaceProtection[NS_PROJECT] = [ 'docseditor' ];
	// Local uploads are disabled...
	$wgNamespaceProtection[NS_FILE] = [ 'docseditor' ];
	$wgNamespaceProtection[NS_TEMPLATE] = [ 'docseditor' ];
	$wgNamespaceProtection[NS_HELP] = [ 'docseditor' ];
	$wgNamespaceProtection[NS_CATEGORY] = [ 'docseditor' ];
}

} # end safeguard
