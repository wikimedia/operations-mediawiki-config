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
		'incubatorwiki'	=> '//incubator.wikimedia.beta.wmflabs.org',
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
		'testwiki'      => '//test.wikimedia.beta.wmflabs.org',
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

		'incubatorwiki'	=> 'http://incubator.wikimedia.beta.wmflabs.org',
		'metawiki'      => 'http://meta.wikimedia.beta.wmflabs.org',
		'ee_prototypewiki' => 'http://ee-prototype.wikipedia.beta.wmflabs.org',
		'commonswiki'	=> 'http://commons.wikimedia.beta.wmflabs.org',
		'labswiki'      => 'http://deployment.wikimedia.beta.wmflabs.org',
		'testwiki'      => 'http://test.wikimedia.beta.wmflabs.org',
	),

	'wmgUsabilityPrefSwitch' => array(
		'default' => ''
	),

	'wmgUseLiquidThreads' => array(
		'testwiki' => true,
	),

	'-wgUploadDirectory' => array(
		'default'      => '/mnt/upload7/$site/$lang',
	),

	/* 'wmgUseOnlineStatusBar' => array( */
	/* 	'default' => false, */
	/* ), */

	'-wgThumbnailScriptPath' => array(
		'default' => '/w/thumb.php',
	),

	'-wgUploadPath' => array(
		'default' => '//upload.beta.wmflabs.org/$site/$lang',
	//	'private' => '/w/img_auth.php',
	//	'wikimania2005wiki' => '//upload..org/wikipedia/wikimania', // back compat
		'commonswiki' => '//upload.beta.wmflabs.org/wikipedia/commons',
		'metawiki' => '//upload.beta.wmflabs.org/wikipedia/meta',
		'testwiki' => '//upload.beta.wmflabs.org/wikipedia/test',
	),

	'-wgMathDirectory' => array(
		'default' => '/mnt/upload7/math',
	),

	'-wgMathPath' => array(
		'default' => '//upload.beta.wmflabs.org/math',
	),

	'wmgNoticeProject' => array(
		'labswiki' => 'meta',
	),

	//'-wgDebugLogGroups' => array(),
	'-wgRateLimitLog' => array(),
	'-wgJobLogFile' => array(),

	'-wgRC2UDPAddress' => array(
		'default' => false,
	),

	'wmgUseWebFonts' => array(
		'mywiki' => true,
        ),

        'wgLogo' => array(
//		'commonswiki'       => '//commons.wikimedia.beta.wmflabs.org/w/thumb.php?f=Wiki.png&width=88&a',
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
	# ClickTracking is a required dependency for AFTv5
	'-wmgClickTracking' => array(
		'default' => true,
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
	'wmgMobileFrontend' => array(
		'default' => true,
	),
	'wmgMobileFrontendLogo' => array(
		'default' => '//upload.wikimedia.org/wikipedia/commons/8/84/W_logo_for_Mobile_Frontend.gif',
		'wikinews' => '//upload.wikimedia.org/wikipedia/commons/thumb/2/24/Wikinews-logo.svg/35px-Wikinews-logo.svg.png',
		'wiktionary' => '//upload.wikimedia.org/wikipedia/commons/thumb/b/b4/Wiktionary-logo-en.png/35px-Wiktionary-logo-en.png',
		'wikibooks' => '//upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Wikibooks-logo.svg/35px-Wikibooks-logo.svg.png',
		'wikiversity' => '//upload.wikimedia.org/wikipedia/commons/thumb/9/91/Wikiversity-logo.svg/35px-Wikiversity-logo.svg.png',
		'specieswiki' => '//upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Wikispecies-logo.png/35px-Wikispecies-logo.png',
		'commonswiki' => '//upload.wikimedia.org/wikipedia/commons/4/42/22x35px-Commons-logo.png',
		'wikiquote' => '//upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Wikiquote-logo.svg/35px-Wikiquote-logo.svg.png',
		'metawiki' => '//upload.wikimedia.org/wikipedia/commons/2/23/22x35px-Wikimedia_Community_Logo.png',
		'foundationwiki' => '//upload.wikimedia.org/wikipedia/commons/8/81/35x22px-Wikimedia-logo.png',
		'wikisource' => '//upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Wikisource-logo.svg/35px-Wikisource-logo.svg.png',
	),
	'wmgMobileUrlTemplate' => array(
		'default' => '',//'%h0.m.%h1.%h2',
		'foundationwiki' => '',
		'commonswiki' => '',
		'mediawikiwiki' => '',//'m.%h1.%h2',
	),
	'wmgMFRemovableClasses' => array(
		'default' => array( 'table.metadata',
				   '.metadata mbox-small',
				   '.metadata plainlinks ambox ambox-content',
				   '.metadata plainlinks ambox ambox-move',
				   '.metadata plainlinks ambox ambox-style', ),
	),
	'wmgMFCustomLogos' => array(
		'default' => array(
			'site' => 'wikipedia',
			'logo' => '//upload.wikimedia.org/wikipedia/commons/5/54/Mobile_W_beta_light.png'
		),	
		'enwiki' => array(
			'site' => 'wikipedia',
			'logo' => '//upload.wikimedia.org/wikipedia/commons/5/54/Mobile_W_beta_light.png',
			'copyright' => '{wgExtensionAssetsPath}/MobileFrontend/stylesheets/common/images/logo-copyright-en.png'
		),
	),
	'testwiki' => array(
		'site' => 'wikipedia',
		'logo' => '//upload.wikimedia.org/wikipedia/commons/5/54/Mobile_W_beta_light.png',
		// {wgExtensionAssetsPath} will get replaced with $wgExtensionAssetsPath in CustomSettings.php
		'copyright' => '{wgExtensionAssetsPath}/MobileFrontend/stylesheets/common/images/logo-copyright-en.png'
	),
	'wmgMFEnableDesktopResources' => array(
		'default' => true,
	),
	#'wmgMFPhotoUploadEndpoint' => array(
	#	'default' => '//commons.wikimedia.org/w/api.php',
	#),
	'wmgMFForceSecureLogin' => array(
		'default' => true,
	),
);

} # wmflLabsSettings()
