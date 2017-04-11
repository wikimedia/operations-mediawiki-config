<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

require_once( "$IP/extensions/LiquidThreads/LiquidThreads.php" );
$extName = 'LiquidThreads';

$wgLiquidThreadsExtensionName = $extName;
$wgLiquidThreadsExtensionPath = "{$wgExtensionAssetsPath}/{$extName}";

if ( $wgWMFLiquidThreadsFrozen ) {
	// Preserve access to LQT edits and logs after converting all LQT, but prevent
	// LQT pages from being created.
	$wgLqtTalkPages = false;
	$wgLiquidThreadsAllowUserControl = false;
	$wgLiquidThreadsAllowUserControlNamespaces = [];
	$wgLiquidThreadsAllowEmbedding = false;
	$wgLiquidThreadsEnableNewMessages = false;
} else {
	if ( $wgWMFLiquidThreadsOptIn ) {
		$wgLqtTalkPages = false;
	}

	if ( isset( $wgWMFLQTUserControlNamespaces ) ) {
		$wgLiquidThreadsAllowUserControlNamespaces = $wgWMFLQTUserControlNamespaces;
	}
}