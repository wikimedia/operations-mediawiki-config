<?php

// phpcs:ignore MediaWiki.Usage.ForbiddenFunctions.exec
echo str_replace( ' ', ', ', exec( '/usr/bin/scap wikiversions-inuse' ) );
