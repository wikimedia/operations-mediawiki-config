<?php

require_once( '/srv/mediawiki/multiversion/activeMWVersions.php' );
echo implode( ' ', getActiveWikiVersions() ) . "\n";
