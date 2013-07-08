<?php
/**
 * Define installation specific MWMultiVersion values like paths
 */
define( 'MULTIVER_COMMON_HOME', '/a/common' );
define( 'MULTIVER_COMMON_APACHE', '/usr/local/apache/common-local' );

define( 'MULTIVER_CDB_DIR_HOME', MULTIVER_COMMON_HOME );
define( 'MULTIVER_CDB_DIR_APACHE', MULTIVER_COMMON_APACHE );

define( 'MULTIVER_404SCRIPT_PATH_HOME', MULTIVER_COMMON_HOME . '/wmf-config/missing.php' );
define( 'MULTIVER_404SCRIPT_PATH_APACHE', MULTIVER_COMMON_APACHE . '/wmf-config/missing.php' );
