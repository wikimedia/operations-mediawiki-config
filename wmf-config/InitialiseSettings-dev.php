<?php

/**
 * Get overrides for train-dev. This is used by
 * wmfLoadInitialiseSettings() in CommonSettings.php.
 *
 * Keys that start with a hyphen will completely override the prodution settings
 * from InitializeSettings.php.
 *
 * Keys that don't start with a hyphen will have their settings merged with
 * the production settings.
 *
 * @return array
 */
function wmfGetOverrideSettings() {
	return [
		'-wmgUsePageViewInfo' => [
			'default' => false,
		],
		'-wmgUseIPInfo' => [
			'default' => false,
		],
		'-wmgUseUrlShortener' => [
			'default' => false,
		],
		'-wmgUseCampaignEvents' => [
			'default' => false,
		],
		'-wmgUseReadingLists' => [
			'default' => false,
		],
		'-wmgUseCognate' => [
			'default' => false,
		],
		'-wmgUseBounceHandler' => [
			'default' => false,
		],
		'-wmgUseCentralAuth' => [
			'default' => false,
		],
		'-wgParserCacheType' => [
			'default' => 'memcached-pecl',
		],
		'-wmgUseMediaModeration' => [
			'default' => false,
		],
		'-wmgUseThanks' => [
			'default' => false,
		],
		'-wmgUseEcho' => [
			'default' => false,
		],
		'-wmgUseGlobalBlocking' => [
			'default' => false,
		],
		'-wmgUseFlow' => [
			'default' => false,
		],
		'-wmgFlowCluster' => [
			'default' => false,
		],
		'-wmgFlowDefaultWikiDb' => [
			'default' => false,
		],
		'-wmgUseWikibaseRepo' => [
			'default' => false,
		],
		'-wmgUseWikibaseClient' => [
			'default' => false,
		],
		'-wmgUseWikibaseMediaInfo' => [
			'default' => false,
		],
		'-wmgUseTranslate' => [
			'default' => false,
		],
		'-wmgUseTranslationNotifications' => [
			'default' => false,
		],
		'-wmgUseFundraisingTranslateWorkflow' => [
			'default' => false,
		],
		'-wgGEDatabaseCluster' => [
			'default' => false,
		],
		'-wmgUseMediaSearch' => [
			'default' => false,
		],
		'-wmgUseScore' => [
			'default' => false,
		],
		'-wmgUseDynamicSidebar' => [
			'default' => false,
		],
		'-wgShowExceptionDetails' => [
			'default' => true,
		],
		'-wmgUseEntitySchema' => [
			'default' => false,
		],
		'-wmgUseCheckUser' => [
			'default' => false,
		],
	];
}
