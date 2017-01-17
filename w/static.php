<?php
/**
 * Serve static files in a multiversion-friendly way.
 *
 * See https://phabricator.wikimedia.org/T99096 for design requirements.
 *
 * Overview:
 *
 * - multiversion requires the MediaWiki script directory (/w) to be shared across
 *   all domains. Files in /w are generic and load the real MediaWiki entry point
 *   from the current wiki's MediaWiki version (based on request host name).
 * - MediaWiki configuration sets $wgResourceBasePath to "/w".
 * - Apache configuration rewrites "/w/skins/*", "/w/resources/*", and "/w/extension/*"
 *   to /w/static.php (this file).
 * - static.php streams the file from the appropiate MediaWiki branch directory.
 * - For performance, Varnish caches responses from static.php in a hostname-agnostic
 *   way if a hexidecimal query string was set. (E.g. verifiable hash.)
 *   Therefore static.php MUST respond in a deterministic way for those requests
 *   regardless of which wiki made the request. (Compliance is enforced via VCL by
 *   hardcoding 'en.wikipedia.org' for these requesrs, per static_host config.)
 *
 * In addition to the above, this file also looks in older MediaWiki branch
 * directories in order to support references from our static HTML cache for 30 days.
 * While responses from static may also be cached, they are not linked or guruanteed.
 * As such, this file must be able to respond to requests for older resources as well.
 *
 * StatD metrics:
 *
 * - wmfstatic.success.<responseType (nohash, verified, unknown)>
 * - wmfstatic.notfound
 * - wmfstatic.mismatch
 */
define( 'MW_NO_SESSION', 'warn' );
require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php' );

function wmfStaticShowError( $message, $status, $smaxage = 60 ) {
	HttpStatus::header( $status );
	header(
		'Cache-Control: ' .
		's-maxage=' . (int)$smaxage . ', must-revalidate, max-age=0'
	);
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo "$message\n";
}

/**
 * Stream file from disk to web response
 * Based on StreamFile::stream()
 * @param string $filePath
 * @param string $responseType Cache control for successful repsonse (one of 'short' or 'long')
 */
function wmfStaticStreamFile( $filePath, $responseType = 'nohash' ) {
	$ctype = StreamFile::contentTypeFromPath( $filePath, /* safe: not for upload */ false );
	if ( !$ctype || $ctype === 'unknown/unknown' ) {
		// Directory, extension-less file or unknown extension
		wmfStaticShowError( 'Invalid file type', 400 );
		return;
	}

	$stat = stat( $filePath );
	if ( !$stat ) {
		wmfStaticShowError( 'Unknown file path', 404, 300 );
		return;
	}

	// Match puppet:///mediawiki/apache/expires.conf
	if ( preg_match( '/\.(gif|jpe?g|png|css|js|json|woff|woff2|svg|eot|ttf|ico)$/', $filePath ) ) {
		header( 'Access-Control-Allow-Origin: *' );
	}
	header( 'Last-Modified: ' . wfTimestamp( TS_RFC2822, $stat['mtime'] ) );
	header( "Content-Type: $ctype" );
	if ( $responseType === 'nohash' ) {
		// 5 min (5 * 50) on proxy servers and 24 hours (24 * 3600) on clients
		header( 'Cache-Control: public, s-maxage=300, must-revalidate, max-age=86400' );
	} else {
		// Response type "verified" or "unknown"
		// 1 year (365 * 24 * 3600) on proxy servers and clients
		header( 'Cache-Control: public, s-maxage=31536000, max-age=31536000' );
	}

	if ( !empty( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) {
		$ims = preg_replace( '/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE'] );
		if ( wfTimestamp( TS_UNIX, $stat['mtime'] ) <= strtotime( $ims ) ) {
			ini_set( 'zlib.output_compression', 0 );
			header( 'HTTP/1.1 304 Not Modified' );
			return;
		}
	}

	header( 'Content-Length: ' . $stat['size'] );
	readfile( $filePath );
}

