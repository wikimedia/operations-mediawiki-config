<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.

/**
 * This file is for overriding the default InitialiseSettings.php with our own
 * stuff. Prefixing a setting key with '-' to override all values from
 * InitialiseSettings.php
 *
 * Please wrap your code in functions to avoid tainting the global namespace.
 */

/**
 * Main entry point to override production settings. Supports key beginning wit
 * a dash to completely override a setting.
 * Settings are fetched through wmfLabsSettings() defined below.
 */
function wmfLabsOverrideSettings() {
	global $wmfConfigDir, $wgConf;

	// Override (or add) settings that we need within the labs environment,
	// but not in production.
	$betaSettings = wmfLabsSettings();

	foreach ( $betaSettings as $key => $value ) {
		if ( substr( $key, 0, 1 ) == '-' ) {
			// Settings prefixed with - are completely overriden
			$wgConf->settings[substr( $key, 1 )] = $value;
		} elseif ( isset( $wgConf->settings[$key] ) ) {
			$wgConf->settings[$key] = array_merge( $wgConf->settings[$key], $value );
		} else {
			$wgConf->settings[$key] = $value;
		}
	}
}

/**
 * Return settings for wmflabs cluster. This is used by wmfLabsOverride().
 * Keys that start with a hyphen will completely override the regular settings
 * in InitializeSettings.php. Keys that don't start with a hyphen will have
 * their settings combined with the regular settings.
 */
