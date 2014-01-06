<?php
/**
 * Define installation specific MWMultiVersion values like paths
 */

if ( getenv( 'MULTIVER_TEST_PATH' ) !== false ) {
	# Hack for Wikimedia Jenkins jobs
	define( 'MULTIVER_COMMON_HOME', getenv( 'MULTIVER_TEST_PATH' ) );
	define( 'MULTIVER_COMMON_APACHE', MULTIVER_COMMON_HOME );

	define( 'MULTIVER_CDB_DIR_HOME', getenv( 'MULTIVER_TEST_PATH' ) );
	define( 'MULTIVER_CDB_DIR_APACHE', MULTIVER_CDB_DIR_HOME );

	define( 'MULTIVER_404SCRIPT_PATH_HOME', getenv( 'MULTIVER_TEST_PATH' ) . '/wmf-config/missing.php' );
	define( 'MULTIVER_404SCRIPT_PATH_APACHE', MULTIVER_404SCRIPT_PATH_HOME );
} else {
	define( 'MULTIVER_COMMON_HOME', '/a/common' );
	define( 'MULTIVER_COMMON_APACHE', '/usr/local/apache/common-local' );

	define( 'MULTIVER_CDB_DIR_HOME', '/a/common' );
	define( 'MULTIVER_CDB_DIR_APACHE', '/usr/local/apache/common-local' );

	define( 'MULTIVER_404SCRIPT_PATH_HOME', '/a/common/wmf-config/missing.php' );
	define( 'MULTIVER_404SCRIPT_PATH_APACHE', '/usr/local/apache/common-local/wmf-config/missing.php' );
}