function wmfStaticRespond() {
	global $wgScriptPath, $IP;

	if ( !isset( $_SERVER['REQUEST_URI'] ) || !isset( $_SERVER['SCRIPT_NAME'] ) ) {
		wmfStaticShowError( 'Invalid request', 500 );
		return;
	}

	// Ignore direct request (eg. "/w/static.php" or "/w/static.php/test")
	// (use strpos instead of equal to ignore pathinfo and query string)
	if ( strpos( $_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'] ) === 0 ) {
		wmfStaticShowError( 'Invalid request', 400 );
		return;
	}

	// Strip query parameters
	$uriPath = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

	// Strip prefix
	$urlPrefix = $wgScriptPath;
	if ( strpos( $uriPath, $urlPrefix ) !== 0 ) {
		wmfStaticShowError( 'Bad request', 400 );
		return;
	}
	$path = substr( $uriPath, strlen( $urlPrefix ) );

	// Validation hash
	$urlHash = isset( $_SERVER['QUERY_STRING'] ) ? $_SERVER['QUERY_STRING'] : false;
	$fallback = false;
	$responseType = 'nohash';

	// Get branch dirs and sort with newest first
	$branchDirs = MWWikiversions::getAvailableBranchDirs();
	usort( $branchDirs, function ( $a, $b ) {
		return version_compare( $b, $a );
	} );

	// If request has no or invalid verification hash, prefer the current wikiversion
	// Note we can't do this for a matching verification hash because varnish will
	// have already sent us to the static host instead of the individual wiki.
	$validHash = $urlHash ? preg_match( '/^[a-fA-F0-9]+$/', $urlHash ) : false;
	if ( !$validHash ) {
		array_unshift( $branchDirs, $IP );
	}

	$stats = RequestContext::getMain()->getStats();

	// Try each version in descending order
	// - Requests without a validation hash will get the latest version.
	//   (If the file no longer exists in the latest version, it will correctly
	//   fall back to the last available version.)
	// - Requests with validation hash get the first match. If none found, falls back to the last
	//   available version. Cache expiry is shorted in that case to allow eventual-consistency and
	//   avoids cache poisoning (see T47877).
	foreach ( $branchDirs as $branchDir ) {
		// Use realpath() to prevent path escalation through e.g. "../"
		$filePath = realpath( "$branchDir/$path" );
		if ( !$filePath ) {
			continue;
		}

		if ( strpos( $filePath, $branchDir ) !== 0 ) {
			wmfStaticShowError( 'Bad request', 400 );
			return;
		}

		if ( $urlHash ) {
			if ( !$validHash || strlen( $urlHash ) !== 5 ) {
				// Garbage query string. Give same response as for requests with
				// no validation hash (nohash), except with a longer max-age.
				//
				// This prevents extra backend hits from unexpected random strings,
				// and also keeps expected behavior for extensions using libraries
				// with their own versioned cache-buster query strings.
				$responseType = 'unknown';
			} else {
				// Set fallback to the newest existing version.
				if ( !$fallback ) {
					$fallback = $branchDir;
				}

				// Match OutputPage::transformFilePath()
				$fileHash = substr( md5_file( $filePath ), 0, 5 );
				if ( $fileHash !== $urlHash ) {
					// Hash mismatch, continue search in older branches
					continue;
				}
				// Cache hash-validated responses for long
				$responseType = 'verified';
			}
		}

		wmfStaticStreamFile( $filePath, $responseType );
		$stats->increment( "wmfstatic.success.$responseType" );
		return;
	}

	if ( !$fallback ) {
		wmfStaticShowError( 'Unknown file path', 404, 300 );
		$stats->increment( 'wmfstatic.notfound' );
		return;
	}

	wmfStaticStreamFile( "$fallback/$path", $responseType );
	$stats->increment( 'wmfstatic.mismatch' );
	return;
}

wfResetOutputBuffers();
wmfStaticRespond();

$mediawiki = new MediaWiki();
$mediawiki->doPostOutputShutdown( 'fast' );