function wmfLabsSettings() {
return array(
	'wgParserCacheType' => array(
		'default' => CACHE_MEMCACHED,
	),

	'wgSitename' => array(
		'labswiki' => 'Deployment',
		'ee_prototypewiki' => 'Editor Engagement Prototype',
		'wikivoyage'    => 'Wikivoyage',
	),

	'wgServer' => array(
		'default'     => '//$lang.wikipedia.beta.wmflabs.org',
		'wiktionary'	=> '//$lang.wiktionary.beta.wmflabs.org',
		'wikipedia'     => '//$lang.wikipedia.beta.wmflabs.org',
		'wikiversity'	=> '//$lang.wikiversity.beta.wmflabs.org',
		'wikispecies'	=> '//$lang.wikispecies.beta.wmflabs.org',
		'wikisource'	=> '//$lang.wikisource.beta.wmflabs.org',
		'wikiquote'	=> '//$lang.wikiquote.beta.wmflabs.org',
		'wikinews'	=> '//$lang.wikinews.beta.wmflabs.org',
		'wikibooks'     => '//$lang.wikibooks.beta.wmflabs.org',
		'wikivoyage'    => '//$lang.wikivoyage.beta.wmflabs.org',

		'metawiki'      => '//meta.wikimedia.beta.wmflabs.org',
		'commonswiki'   => '//commons.wikimedia.beta.wmflabs.org',
		'labswiki'      => '//deployment.wikimedia.beta.wmflabs.org',
		'ee_prototypewiki' => '//ee-prototype.wikipedia.beta.wmflabs.org',
		'loginwiki'     => '//login.wikimedia.beta.wmflabs.org',
		'testwiki'      => '//test.wikimedia.beta.wmflabs.org',
		'wikidatawiki'  => '//wikidata.beta.wmflabs.org',
	),

	'wgCanonicalServer' => array(
		'default'     => 'http://$lang.wikipedia.beta.wmflabs.org',
		'wikipedia'     => 'http://$lang.wikipedia.beta.wmflabs.org',
		'wikibooks'     => 'http://$lang.wikibooks.beta.wmflabs.org',
		'wikiquote'	=> 'http://$lang.wikiquote.beta.wmflabs.org',
		'wikinews'	=> 'http://$lang.wikinews.beta.wmflabs.org',
		'wikisource'	=> 'http://$lang.wikisource.beta.wmflabs.org',
		'wikiversity'     => 'http://$lang.wikiversity.beta.wmflabs.org',
		'wiktionary'     => 'http://$lang.wiktionary.beta.wmflabs.org',
		'wikispecies'     => 'http://$lang.wikispecies.beta.wmflabs.org',
		'wikivoyage'    => 'http://$lang.wikivoyage.beta.wmflabs.org',

		'metawiki'      => 'http://meta.wikimedia.beta.wmflabs.org',
		'ee_prototypewiki' => 'http://ee-prototype.wikipedia.beta.wmflabs.org',
		'commonswiki'	=> 'http://commons.wikimedia.beta.wmflabs.org',
		'labswiki'      => 'http://deployment.wikimedia.beta.wmflabs.org',
		'loginwiki'     => 'http://login.wikimedia.beta.wmflabs.org',
		'testwiki'      => 'http://test.wikimedia.beta.wmflabs.org',
		'wikidatawiki'  => 'http://wikidata.beta.wmflabs.org',
	),

	'wmgUsabilityPrefSwitch' => array(
		'default' => ''
	),

	'wmgUseLiquidThreads' => array(
		'testwiki' => true,
	),

	'-wgUploadDirectory' => array(
		'default'      => '/data/project/upload7/$site/$lang',
		'private'      => '/data/project/upload7/private/$lang',
	),

	/* 'wmgUseOnlineStatusBar' => array( */
	/* 	'default' => false, */
	/* ), */

	'-wgUploadPath' => array(
		'default' => '//upload.beta.wmflabs.org/$site/$lang',
		'private' => '/w/img_auth.php',
	//	'wikimania2005wiki' => '//upload..org/wikipedia/wikimania', // back compat
		'commonswiki' => '//upload.beta.wmflabs.org/wikipedia/commons',
		'metawiki' => '//upload.beta.wmflabs.org/wikipedia/meta',
		'testwiki' => '//upload.beta.wmflabs.org/wikipedia/test',
	),

	'-wmgMathPath' => array(
		'default' => '//upload.beta.wmflabs.org/math',
	),

	'wmgNoticeProject' => array(
		'labswiki' => 'meta',
	),

	//'-wgDebugLogGroups' => array(),
	'-wgRateLimitLog' => array(),
	'-wgJobLogFile' => array(),

	// bug 60013, 56758
	'-wmgRC2UDPPrefix' => array(
		'default' => false,
	),

	'wmgUseWebFonts' => array(
		'mywiki' => true,
	),

	'wgLogo' => array(
	//	'commonswiki' => '//commons.wikimedia.beta.wmflabs.org/w/thumb.php?f=Wiki.png&width=88&a',
	),

	// Editor Engagement stuff
	'-wmfUseArticleCreationWorkflow' => array(
		'default' => false,
	),
	'wmgUseEcho' => array(
		'enwiki' => true,
	),

	'-wmgUsePoolCounter' => array(
		'default' => false, # bug 36891
	),
	'-wmgUseAPIRequestLog' => array(
		'default' => false,
	),
	'-wmgEnableCaptcha' => array(
		'default' => true,
	),
	// Completely disabled AFTv4 & enable AFTv5
	'-wmgArticleFeedbackLotteryOdds' => array(
		'default' => 0,
	),
	'-wmgArticleFeedbackv5LotteryOdds' => array(
		'default' => 100,
	),
	'-wmgArticleFeedbackv5Namespaces' => array(
		'default' => array( NS_MAIN ),
		'dewiki' => array( NS_MAIN ),
		'enwiki' => array( NS_MAIN, NS_HELP, NS_PROJECT ),
		'testwiki' => array( NS_MAIN, NS_HELP, NS_PROJECT ),
	),
	// On labs, there's no dedicated cluster for AFTv5's data; false will default to main MW db
	'-wmgArticleFeedbackv5Cluster' => array(
		'default' => false,
	),
	'-wmgEchoCluster' => array(
		'default' => false,
	),
	# FIXME: make that settings to be applied
	'-wgShowExceptionDetails' => array(
		'default' => true,
	),
	'-wgUseContributionTracking' => array(
		'default' => false,
	),
	'-wmgUseContributionReporting' => array(
		'default' => false,
	),

	# To help fight spam, makes rules maintained on labswiki
	# to be available on all beta wikis.
	'-wmgAbuseFilterCentralDB' => array(
		'default' => 'labswiki',
	),
	'-wmgUseGlobalAbuseFilters' => array(
		'default' => true,
	),

	# Bug 37852
	'wmgUseWikimediaShopLink' => array(
		'default'    => false,
		'enwiki'     => true,
		'simplewiki' => true,
	),

	//enable TimedMediaHandler and MwEmbedSupport for testing on commons and enwiki
	'wmgUseMwEmbedSupport' => array(
		'commonswiki'	=> true,
		'enwiki'	=> true,
	),
	// NOTE: TMH *requires* MwEmbedSupport to function
	'wmgUseTimedMediaHandler' => array(
		'commonswiki'	=> true,
		'enwiki'	=> true,
	),
	'wmgMobileUrlTemplate' => array(
		'default' => '%h0.m.%h1.%h2.%h3.%h4',
		'commonswiki' => '',
		'mediawikiwiki' => '',//'m.%h1.%h2',
	),
//	'wmgZeroRatedMobileAccess' => array(
//		'default' => false,
//	),
	'wmgMFPhotoUploadEndpoint' => array(
		'default' => '//commons.wikimedia.beta.wmflabs.org/w/api.php',
	),
	'wmgMFUseCentralAuthToken' => array(
		'default' => true,
	),
	'wmgMFEnableBetaDiff' => array(
		'default' => true,
	),

	'wmgEnableGeoData' => array(
		'default' => true,
	),

	# ULS predeployment testing 2013-05
	'wmgUseUniversalLanguageSelector' => array(
		'default' => true,
	),

	'wmgULSPosition' => array(
		# Beta-specific
		'ee-prototype' => 'personal',
		'labswiki' => 'personal',
	),

	// (bug 39653) The plan is to enable it for testing on labs first, so add
	// the config hook to be able to do that.
	'wmgUseCodeEditorForCore' => array(
		'default' => true,
	),

	'wmgUseBetaFeatures' => array(
		'default' => true,
	),

	'wmgUseCommonsMetadata' => array(
		'default' => true,
	),

	'wmgUseGWToolset' => array(
		'default' => false,
		'commonswiki' => true,
	),

	// Don't use an http/https proxy
	'-wgCopyUploadProxy' => array(
		'default' => false,
	),

	'wmgUseMultimediaViewer' => array(
		'default' => true,
	),

	'wmgUseVectorBeta' => array(
		'default' => true,
	),

	'wmgVisualEditorExperimental' => array(
		'default' => true,
	),

	'wmgUseCampaigns' => array(
		'default' => true,
	),

	'wmgUseEventLogging' => array(
	    'default' => true,
	),

	'wmgUseNavigationTiming' => array(
	    'default' => true,
	),

	'wgSecureLogin' => array(
		// Setting false throughout Labs for now due to untrusted SSL certificate
		// bug 48501
		'default' => false,
		'loginwiki' => false,
	),

	'wgSearchSuggestCacheExpiry' => array(
		'default' => 1800,
	),

	'wmgUseCirrus' => array(
		'default' => true,
	),

	'wmgUseFlow' => array(
		'enwiki' => true,
	),
	# Extension:Flow's browsertests use Talk:Flow_QA.
	'wmgFlowOccupyPages' => array(
		'enwiki' => array( 'Talk:Flow QA', 'Talk:Flow' ),
	),
	# No separate Flow DB or cluster (yet) for labs.
	'-wmgFlowDefaultWikiDb' => array(
		'default' => false,
	),
	'-wmgFlowCluster' => array(
		'default' => false,
	),

	'wmgBug54847' => array(
		'default' => false,
	),

);

} # wmflLabsSettings()
