<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This for PRODUCTION.
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/CommonSettings.php
# - wmf-config/wgConf.php [THIS FILE]
#
# Included from: wmf-config/CommonSettings.php.

$wgConf = new SiteConfiguration;

# Read wiki lists

$wgConf->suffixes = MWMultiVersion::SUFFIXES;

$dbList = $wmfRealm === 'labs' ? 'all-labs' : 'all';
$wgConf->wikis = MWWikiversions::readDbListFile( $dbList );

$wgLocalDatabases =& $wgConf->getLocalDatabases();
