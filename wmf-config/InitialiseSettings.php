<?php
/* vim: set sw=4 ts=4 noet foldmarker=@{,@} foldmethod=marker: */

# WARNING: This file is publicly viewable on the web. Do not put private data here.

# InitialiseSettings.php loads wiki-specific configuration for the WMF cluster.
# Actual configuration is performed statically in VariantSettings.php.
# For configuration shared by all wikis, see CommonSettings.php.
#
# This for PRODUCTION.
#
# Usage:
# - Settings prefixed with 'wg' are standard MediaWiki configuration
#   variables.
# - Settings prefixed with 'wmg' are custom parameters handled by
#   CommonSettings.php.
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/*Services.php
# - wmf-config/InitialiseSettings.php [THIS FILE]
#    - wmf-config/VariantSettings.php
# - wmf-config/CommonSettings.php
#
# Included from: wmf-config/CommonSettings.php.

global $wmfRealm, $wgConf;

require_once __DIR__ . '/VariantSettings.php';

$settings = wmfGetVariantSettings();

### WMF Labs override #####
if ( $wmfRealm == 'labs' ) {
	require_once __DIR__ . '/InitialiseSettings-labs.php';
	$settings = wmfApplyLabsOverrideSettings( $settings );
}

$wgConf->settings = $settings;
unset( $settings );
