<?php
/**
 * Define installation specific MWMultiVersion values like paths
 */
define( 'MEDIAWIKI_STAGING_DIR', '/srv/mediawiki-staging' );
define( 'MEDIAWIKI_DEPLOYMENT_DIR', '/srv/mediawiki' );
define( 'MEDIAWIKI_VERSION_REGEX', '(\d+\.\d+(\.\d+-)?wmf\.?\d+|master)');
define( 'MEDIAWIKI_DIRECTORY_REGEX', '/^php-'.MEDIAWIKI_VERSION_REGEX.'$/');