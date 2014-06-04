<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

// Reuse most of production settings
require_once( __DIR__ . '/mobile.php' );

$wgMFForceSecureLogin = false;
$wgMFUseCentralAuthToken = $wmgMFUseCentralAuthToken;

// Zero extension
$wgEnableZeroRatedMobileAccessTesting = true;  // Delete once ZRMA extension is removed
$wgZeroEnableTesting = true;


// Keep Going experiments
$wgMFKeepGoing = true;
