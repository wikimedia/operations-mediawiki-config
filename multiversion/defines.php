<?php
/**
 * Define installation specific MWMultiVersion values like paths
 */
define( 'MEDIAWIKI_STAGING_DIR', '/srv/mediawiki-staging' );
define( 'MEDIAWIKI_DEPLOYMENT_DIR', '/srv/mediawiki' );

// Temporary hack, see T304460
if ( file_exists( '/var/run/php/use-config-schema' ) ) {
	define( 'MW_USE_CONFIG_SCHEMA', 'yes' );
}
