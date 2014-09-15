<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

require_once( "$IP/extensions/LiquidThreads/LiquidThreads.php" );
$extName = 'LiquidThreads';

$wgLiquidThreadsExtensionName = $extName;
$wgLiquidThreadsExtensionPath = "{$wgExtensionAssetsPath}/{$extName}";

if ( $wmgLiquidThreadsOptIn ) {
	$wgLqtTalkPages = false;
}

if ( isset( $wmgLQTUserControlNamespaces ) ) {
	$wgLiquidThreadsAllowUserControlNamespaces = $wmgLQTUserControlNamespaces;
}
