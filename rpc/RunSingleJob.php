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
 * @author Aaron Schulz
 * @author Marko Obrovac
 */
if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
	http_response_code( 405 );
	header( 'Allow: POST' );
	die( "Request must use POST.\n" );
}

// get the info contained in the body
$event = null;
try {
	// we do not need to use FormatJson::decode because it only wraps
	// json_decode() and we haven't loaded any MW components at this
	// point yet
	$event = json_decode( file_get_contents( "php://input" ), true );
} catch ( Exception $e ) {
	http_response_code( 500 );
	die( $e );
}

// check that we have the needed components of the event
if ( !isset( $event['database'] )
		|| !isset( $event['type'] )
		|| !isset( $event['page_title'] )
		|| !isset( $event['params'] )
) {
	http_response_code( 400 );
	die( 'Invalid event received!' );
}

define( 'MEDIAWIKI_JOB_RUNNER', 1 );

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php', $event['database'] );

error_reporting( E_ERROR ); // fatals but not random I/O warnings
ini_set( 'display_errors', 1 );
$wgShowExceptionDetails = true;

// Session consistency is not helpful here and will slow things down in some cases
$lbFactory = MediaWiki\MediaWikiServices::getInstance()->getDBLoadBalancerFactory();
$lbFactory->disableChronologyProtection();

try {
	$mediawiki = new MediaWiki();
	$executor = new JobExecutor();
	// execute the job
	$response = $executor->execute( $event );
	if ( $response['status'] === true ) {
		http_response_code( 200 );
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
			header( 'X-Readonly: true' );
		}
		http_response_code( 500 );
	}
	$mediawiki->restInPeace();
} catch ( Exception $e ) {
	# Since output is logged to file, get MediaWiki to generate a raw error
	$wgCommandLineMode = true;

	http_response_code( 500 );
	MWExceptionHandler::handleException( $e );
}
