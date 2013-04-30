<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

// Reuse most of production settings
require_once( __DIR__ . '/mobile.php' );

$wgMFForceSecureLogin = false;
$wgMFLoginHandshakeUrl = 'http://commons.wikimedia.beta.wmflabs.org/wiki/Special:LoginHandshake?useformat=mobile';
