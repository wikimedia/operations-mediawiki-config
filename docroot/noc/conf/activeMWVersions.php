<?php

require_once( '/usr/local/apache/common-local/multiversion/activeMWVersions.php' );
echo implode( ' ', getActiveWikiVersions() ) . "\n";
