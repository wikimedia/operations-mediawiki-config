<?php
require_once __DIR__ . '/../../../src/Noc/utils.php';
header( "Content-type: text/plain" );
echo implode( ', ', Wikimedia\MWConfig\Noc\getWikiVersions( true ) );
