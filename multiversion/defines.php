<?php
/**
 * Define installation specific MWMultiVersion values like paths
 */

if ( getenv( 'MW_CONFIG_TESTING' ) ) {
	# invoked via tests/bootstrap.php, act differently

	define( 'MW_CONFIG_TESTING', getenv( 'MW_CONFIG_TESTING' ) );

	define( 'MULTIVER_COMMON_HOME', MW_CONFIG_TESTING );
	define( 'MULTIVER_COMMON_APACHE', MW_CONFIG_TESTING );

	define( 'MULTIVER_CDB_DIR_HOME', MW_CONFIG_TESTING );
	define( 'MULTIVER_CDB_DIR_APACHE', MW_CONFIG_TESTING );

	define( 'MULTIVER_404SCRIPT_PATH_HOME', MW_CONFIG_TESTING . '/wmf-config/missing.php' );
	define( 'MULTIVER_404SCRIPT_PATH_APACHE', MW_CONFIG_TESTING . '/wmf-config/missing.php' );
} else {
	define( 'MULTIVER_COMMON_HOME', '/a/common' );
	define( 'MULTIVER_COMMON_APACHE', '/usr/local/apache/common-local' );

	define( 'MULTIVER_CDB_DIR_HOME', '/a/common' );
	define( 'MULTIVER_CDB_DIR_APACHE', '/usr/local/apache/common-local' );

	define( 'MULTIVER_404SCRIPT_PATH_HOME', '/a/common/wmf-config/missing.php' );
	define( 'MULTIVER_404SCRIPT_PATH_APACHE', '/usr/local/apache/common-local/wmf-config/missing.php' );
}
