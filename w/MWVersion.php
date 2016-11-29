<?php

# MW_SECURE_HOST set from secure gateway?
$secure = getenv( 'MW_SECURE_HOST' );
$host = $secure ?: $_SERVER['HTTP_HOST'];

require_once '/srv/mediawiki/multiversion/MWVersion.php';
