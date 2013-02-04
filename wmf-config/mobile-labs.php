<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

$wmgMFRemotePostFeedbackUsername = '';
$wmgMFRemotePostFeedbackPassword = '';

// Reuse most of production settings
require_once( __DIR__ . '/mobile.php' );

// Disabled on deployment-prep until there's EventLogging up and running
$wgMFLogEvents = false;
