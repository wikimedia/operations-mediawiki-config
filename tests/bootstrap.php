<?php

// Environnement variable used to act differently for testing purposes
putenv( 'MW_CONFIG_TESTING=' . dirname( dirname( __FILE__ ) ) );

require( dirname( __FILE__ ) . '/../multiversion/defines.php' );

// Load the shared utilities classes from here!
require_once( dirname( __FILE__ ) . "/DBList.php" );
require_once( dirname( __FILE__ ) . "/Provide.php" );
