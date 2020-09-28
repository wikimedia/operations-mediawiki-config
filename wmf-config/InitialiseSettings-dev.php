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
		// This can be removed once I figure out how to prepare the centralauth db.
		'-wmgUseCentralAuth' => [
			'default' => false,
		],
		// Keep things simple
		'-wgParserCacheType' => [
			'default' => 'memcached-pecl',
		],
		'-wmgUseGlobalBlocking' => [
			'default' => false,
		],
		'-wmgUseFlow' => [
			'default' => false,
		],
		// FIXME: Not sure if these other flow settings are necessary given wmfUseFlow being false. Test.
		'-wmgFlowCluster' => [
			'default' => false,
		],
		'-wmgFlowDefaultWikiDb' => [
			'default' => false,
		],
		// Don't wanna deal with wikibase shiz
		'-wmgUseWikibaseRepo' => [
			'default' => false,
		],
		'-wmgUseWikibaseClient' => [
			'default' => false,
		],
		'-wmgUseWikibaseMediaInfo' => [
			'default' => false,
		],
	];
}
