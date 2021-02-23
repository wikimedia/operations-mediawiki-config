#!/usr/bin/env php
<?php
require_once dirname( __DIR__ ) . '/MWWikiversions.php';

$jsonPath = $argv[1] ?? 'wikiversions.json';
$phpPath = $argv[2] ?? 'wikiversions.php';

MWWikiversions::writePHPWikiVersionsFile(
	$phpPath,
	MWWikiversions::readWikiVersionsFile( $jsonPath )
);
