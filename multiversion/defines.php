<?php
/**
 * Define installation specific MWMultiVersion values like paths
 */
define( 'MEDIAWIKI_STAGING_DIR', '/srv/mediawiki-staging' );
if ( !defined( 'MEDIAWIKI_DEPLOYMENT_DIR' ) ) {
	define( 'MEDIAWIKI_DEPLOYMENT_DIR', '/srv/mediawiki' );
}
