<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

if ( PHP_SAPI !== 'cli' ) {
	die( 'This script can only be run on the command line.' );
}

$input = file_get_contents( "php://stdin" );
if ( $input === '' ) {
	die( 'No event received.' );
}

$event = json_decode( $input, true );
// check that we have the needed components of the event
if ( !isset( $event['database'] ) ) {
	throw new Exception( 'Invalid event received! ' . json_encode( $event ) );
}

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php', $event['database'] );

// fatals but not random I/O warnings
error_reporting( E_ERROR );
ini_set( 'display_errors', 1 );
$wgShowExceptionDetails = true;

// Session consistency is not helpful here and will slow things down in some cases
$chronologyProtector = MediaWiki\MediaWikiServices::getInstance()->getChronologyProtector();
$chronologyProtector->setEnabled( false );

$success = false;

try {
	$mediawiki = new MediaWiki();
	$executor = new MediaWiki\Extension\EventBus\JobExecutor();
	$response = $executor->execute( $event );
	if ( $response['status'] === true ) {
		$success = true;
	} else {
		if ( $response['readonly'] ) {
			// TODO - T204154
			// if we detect that the DB is in read-only mode, we delay the return of the
			// response by at most 45 seconds in order to minimise the number of requests
			// made by change-prop; this will keep the request rate at a reasonably low
			// level without causing request time outs
			// NOTE: this is currently only a work-around, a proper solution is needed
			sleep( rand( 40, 45 ) );
			// END TODO
		}
		fwrite( STDERR, "Failed to execute job on {$event['database']}: {$response['message']}" );
	}
	$mediawiki->restInPeace();
} catch ( Exception $e ) {
	$exc_msg = $e->getMessage();
	fwrite( STDERR, "Caught exception while handling event for {$event['database']}: $exc_msg" );
	MWExceptionHandler::rollbackPrimaryChangesAndLog( $e );
}

if ( $success ) {
	exit( 0 );
} else {
	exit( 1 );
}
