<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

require_once "$IP/extensions/LiquidThreads/LiquidThreads.php";
$extName = 'LiquidThreads';

$wgLiquidThreadsExtensionName = $extName;
$wgLiquidThreadsExtensionPath = "{$wgExtensionAssetsPath}/{$extName}";

if ( $wmgLiquidThreadsFrozen ) {
	// Preserve access to LQT edits and logs after converting all LQT, but prevent
	// LQT pages from being created.
	$wgLqtTalkPages = false;
	$wgLiquidThreadsAllowUserControl = false;
	$wgLiquidThreadsAllowUserControlNamespaces = [];
	$wgLiquidThreadsAllowEmbedding = false;
	$wgLiquidThreadsEnableNewMessages = false;
} else {
	if ( $wmgLiquidThreadsOptIn ) {
		$wgLqtTalkPages = false;
	}

	if ( isset( $wmgLQTUserControlNamespaces ) ) {
		$wgLiquidThreadsAllowUserControlNamespaces = $wmgLQTUserControlNamespaces;
	}
}
