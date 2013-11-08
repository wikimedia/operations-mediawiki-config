<?php
### Determine realm and datacenter we are on #############################
# $wmgRealm and $wmgDatacenter are used to vary configuration based on server
# location. Thet should be provisioned by puppet in /etc/wikimedia-site and
# /etc/wikimedia-realm.
#
# The possible values of $wmgRealm and $wmgDatacenter as of October 2012 are:
#  - labs + pmtpa
#  - labs + eqiad
#  - production + pmtpa
#  - production + eqiad
global $wmgDatacenter, $wmgRealm;
$wmgRealm   = 'production';
$wmgDatacenter = 'pmtpa';

# Puppet provision the realm in /etc/wikimedia-realm
if( file_exists( '/etc/wikimedia-realm' ) ) {
	$wmgRealm = trim( file_get_contents( '/etc/wikimedia-realm' ) );
}
if( file_exists( '/etc/wikimedia-site' ) ) {
	$wmgDatacenter = trim( file_get_contents( '/etc/wikimedia-site' ) );
}

# Validate settings
switch( $wmgRealm ) {
case 'labs':
case 'production':
	if ( ! in_array( $wmgDatacenter, array( 'pmtpa', 'eqiad' ) ) ) {
		$wmgDatacenter = 'pmtpa';
	}
	break;

default:
	# Assume something vaguely resembling a default
	$wmgRealm   = 'production';
	$wmgDatacenter = 'pmtpa';
	break;
}

# Function to list all valid realm/datacenter pairs, for testing purposes.
# @returns array List of realm-datacenter pairs
function listAllRealmsAndDatacenters() {
	return array(
		array( 'production', 'pmtpa' ),
		array( 'production', 'eqiad' ),
		array( 'labs', 'pmtpa' ),
		array( 'labs', 'eqiad' ),
	);
}

# Function to get the filename for the current realm/datacenter, falling back
# to the "base" file if not found.
#
# Files checked are:
#   base-realm-datacenter.ext
#   base-realm.ext
#   base-datacenter.ext
#   base.ext
#
# @param $filename string Base filename, must contain an extension
# @returns string Filename to be used
function getRealmSpecificFilename( $filename ) {
	global $wmgRealm, $wmgDatacenter;

	$dotPos = strrpos( $filename, '.' );
	if ( $dotPos === false ) {
		return $filename;
	}
	$base = substr( $filename, 0, $dotPos );
	$ext = substr( $filename, $dotPos );

	# Test existence of the following file suffix and return
	# immediately whenever found:
	#  - {realm}-{datacenter}
	#  - {realm}
	#  - {datacenter}
	#  - {}
	#
	# Please update /README whenever changing code below.

	$new_filename = "{$base}-{$wmgRealm}-{$wmgDatacenter}{$ext}";
	if ( file_exists( $new_filename ) ) {
		return $new_filename;
	}

	# realm take precedence over datacenter.
	$new_filename = "{$base}-{$wmgRealm}{$ext}";
	if ( file_exists( $new_filename ) ) {
		return $new_filename;
	}

	$new_filename = "{$base}-{$wmgDatacenter}{$ext}";
	if ( file_exists( $new_filename ) ) {
		return $new_filename;
	}

	return $filename;
}
### End /Determine realm and datacenter we are on/ ########################
