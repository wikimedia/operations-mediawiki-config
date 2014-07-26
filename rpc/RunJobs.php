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
 */
if ( !in_array( $_SERVER['REMOTE_ADDR'], array( '127.0.0.1', '0:0:0:0:0:0:0:1' ), true ) ) {
	die( "Only loopback requests are allowed.\n" );
}

require_once( '../multiversion/MWVersion.php' );
$wiki = isset( $_POST['wiki'] ) ? $_POST['wiki'] : '';
require getMediaWiki( 'includes/WebStart.php', $wiki );

$wgShowExceptionDetails = true;
$mediawiki = new MediaWiki();

$runner = new JobRunner();
$response = $runner->run( array(
	'type'     => $mediawiki->request()->get( 'type', false ),
	'maxJobs'  => $mediawiki->request()->get( 'maxjobs', 1 ),
	'maxTime'  => $mediawiki->request()->get( 'maxtime', 30 )
) );
print FormatJson::encode( $response, true );

$mediawiki->restInPeace();
